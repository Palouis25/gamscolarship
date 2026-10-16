import { query } from './_generated/server';
import { v } from 'convex/values';
import { isExpired } from './lib/deadline';

/**
 * Public queries. The ONLY scholarship data the frontend can read:
 *  - rows must be status=active AND verificationStatus=verified
 *  - a deadline that has passed is hidden immediately (the expiry cron is a
 *    clean-up, not what protects users from stale listings)
 *  - pipeline-internal fields (source ids, verification notes, duplicate
 *    links) are never returned.
 * There are no public mutations: collection is fully automatic.
 */
function toPublic(doc: any) {
  return {
    _id: doc._id,
    title: doc.title,
    organization: doc.organization,
    country: doc.country,
    level: doc.level,
    funding: doc.funding,
    field: doc.field,
    deadline: doc.deadline,
    description: doc.description,
    eligibility: doc.eligibility,
    requirements: doc.requirements,
    benefits: doc.benefits,
    applicationUrl: doc.applicationUrl,
    officialSourceUrl: doc.officialSourceUrl,
    isSample: doc.isSample,
    lastVerifiedAt: doc.lastVerifiedAt,
  };
}

export const list = query({
  args: {},
  handler: async (ctx) => {
    const rows = await ctx.db
      .query('scholarships')
      .withIndex('by_status_verification', (q) => q.eq('status', 'active').eq('verificationStatus', 'verified'))
      .order('desc')
      .take(1000);
    return rows.filter((r) => !isExpired(r.deadline)).map(toPublic);
  },
});

export const getById = query({
  args: { id: v.string() },
  handler: async (ctx, { id }) => {
    const normalized = ctx.db.normalizeId('scholarships', id);
    if (!normalized) return null;
    const doc = await ctx.db.get(normalized);
    if (!doc || doc.status !== 'active' || doc.verificationStatus !== 'verified' || isExpired(doc.deadline)) return null;
    return toPublic(doc);
  },
});
