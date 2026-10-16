import { v } from 'convex/values';
import { internalMutation, internalQuery, internalAction } from './_generated/server';
import { internal } from './_generated/api';
import { normalizeManualEntry } from './sources/manualAdapter';
import { runStaticChecks, looksTemplated } from './validation';
import { duplicateScore, DUPLICATE_REVIEW_THRESHOLD } from './duplicates';
import { parseDeadline } from './lib/deadline';
import { checkLinks } from './lib/linkCheck';
import type { NormalizedListing } from './sources/types';

const CHECK_BATCH_LIMIT = 40; // listings link-checked per run
const CHECK_CONCURRENCY = 5;
const RECHECK_PENDING_AFTER_MS = 60 * 60 * 1000; // don't hammer a failing link
const REVALIDATE_VERIFIED_AFTER_MS = 6 * 24 * 60 * 60 * 1000;
const PENDING_GIVE_UP_MS = 14 * 24 * 60 * 60 * 1000;
const DEMOTE_AT = 3; // dead check = 2 points, transient = 1

export type IngestOutcome = 'inserted' | 'updated' | 'unchanged' | 'rejected' | 'duplicate' | 'expired';

const CONTENT_FIELDS = [
  'title', 'organization', 'country', 'level', 'funding', 'field', 'deadline', 'description',
  'eligibility', 'requirements', 'benefits', 'applicationUrl', 'officialSourceUrl', 'isSample',
] as const;

/**
 * Validation + duplicate detection + upsert for one normalized listing.
 * Network checks happen later (verifyPendingListings); a listing enters as
 * "pending" and is never public until its links verify. Re-ingesting the same
 * (sourceName, sourceId) updates the existing row, whatever its state, so
 * repeated runs never accumulate duplicates.
 */
