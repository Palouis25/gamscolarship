// Tier 1 source: the Erasmus Mundus Catalogue RSS feed published by EACEA
// (European Commission). See sources/erasmusMundusAdapter.ts for what the
// feed contains. No credentials are needed.
//
//   RSS pages -> parseRssFeed -> normalizeErasmusMundusItem -> ingestDiscovered
//
// Pagination (`?page=N`) is the conventional Drupal Views parameter but has
// not been confirmed against the live feed; the loop stops on the first
// empty page, a repeated page, or a 404, so a wrong assumption only costs a
// few requests. A failed FIRST page is recorded as a failed run (it used to
// be recorded as a successful run with zero items).

import { internalAction } from './_generated/server';
import { internal } from './_generated/api';
import { parseRssFeed, normalizeErasmusMundusItem } from './sources/erasmusMundusAdapter';
import { ERASMUS_SOURCE } from './sources/registry';
import { fetchWithRetry } from './lib/http';

export const FEED_URL = 'https://www.eacea.ec.europa.eu/node/253/rss_en';
const MAX_PAGES = 15;

export const runErasmusMundusSync = internalAction({
  args: {},
  handler: async (ctx) => {
    await ctx.runMutation(internal.sourceRegistry.ensureSources, {});
    const enabled: string[] = await ctx.runQuery(internal.sourceRegistry.listEnabledSourceNames, {});
    if (!enabled.includes(ERASMUS_SOURCE)) return { skipped: 'source disabled' };

    const startedAt = new Date().toISOString();
    let retrieved = 0, accepted = 0, rejected = 0;
    const errors: string[] = [];
    const seen = new Set<string>();

    for (let page = 0; page < MAX_PAGES; page++) {
      let xml: string;
      try {
        const res = await fetchWithRetry(page === 0 ? FEED_URL : `${FEED_URL}?page=${page}`, { redirect: 'follow' });
        if (res.status === 404 && page > 0) break; // end of pagination
        if (!res.ok) {
          errors.push(`feed page ${page}: HTTP ${res.status}`);
          break;
        }
        xml = await res.text();
      } catch (err) {
        errors.push(`feed page ${page}: ${(err as Error).message}`);
        break;
      }

      let items;
      try {
        items = parseRssFeed(xml);
      } catch (err) {
        errors.push(`parse page ${page}: ${(err as Error).message}`);
        break;
      }
      const fresh = items.filter((i) => !seen.has(i.guid));
      if (fresh.length === 0) break; // empty or repeated page: pagination exhausted
      fresh.forEach((i) => seen.add(i.guid));
      retrieved += fresh.length;

      for (const item of fresh) {
        try {
          const r: any = await ctx.runMutation(internal.ingestion.ingestDiscovered, { listing: normalizeErasmusMundusItem(item) });
          if (['inserted', 'updated', 'unchanged'].includes(r.outcome)) accepted++;
          else rejected++;
        } catch (err) {
          errors.push(`ingest ${item.guid}: ${(err as Error).message}`);
          rejected++;
        }
      }
    }

    const status = retrieved === 0 ? 'failed' : errors.length === 0 ? 'success' : 'partial';
    await ctx.runMutation(internal.sourceRegistry.recordRunResult, {
      sourceName: ERASMUS_SOURCE, startedAt, status, retrieved, accepted, rejected,
      error: retrieved === 0 && errors.length === 0 ? 'Feed returned no items.' : errors.length ? errors.slice(0, 5).join(' | ') : undefined,
    });
    if (accepted > 0) await ctx.scheduler.runAfter(0, internal.ingestion.verifyPendingListings, {});
    return { status, retrieved, accepted, rejected };
  },
});
