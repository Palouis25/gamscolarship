import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import { convexTest } from 'convex-test';
import { readFileSync, readdirSync, existsSync } from 'node:fs';
import schema from '../convex/schema';
import { api, internal } from '../convex/_generated/api';

const modules = import.meta.glob('../convex/**/*.ts');
const RSS = readFileSync(new URL('../convex/sources/__fixtures__/erasmusMundusRssSample.xml', import.meta.url), 'utf8');

/** Mocked network: `routes` maps URL -> status (or a Response factory). Unknown URLs -> 200. */
function mockNetwork(routes: Record<string, number | (() => Response)> = {}) {
  const calls: string[] = [];
  vi.stubGlobal('fetch', async (input: any) => {
    const url = typeof input === 'string' ? input : input.url;
    calls.push(url);
    const r = routes[url];
    if (typeof r === 'function') return r();
    return new Response('<html>ok</html>', { status: r ?? 200 });
  });
  return calls;
}

const good = (over: Record<string, any> = {}) => ({
  sourceName: 'manual', sourceId: 'x', title: 'Chevening Test Scholarship', organization: 'FCDO', country: 'UK',
  level: "Master's", funding: 'Fully Funded', field: 'All fields', deadline: '2099-01-01',
  description: 'A funded scholarship for international master students worldwide.',
  eligibility: ['a'], requirements: ['b'], benefits: ['c'], applicationUrl: 'https://www.chevening.org/apply/', ...over,
});
const all = (t: any) => t.run((ctx: any) => ctx.db.query('scholarships').collect());