export async function ingestNormalized(ctx: any, input: NormalizedListing) {
  const now = new Date().toISOString();
  const dl = parseDeadline(input.deadline ?? '');
  const listing: NormalizedListing = { ...input, deadline: dl.kind === 'date' ? dl.iso : (input.deadline ?? '').trim() };

  const issues = runStaticChecks(listing);
  const pastOnly = issues.length > 0 && issues.every((i) => i.field === 'deadline' && i.problem.includes('past'));
  const hardIssues = issues.filter((i) => !(i.field === 'deadline' && i.problem.includes('past')));

  // 1. Same source + same upstream id -> update target (rejected rows included).
  let target: any = null;
  if (listing.sourceId) {
    target = await ctx.db
      .query('scholarships')
      .withIndex('by_source', (q: any) => q.eq('sourceName', listing.sourceName).eq('sourceId', listing.sourceId))
      .first();
  }

  if (pastOnly) {
    // An expired listing is never published; if we already hold it, mark it expired.
    if (target && target.status !== 'expired') {
      await ctx.db.patch(target._id, { status: 'expired', deadline: listing.deadline, updatedAt: now });
    }
    return { docId: target?._id ?? null, outcome: 'expired' as IngestOutcome, verificationStatus: target?.verificationStatus ?? 'rejected' };
  }

  // 2. Otherwise look for the same scholarship arriving from another source.
  let duplicateOf: string | null = null;
  if (!target && hardIssues.length === 0) {
    const others = await ctx.db.query('scholarships').filter((q: any) => q.neq(q.field('verificationStatus'), 'rejected')).collect();
    let best: { doc: any; score: number } | null = null;
    for (const doc of others) {
      const score = duplicateScore(doc, listing);
      if (!best || score > best.score) best = { doc, score };
    }
    if (best && best.score >= DUPLICATE_REVIEW_THRESHOLD) {
      if (best.doc.status === 'expired') target = best.doc; // new cycle of a known scholarship: refresh it
      else duplicateOf = best.doc._id;
    }
  }

  let verificationStatus: 'pending' | 'verified' | 'rejected' = 'pending';
  let verificationNotes: string | undefined;
  if (hardIssues.length > 0) {
    verificationStatus = 'rejected';
    verificationNotes = hardIssues.map((i) => `${i.field}: ${i.problem}`).join('; ');
  } else if (looksTemplated(listing)) {
    verificationStatus = 'rejected';
    verificationNotes = 'Automated checks flagged a suspicious templated listing.';
  } else if (duplicateOf) {
    verificationStatus = 'rejected';
    verificationNotes = 'Duplicate of an existing listing.';
  } else if (
    target &&
    target.verificationStatus === 'verified' &&
    target.status === 'active' &&
    target.applicationUrl === listing.applicationUrl &&
    target.officialSourceUrl === listing.officialSourceUrl
  ) {
    verificationStatus = 'verified'; // same links, already verified: don't flap out of public view
    verificationNotes = target.verificationNotes;
  }

  const fields = {
    title: listing.title, organization: listing.organization, country: listing.country,
    level: listing.level, funding: listing.funding, field: listing.field,
    deadline: listing.deadline, description: listing.description,
    eligibility: listing.eligibility ?? [], requirements: listing.requirements ?? [], benefits: listing.benefits ?? [],
    applicationUrl: listing.applicationUrl, officialSourceUrl: listing.officialSourceUrl,
    isSample: listing.isSample === true,
  };

  if (target) {
    const unchanged =
      target.status === 'active' &&
      target.verificationStatus === verificationStatus &&
      target.verificationNotes === verificationNotes &&
      target.possibleDuplicateOf === (duplicateOf ?? undefined) &&
      CONTENT_FIELDS.every((k) => JSON.stringify((target as any)[k] ?? null) === JSON.stringify((fields as any)[k] ?? null));
    if (unchanged) return { docId: target._id, outcome: 'unchanged' as IngestOutcome, verificationStatus };
    await ctx.db.patch(target._id, {
      ...fields,
      sourceName: target.sourceName,
      status: 'active',
      verificationStatus,
      verificationNotes,
      possibleDuplicateOf: duplicateOf ?? undefined,
      // A listing awaiting its link check has no check yet, so it is due immediately.
      lastCheckedAt: verificationStatus === 'pending' ? undefined : (verificationStatus === 'verified' ? target.lastCheckedAt : now),
      updatedAt: now,
    });
    return { docId: target._id, outcome: 'updated' as IngestOutcome, verificationStatus };
  }

  const docId = await ctx.db.insert('scholarships', {
    ...fields,
    status: 'active',
    sourceName: listing.sourceName,
    sourceId: listing.sourceId,
    verificationStatus,
    verificationNotes,
    possibleDuplicateOf: duplicateOf ?? undefined,
    lastCheckedAt: verificationStatus === 'pending' ? undefined : now,
    createdAt: now,
    updatedAt: now,
  });
  const outcome: IngestOutcome = duplicateOf ? 'duplicate' : verificationStatus === 'rejected' ? 'rejected' : 'inserted';
  return { docId, outcome, verificationStatus };
}

/** Entry point for automated adapters (RSS, official-site discovery). */
export const ingestDiscovered = internalMutation({
  args: { listing: v.any() },
  handler: async (ctx, { listing }) => ingestNormalized(ctx, listing as NormalizedListing),
});

/** Entry point for curated entries; used by seed.run. */
export async function ingestManualEntry(ctx: any, raw: Record<string, any>) {
  return ingestNormalized(ctx, normalizeManualEntry(raw));
}

// ---------------------------------------------------------------- link checks

export const listForLinkCheck = internalQuery({
  args: { which: v.union(v.literal('pending'), v.literal('verified')), limit: v.number() },
  handler: async (ctx, { which, limit }) => {
    const rows =
      which === 'pending'
        ? await ctx.db.query('scholarships').withIndex('by_verification', (q) => q.eq('verificationStatus', 'pending')).collect()
        : await ctx.db.query('scholarships').withIndex('by_status_verification', (q) => q.eq('status', 'active').eq('verificationStatus', 'verified')).collect();
    const cutoff = Date.now() - (which === 'pending' ? RECHECK_PENDING_AFTER_MS : REVALIDATE_VERIFIED_AFTER_MS);
    const due = rows
      .filter((r) => r.status === 'active')
      .filter((r) => {
        const t = which === 'pending' ? r.lastCheckedAt : (r.lastVerifiedAt ?? r.lastCheckedAt);
        return !t || Date.parse(t) < cutoff;
      })
      .sort((a, b) => (a.lastCheckedAt ?? '').localeCompare(b.lastCheckedAt ?? ''));
    return {
      remaining: Math.max(0, due.length - limit),
      docs: due.slice(0, limit).map((r) => ({ id: r._id, applicationUrl: r.applicationUrl, officialSourceUrl: r.officialSourceUrl })),
    };
  },
});

