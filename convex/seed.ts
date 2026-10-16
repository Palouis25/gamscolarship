import { internalMutation } from './_generated/server';
import { internal } from './_generated/api';
import { scholarships } from './seedData';
import { ingestManualEntry } from './ingestion';
import { ensureSourcesInDb } from './sourceRegistry';

const slugify = (t: string) => t.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');

/** `npx convex run seed:run` — safe to repeat; existing entries are updated, not duplicated. */
export const run = internalMutation({
  args: {},
  handler: async (ctx) => {
    await ensureSourcesInDb(ctx);
    const outcomes: Record<string, number> = {};
    for (const s of scholarships) {
      const r = await ingestManualEntry(ctx, { ...s, sourceId: slugify(s.title) });
      outcomes[r.outcome] = (outcomes[r.outcome] ?? 0) + 1;
    }
    await ctx.scheduler.runAfter(0, internal.ingestion.verifyPendingListings, {});
    return { total: scholarships.length, outcomes };
  },
});