describe('ingestion + verification pipeline', () => {
  let t: ReturnType<typeof convexTest>;
  beforeEach(() => { vi.useFakeTimers(); t = convexTest(schema, modules); });
  afterEach(() => { vi.unstubAllGlobals(); vi.useRealTimers(); });

  it('seed -> link check -> public list (internal fields hidden); re-seeding adds no duplicates', async () => {
    mockNetwork();
    const first: any = await t.mutation(internal.seed.run, {});
    expect(first.total).toBeGreaterThan(5);
    expect(await t.query(api.scholarships.list, {})).toHaveLength(0); // pending is never public
    await t.action(internal.ingestion.verifyPendingListings, {});
    const pub: any[] = await t.query(api.scholarships.list, {});
    expect(pub.length).toBeGreaterThan(5);
    for (const p of pub) {
      for (const hidden of ['sourceName', 'sourceId', 'verificationNotes', 'possibleDuplicateOf', 'verificationStatus', 'status']) expect(p).not.toHaveProperty(hidden);
    }
    const count = (await all(t)).length;
    const second: any = await t.mutation(internal.seed.run, {});
    expect(second.outcomes.unchanged).toBe(second.total);
    expect((await all(t)).length).toBe(count);
    expect(await t.query(api.scholarships.list, {})).toHaveLength(pub.length); // still public: no flapping
  });

  it('isSample from seed data is preserved', async () => {
    mockNetwork();
    await t.mutation(internal.seed.run, {});
    const rows = await all(t);
    expect(rows.some((r: any) => r.isSample)).toBe(true);
    expect(rows.some((r: any) => !r.isSample)).toBe(true);
  });

  it('a dead link (404) keeps the listing hidden', async () => {
    mockNetwork({ 'https://www.chevening.org/apply/': 404 });
    await t.mutation(internal.ingestion.ingestDiscovered, { listing: good() });
    await t.action(internal.ingestion.verifyPendingListings, {});
    expect(await t.query(api.scholarships.list, {})).toHaveLength(0);
    expect((await all(t))[0].verificationStatus).toBe('pending');
  });

  it('a pending listing that never verifies is rejected after 14 days', async () => {
    mockNetwork({ 'https://www.chevening.org/apply/': 404 });
    await t.mutation(internal.ingestion.ingestDiscovered, { listing: good() });
    const id = (await all(t))[0]._id;
    await t.run((ctx: any) => ctx.db.patch(id, { createdAt: new Date(Date.now() - 15 * 86400000).toISOString(), lastCheckedAt: undefined }));
    await t.action(internal.ingestion.verifyPendingListings, {});
    expect((await all(t))[0].verificationStatus).toBe('rejected');
  });

  it('rejects unrecognised domains, past deadlines, and non-scholarship content (never public, never fetched)', async () => {
    const calls = mockNetwork();
    const bad: any = await t.mutation(internal.ingestion.ingestDiscovered, { listing: good({ sourceId: 'a', applicationUrl: 'https://cheap-scholarships-4u.com/' }) });
    expect(bad.outcome).toBe('rejected');
    const job: any = await t.mutation(internal.ingestion.ingestDiscovered, { listing: good({ sourceId: 'b', title: 'Backend Engineer Internship' }) });
    expect(job.outcome).toBe('rejected');
    const past: any = await t.mutation(internal.ingestion.ingestDiscovered, { listing: good({ sourceId: 'c', deadline: '2020-01-01' }) });
    expect(past.outcome).toBe('expired');
    expect(await all(t)).toHaveLength(2); // the expired one is not stored
    await t.action(internal.ingestion.verifyPendingListings, {});
    expect(calls).toHaveLength(0);
    expect(await t.query(api.scholarships.list, {})).toHaveLength(0);
  });

  it('repeated ingestion of a REJECTED record updates it instead of inserting again', async () => {
    mockNetwork();
    for (let i = 0; i < 3; i++) await t.mutation(internal.ingestion.ingestDiscovered, { listing: good({ sourceId: 'dup', applicationUrl: 'https://cheap-scholarships-4u.com/' }) });
    expect(await all(t)).toHaveLength(1);
  });

  it('flags the same scholarship from another source as a duplicate', async () => {
    mockNetwork();
    await t.mutation(internal.ingestion.ingestDiscovered, { listing: good({ sourceId: 'one' }) });
    const r: any = await t.mutation(internal.ingestion.ingestDiscovered, { listing: good({ sourceName: 'other', sourceId: 'two' }) });
    expect(r.outcome).toBe('duplicate');
    const rows = await all(t);
    expect(rows.filter((x: any) => x.possibleDuplicateOf)).toHaveLength(1);
    await t.action(internal.ingestion.verifyPendingListings, {});
    expect(await t.query(api.scholarships.list, {})).toHaveLength(1);
  });

  it('updates changed content in place and re-verifies when the link changes', async () => {
    mockNetwork();
    await t.mutation(internal.ingestion.ingestDiscovered, { listing: good() });
    await t.action(internal.ingestion.verifyPendingListings, {});
    const r1: any = await t.mutation(internal.ingestion.ingestDiscovered, { listing: good({ description: 'An updated funded scholarship description for students.' }) });
    expect(r1.outcome).toBe('updated');
    expect(await t.query(api.scholarships.list, {})).toHaveLength(1); // same link: stays public
    await t.mutation(internal.ingestion.ingestDiscovered, { listing: good({ applicationUrl: 'https://www.chevening.org/new-page/' }) });
    expect(await t.query(api.scholarships.list, {})).toHaveLength(0); // new link: re-verified first
    expect(await all(t)).toHaveLength(1);
  });

  it('verified listings survive one failed recheck but are demoted after repeated failures', async () => {
    mockNetwork();
    await t.mutation(internal.ingestion.ingestDiscovered, { listing: good() });
    await t.action(internal.ingestion.verifyPendingListings, {});
    const id = (await all(t))[0]._id;
    const age = () => t.run((ctx: any) => ctx.db.patch(id, { lastVerifiedAt: '2020-01-01T00:00:00.000Z', lastCheckedAt: '2020-01-01T00:00:00.000Z' }));
    mockNetwork({ 'https://www.chevening.org/apply/': 503 });
    await age(); await t.action(internal.ingestion.revalidateVerifiedListings, {});
    expect(await t.query(api.scholarships.list, {})).toHaveLength(1); // 1 transient failure: still public
    await age(); await t.action(internal.ingestion.revalidateVerifiedListings, {});
    await age(); await t.action(internal.ingestion.revalidateVerifiedListings, {});
    expect(await t.query(api.scholarships.list, {})).toHaveLength(0); // 3rd: demoted
    mockNetwork();
    await t.action(internal.ingestion.verifyPendingListings, {}); // pending rows are rechecked only after 1h
    expect(await t.query(api.scholarships.list, {})).toHaveLength(0);
    await t.run((ctx: any) => ctx.db.patch(id, { lastCheckedAt: '2020-01-01T00:00:00.000Z' }));
    await t.action(internal.ingestion.verifyPendingListings, {});
    expect(await t.query(api.scholarships.list, {})).toHaveLength(1); // recovered
  });

  it('expired-by-date listings are hidden immediately and by the cron', async () => {
    mockNetwork();
    await t.mutation(internal.ingestion.ingestDiscovered, { listing: good() });
    await t.action(internal.ingestion.verifyPendingListings, {});
    const id = (await all(t))[0]._id;
    await t.run((ctx: any) => ctx.db.patch(id, { deadline: '2020-01-01' }));
    expect(await t.query(api.scholarships.list, {})).toHaveLength(0);
    expect(await t.query(api.scholarships.getById, { id })).toBeNull();
    const r: any = await t.mutation(internal.maintenance.expireStaleListings, {});
    expect(r.expired).toBe(1);
    expect((await all(t))[0].status).toBe('expired');
    // a new cycle of the same scholarship refreshes the expired row instead of duplicating it
    await t.mutation(internal.ingestion.ingestDiscovered, { listing: good({ sourceName: 'other', sourceId: 'new-cycle' }) });
    const rows = await all(t);
    expect(rows).toHaveLength(1);
    expect(rows[0].status).toBe('active');
  });

  it('getById hides pending rows and tolerates garbage ids', async () => {
    mockNetwork();
    await t.mutation(internal.ingestion.ingestDiscovered, { listing: good() });
    const id = (await all(t))[0]._id;
    expect(await t.query(api.scholarships.getById, { id })).toBeNull();
    expect(await t.query(api.scholarships.getById, { id: 'nonsense' })).toBeNull();
  });
});