export const applyLinkVerdict = internalMutation({
  args: {
    id: v.id('scholarships'),
    verdict: v.union(v.literal('ok'), v.literal('reject'), v.literal('dead'), v.literal('transient')),
    notes: v.optional(v.string()),
  },
  handler: async (ctx, { id, verdict, notes }) => {
    const doc = await ctx.db.get(id);
    if (!doc || doc.status !== 'active') return;
    const now = new Date().toISOString();
    if (verdict === 'ok') {
      await ctx.db.patch(id, { verificationStatus: 'verified', verificationNotes: undefined, linkFailureCount: 0, lastCheckedAt: now, lastVerifiedAt: now, updatedAt: now });
      return;
    }
    if (verdict === 'reject') {
      await ctx.db.patch(id, { verificationStatus: 'rejected', verificationNotes: notes, lastCheckedAt: now, updatedAt: now });
      return;
    }
    const failures = (doc.linkFailureCount ?? 0) + (verdict === 'dead' ? 2 : 1);
    if (doc.verificationStatus === 'verified') {
      // One bad check must not hide a good listing: demote only after repeated failures.
      await ctx.db.patch(id, {
        linkFailureCount: failures,
        lastCheckedAt: now,
        ...(failures >= DEMOTE_AT ? { verificationStatus: 'pending' as const, verificationNotes: `Demoted after repeated failed link checks. ${notes ?? ''}`, updatedAt: now } : {}),
      });
    } else if (Date.now() - Date.parse(doc.createdAt) > PENDING_GIVE_UP_MS) {
      await ctx.db.patch(id, { verificationStatus: 'rejected', verificationNotes: `Links could not be verified within 14 days. ${notes ?? ''}`, linkFailureCount: failures, lastCheckedAt: now, updatedAt: now });
    } else {
      await ctx.db.patch(id, { verificationNotes: notes, linkFailureCount: failures, lastCheckedAt: now });
    }
  },
});

async function runChecks(ctx: any, docs: { id: string; applicationUrl: string; officialSourceUrl?: string }[]) {
  for (let i = 0; i < docs.length; i += CHECK_CONCURRENCY) {
    await Promise.all(
      docs.slice(i, i + CHECK_CONCURRENCY).map(async (d) => {
        const urls = [d.applicationUrl, d.officialSourceUrl].filter((u): u is string => !!u);
        const result = await checkLinks([...new Set(urls)]);
        await ctx.runMutation(internal.ingestion.applyLinkVerdict, { id: d.id, verdict: result.verdict, notes: 'notes' in result ? result.notes : undefined });
      })
    );
  }
}

/** Link-checks pending listings; reschedules itself while more are due. */
export const verifyPendingListings = internalAction({
  args: {},
  handler: async (ctx) => {
    const { docs, remaining } = await ctx.runQuery(internal.ingestion.listForLinkCheck, { which: 'pending', limit: CHECK_BATCH_LIMIT });
    await runChecks(ctx, docs);
    if (remaining > 0) await ctx.scheduler.runAfter(30_000, internal.ingestion.verifyPendingListings, {});
  },
});

/** Re-checks verified listings (each at most weekly) so "verified" stays meaningful. */
export const revalidateVerifiedListings = internalAction({
  args: {},
  handler: async (ctx) => {
    const { docs } = await ctx.runQuery(internal.ingestion.listForLinkCheck, { which: 'verified', limit: CHECK_BATCH_LIMIT });
    await runChecks(ctx, docs);
  },
});
