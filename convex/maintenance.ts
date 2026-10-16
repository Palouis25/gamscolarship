import { v } from 'convex/values';
import { internalMutation } from './_generated/server';
import { internal } from './_generated/api';
import { isExpired } from './lib/deadline';

/** Marks active listings whose (real-date) deadline has passed as expired.
 *  Descriptive deadlines ("Varies by country") are left alone. */
export const expireStaleListings = internalMutation({
  args: {},
  handler: async (ctx) => {
    const active = await ctx.db.query('scholarships').withIndex('by_status', (q) => q.eq('status', 'active')).collect();
    let expired = 0;
    for (const s of active) {
      if (isExpired(s.deadline)) {
        await ctx.db.patch(s._id, { status: 'expired', updatedAt: new Date().toISOString() });
        expired++;
      }
    }
    return { expired };
  },
});

/** Keeps the ingestionRuns diagnostics table from growing forever. */
export const pruneIngestionRuns = internalMutation({
  args: {},
  handler: async (ctx) => {
    const cutoff = new Date(Date.now() - 60 * 24 * 3600 * 1000).toISOString();
    const old = await ctx.db.query('ingestionRuns').filter((q) => q.lt(q.field('startedAt'), cutoff)).take(500);
    for (const r of old) await ctx.db.delete(r._id);
    return { deleted: old.length };
  },
});

/** One-off clean-up of the retired `opportunities` table:
 *  `npx convex run maintenance:purgeLegacyOpportunities` (repeat until remaining is 0). */
export const purgeLegacyOpportunities = internalMutation({
  args: { batch: v.optional(v.number()) },
  handler: async (ctx, { batch }) => {
    const rows = await ctx.db.query('opportunities').take(batch ?? 200);
    for (const r of rows) await ctx.db.delete(r._id);
    const more = await ctx.db.query('opportunities').first();
    if (more) await ctx.scheduler.runAfter(0, internal.maintenance.purgeLegacyOpportunities, { batch });
    return { deleted: rows.length, remaining: more ? 'more (rescheduled)' : 0 };
  },
});