describe('Erasmus Mundus RSS source (fixture + mocked network)', () => {
  let t: ReturnType<typeof convexTest>;
  const FEED = 'https://www.eacea.ec.europa.eu/node/253/rss_en';
  beforeEach(() => { vi.useFakeTimers({ toFake: ['Date'] }); t = convexTest(schema, modules); });
  afterEach(() => { vi.unstubAllGlobals(); vi.useRealTimers(); });
  const run = () => t.action(internal.rssIngestion.runErasmusMundusSync, {});
  const sourceRow = () => t.run((ctx: any) => ctx.db.query('sources').withIndex('by_name', (q: any) => q.eq('name', 'erasmus-mundus-catalogue-rss')).unique());

  it('self-registers, ingests, is idempotent, and verifies via the EU feed trust anchor', async () => {
    mockNetwork({ [FEED]: () => new Response(RSS, { status: 200 }), [`${FEED}?page=1`]: () => new Response(RSS, { status: 200 }) });
    const r1: any = await run();
    expect(r1.status).toBe('success');
    expect(r1.retrieved).toBeGreaterThan(0);
    const n = (await all(t)).length;
    expect(n).toBe(r1.retrieved);
    const r2: any = await run();
    expect(r2.status).toBe('success');
    expect((await all(t)).length).toBe(n);
    expect((await sourceRow()).lastRunStats.retrieved).toBe(r1.retrieved);
    await t.action(internal.ingestion.verifyPendingListings, {});
    expect((await t.query(api.scholarships.list, {})).length).toBe(n);
  });

  it('records a FAILED run when the feed is down (not a fake success)', async () => {
    mockNetwork({ [FEED]: 500 });
    vi.useFakeTimers({ toFake: ['Date', 'setTimeout'], shouldAdvanceTime: true });
    const p = run();
    await vi.advanceTimersByTimeAsync(20000);
    const r: any = await p;
    expect(r.status).toBe('failed');
    const row = await sourceRow();
    expect(row.consecutiveFailures).toBe(1);
    expect(row.lastError).toContain('HTTP 500');
  });

  it('does nothing when the source has been paused', async () => {
    mockNetwork({ [FEED]: () => new Response(RSS, { status: 200 }) });
    await run();
    const row = await sourceRow();
    await t.run((ctx: any) => ctx.db.patch(row._id, { enabled: false }));
    const calls = mockNetwork();
    await run();
    expect(calls).toHaveLength(0);
  });
});

describe('discovery without credentials', () => {
  it('records a clear failure instead of silently doing nothing', async () => {
    const t = convexTest(schema, modules);
    delete process.env.ANTHROPIC_API_KEY;
    await t.mutation(internal.sourceRegistry.ensureSources, {});
    await t.action(internal.discovery.runDiscoveryForSource, { slug: 'daad-official-site' });
    const row: any = await t.run((ctx: any) => ctx.db.query('sources').withIndex('by_name', (q: any) => q.eq('name', 'daad-official-site')).unique());
    expect(row.lastError).toContain('ANTHROPIC_API_KEY');
  });
});

describe('frontend <-> backend contract', () => {
  it('every api.x.y referenced in src/ is a public function that exists', () => {
    const files: string[] = [];
    const walk = (d: string) => { for (const e of readdirSync(d, { withFileTypes: true })) { const p = `${d}/${e.name}`; e.isDirectory() ? walk(p) : /\.jsx?$/.test(e.name) && files.push(p); } };
    walk('src');
    const refs = new Set<string>();
    for (const f of files) for (const m of readFileSync(f, 'utf8').matchAll(/\bapi\.(\w+)\.(\w+)/g)) refs.add(`${m[1]}.${m[2]}`);
    expect(refs.size).toBeGreaterThan(0);
    for (const ref of refs) {
      const [mod, fn] = ref.split('.');
      const file = `convex/${mod}.ts`;
      expect(existsSync(file), file).toBe(true);
      expect(readFileSync(file, 'utf8'), ref).toMatch(new RegExp(`export const ${fn} = (query|mutation|action)\\(`));
    }
  });
});
