// Tier 2: official-site discovery. Replaces the old Google Custom Search step
// (that API is closed to new customers and shuts down on 2027-01-01).
//
//   robots.txt + sitemap(s) of an allow-listed official domain
//     -> candidate URLs (path suggests scholarship content, robots-allowed)
//     -> fetch page text -> Anthropic extracts ONE listing or {found:false}
//     -> ingestDiscovered (same validation, dedupe and link verification)
//
// Needs ANTHROPIC_API_KEY (optionally ANTHROPIC_MODEL). Without it each run
// records a clear failure reason instead of silently doing nothing.
// NOT verified against the live provider sites from the build environment
// (no outbound access there); check `sources.lastError` after first runs.

import { v } from 'convex/values';
import { internalAction, internalQuery } from './_generated/server';
import { internal } from './_generated/api';
import { OFFICIAL_SITE_SOURCES } from './sources/officialSiteSources';
import { htmlToText, buildExtractionPrompt, parseExtractionResponse, EXTRACTION_SYSTEM_PROMPT } from './sources/officialSiteAdapter';
import { parseRobots, parseSitemap, pickCandidates } from './sources/sitemap';
import { fetchWithRetry } from './lib/http';

const MAX_PAGES_PER_SOURCE = 8;
const MAX_CHILD_SITEMAPS = 5;
const MAX_SITEMAP_CHARS = 5_000_000;
const REFRESH_AFTER_MS = 14 * 24 * 3600 * 1000;
const DEFAULT_MODEL = 'claude-sonnet-5-5';

async function getText(url: string, max = MAX_SITEMAP_CHARS): Promise<string | null> {
  try {
    const res = await fetchWithRetry(url, { redirect: 'follow' }, { tries: 2 });
    if (!res.ok) return null;
    return (await res.text()).slice(0, max);
  } catch {
    return null;
  }
}

async function collectUrls(domain: string): Promise<{ urls: string[]; disallow: string[]; error?: string }> {
  const origin = `https://${domain}`;
  const robots = parseRobots((await getText(`${origin}/robots.txt`, 100_000)) ?? '');
  const roots = robots.sitemaps.length ? robots.sitemaps : [`${origin}/sitemap.xml`];
  const urls: string[] = [];
  let children = 0;
  for (const root of roots.slice(0, 3)) {
    const xml = await getText(root);
    if (!xml) continue;
    const parsed = parseSitemap(xml);
    urls.push(...parsed.urls);
    for (const child of parsed.sitemaps) {
      if (children++ >= MAX_CHILD_SITEMAPS) break;
      const cx = await getText(child);
      if (cx) urls.push(...parseSitemap(cx).urls);
    }
  }
  return urls.length ? { urls, disallow: robots.disallow } : { urls, disallow: robots.disallow, error: `No sitemap URLs found for ${domain}.` };
}

async function extract(pageUrl: string, text: string, label: string, slug: string, key: string) {
  const res = await fetchWithRetry('https://api.anthropic.com/v1/messages', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'x-api-key': key, 'anthropic-version': '2023-06-01' },
    body: JSON.stringify({
      model: process.env.ANTHROPIC_MODEL || DEFAULT_MODEL,
      max_tokens: 1200,
      system: EXTRACTION_SYSTEM_PROMPT,
      messages: [{ role: 'user', content: buildExtractionPrompt(text, pageUrl, label) }],
    }),
  });
  if (!res.ok) throw new Error(`Anthropic API responded with status ${res.status}`);
  const data: any = await res.json();
  const block = (data.content ?? []).find((b: any) => b.type === 'text');
  return block ? parseExtractionResponse(block.text, slug, pageUrl) : ({ found: false } as const);
}

/** Of `urls`, those not checked within REFRESH_AFTER_MS (new pages first). */
export const dueUrls = internalQuery({
  args: { sourceName: v.string(), urls: v.array(v.string()) },
  handler: async (ctx, { sourceName, urls }) => {
    const cutoff = Date.now() - REFRESH_AFTER_MS;
    const due: string[] = [];
    for (const url of urls) {
      const row = await ctx.db.query('scholarships').withIndex('by_source', (q) => q.eq('sourceName', sourceName).eq('sourceId', url)).first();
      if (!row) due.unshift(url);
      else if (!row.lastCheckedAt || Date.parse(row.lastCheckedAt) < cutoff) due.push(url);
    }
    return due;
  },
});

export const runDiscoveryForSource = internalAction({
  args: { slug: v.string() },
  handler: async (ctx, { slug }): Promise<void> => {
    const source = OFFICIAL_SITE_SOURCES.find((s) => s.slug === slug);
    const startedAt = new Date().toISOString();
    const record = async (status: 'success' | 'partial' | 'failed', retrieved: number, accepted: number, rejected: number, error?: string): Promise<void> => {
      await ctx.runMutation(internal.sourceRegistry.recordRunResult, { sourceName: slug, startedAt, status, retrieved, accepted, rejected, error });
    };

    if (!source) return record('failed', 0, 0, 0, `Unknown source "${slug}".`);
    const key = process.env.ANTHROPIC_API_KEY;
    if (!key) return record('failed', 0, 0, 0, 'ANTHROPIC_API_KEY is not set on this Convex deployment.');

    const { urls, disallow, error } = await collectUrls(source.domain);
    if (error) return record('failed', 0, 0, 0, error);
    const candidates = pickCandidates(urls, source.domain, disallow);
    const due: string[] = await ctx.runQuery(internal.discovery.dueUrls, { sourceName: slug, urls: candidates.slice(0, 200) });
    const batch = due.slice(0, MAX_PAGES_PER_SOURCE);

    let accepted = 0, rejected = 0;
    const errors: string[] = [];
    for (const url of batch) {
      const html = await getText(url, 2_000_000);
      const text = html ? htmlToText(html) : '';
      if (text.length < 200) { rejected++; continue; }
      try {
        const ex = await extract(url, text, source.label, slug, key);
        if (!ex.found) { rejected++; continue; }
        const r: any = await ctx.runMutation(internal.ingestion.ingestDiscovered, { listing: ex.listing });
        if (['inserted', 'updated', 'unchanged'].includes(r.outcome)) accepted++;
        else rejected++;
      } catch (err) {
        errors.push(`${url}: ${(err as Error).message}`);
        rejected++;
      }
    }
    const status = errors.length === 0 ? 'success' : accepted > 0 ? 'partial' : 'failed';
    await record(status, batch.length, accepted, rejected, errors.slice(0, 5).join(' | ') || undefined);
    if (accepted > 0) await ctx.scheduler.runAfter(0, internal.ingestion.verifyPendingListings, {});
  },
});

/** Schedules each enabled source as its own action (staggered), so one slow
 *  site cannot exhaust a single action's time limit. */
export const runAllDiscovery = internalAction({
  args: {},
  handler: async (ctx) => {
    await ctx.runMutation(internal.sourceRegistry.ensureSources, {});
    const enabled: string[] = await ctx.runQuery(internal.sourceRegistry.listEnabledSourceNames, {});
    let i = 0;
    for (const s of OFFICIAL_SITE_SOURCES) {
      if (!enabled.includes(s.slug)) continue;
      await ctx.scheduler.runAfter(i++ * 90_000, internal.discovery.runDiscoveryForSource, { slug: s.slug });
    }
  },
});
