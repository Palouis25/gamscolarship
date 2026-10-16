import { v } from 'convex/values';
import { internalMutation, internalQuery } from './_generated/server';
import { SOURCE_REGISTRY } from './sources/registry';

/** Upserts a row per source in SOURCE_REGISTRY. New rows start enabled;
 *  existing rows keep their `enabled` flag so a paused source stays paused. */
export async function ensureSourcesInDb(ctx: any) {
  for (const s of SOURCE_REGISTRY) {
    const existing = await ctx.db.query('sources').withIndex('by_name', (q: any) => q.eq('name', s.name)).unique();
    if (existing) await ctx.db.patch(existing._id, { label: s.label, kind: s.kind, trustLevel: s.trustLevel, notes: s.notes });
    else await ctx.db.insert('sources', { ...s, enabled: true });
  }
}

export const ensureSources = internalMutation({
  args: {},
  handler: async (ctx) => {
    await ensureSourcesInDb(ctx);
  },
});

export const listEnabledSourceNames = internalQuery({
  args: {},
  handler: async (ctx) => (await ctx.db.query('sources').collect()).filter((s) => s.enabled).map((s) => s.name),
});

export const recordRunResult = internalMutation({
  args: {
    sourceName: v.string(),
    startedAt: v.string(),
    status: v.union(v.literal('success'), v.literal('partial'), v.literal('failed')),
    retrieved: v.number(),
    accepted: v.number(),
    rejected: v.number(),
    error: v.optional(v.string()),
  },
  handler: async (ctx, { sourceName, startedAt, status, retrieved, accepted, rejected, error }) => {
    const finishedAt = new Date().toISOString();
    await ctx.db.insert('ingestionRuns', { sourceName, startedAt, finishedAt, status, retrieved, accepted, rejected, error });
    const existing = await ctx.db.query('sources').withIndex('by_name', (q) => q.eq('name', sourceName)).unique();
    if (!existing) return;
    const patch: Record<string, unknown> = { lastRunAt: finishedAt, lastRunStats: { retrieved, accepted, rejected } };
    if (status === 'failed') {
      patch.lastFailureAt = finishedAt;
      patch.lastError = error;
      patch.consecutiveFailures = (existing.consecutiveFailures ?? 0) + 1;
    } else {
      patch.lastSuccessAt = finishedAt;
      patch.consecutiveFailures = 0;
      patch.lastError = status === 'partial' ? error : undefined;
    }
    await ctx.db.patch(existing._id, patch);
  },
});
