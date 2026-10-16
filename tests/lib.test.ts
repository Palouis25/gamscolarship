import { describe, it, expect } from 'vitest';
import { parseDeadline, isExpired, daysUntil } from '../convex/lib/deadline';
import { assessUrl, isOfficialHost, registrableDomain, sameSite } from '../convex/lib/urlSafety';
import { checkUrl, combineResults } from '../convex/lib/linkCheck';
import { runStaticChecks, looksTemplated, isScholarshipContent } from '../convex/validation';
import { duplicateScore } from '../convex/duplicates';
import { parseRobots, parseSitemap, pickCandidates } from '../convex/sources/sitemap';
import { parseExtractionResponse } from '../convex/sources/officialSiteAdapter';
import { fetchWithRetry } from '../convex/lib/http';

describe('deadline', () => {
  it('parses strict dates only', () => {
    expect(parseDeadline('2026-11-03')).toEqual({ kind: 'date', iso: '2026-11-03' });
    expect(parseDeadline('3 November 2026')).toEqual({ kind: 'date', iso: '2026-11-03' });
    expect(parseDeadline('November 3, 2026')).toEqual({ kind: 'date', iso: '2026-11-03' });
    expect(parseDeadline('2026-02-31').kind).toBe('text');
  });
  it('never turns descriptive text into a date', () => {
    const t = 'Applications for the next cohort open April 2027 (2027-28 cycle closed September 2026)';
    expect(parseDeadline(t).kind).toBe('text');
    expect(isExpired(t, '2030-01-01')).toBe(false);
    expect(daysUntil('Varies by country')).toBeNull();
  });
  it('expiry and countdown', () => {
    expect(isExpired('2026-10-01', '2026-10-14')).toBe(true);
    expect(isExpired('2026-10-14', '2026-10-14')).toBe(false);
    expect(daysUntil('2026-10-24', '2026-10-14')).toBe(10);
  });
});

describe('urlSafety', () => {
  it('flags unsafe URLs', () => {
    for (const u of ['http://chevening.org/', 'https://bit.ly/abc', 'https://192.168.0.1/x', 'https://user:pw@daad.de/', 'https://xn--chvening-9ya.org/', 'https://localhost/', 'https://daad.de:8443/', 'not a url']) {
      expect(assessUrl(u).length, u).toBeGreaterThan(0);
    }
    expect(assessUrl('https://www.chevening.org/scholarships/')).toEqual([]);
  });
  it('recognises official hosts without being fooled by look-alikes', () => {
    expect(isOfficialHost('www.chevening.org')).toBe(true);
    expect(isOfficialHost('cscuk.fcdo.gov.uk')).toBe(true);
    expect(isOfficialHost('ox.ac.uk')).toBe(true);
    expect(isOfficialHost('evilchevening.org')).toBe(false);
    expect(isOfficialHost('chevening.org.scam.xyz')).toBe(false);
    expect(isOfficialHost('free-scholarships-now.com')).toBe(false);
  });
  it('registrable domain', () => {
    expect(registrableDomain('www2.daad.de')).toBe('daad.de');
    expect(registrableDomain('www.ox.ac.uk')).toBe('ox.ac.uk');
    expect(sameSite('www.daad.de', 'daad.de')).toBe(true);
    expect(sameSite('daad.de', 'evil.com')).toBe(false);
  });
});

const res = (status: number, url = '') => ({ status, url, body: { cancel: async () => {} } });
describe('linkCheck', () => {
  const run = (r: any, url = 'https://www.chevening.org/') => checkUrl(url, { fetchImpl: async () => r });
  it('classifies statuses', async () => {
    expect((await run(res(200))).outcome).toBe('ok');
    expect((await run(res(404))).outcome).toBe('dead');
    expect((await run(res(410))).outcome).toBe('dead');
    expect((await run(res(503))).outcome).toBe('transient');
    expect((await run(res(429))).outcome).toBe('transient');
    expect((await run(res(403))).outcome).toBe('blocked');
  });
  it('network errors and timeouts are transient', async () => {
    expect((await checkUrl('https://daad.de/', { fetchImpl: async () => { throw new Error('ECONNRESET'); } })).outcome).toBe('transient');
    const abort = Object.assign(new Error('x'), { name: 'AbortError' });
    expect((await checkUrl('https://daad.de/', { fetchImpl: async () => { throw abort; } })).detail).toBe('Timed out');
  });
  it('rejects suspicious URLs without fetching, and cross-domain redirects', async () => {
    let called = false;
    const r = await checkUrl('https://bit.ly/x', { fetchImpl: async () => { called = true; return res(200); } });
    expect(r.outcome).toBe('unsafe');
    expect(called).toBe(false);
    expect((await run(res(200, 'https://parked-domain.example.xyz/'))).outcome).toBe('unsafe');
    expect((await run(res(200, 'https://www.chevening.org/en/'))).outcome).toBe('ok');
  });
  it('403 is accepted only for recognised official hosts', async () => {
    const official = await run(res(403));
    expect(combineResults([official]).verdict).toBe('ok');
    const other = await checkUrl('https://random-uni.example.com/', { fetchImpl: async () => res(403) });
    expect(combineResults([other]).verdict).toBe('transient');
    expect(combineResults([{ url: 'u', outcome: 'ok', detail: '' }, { url: 'v', outcome: 'dead', detail: '' }]).verdict).toBe('dead');
  });
});

const base: any = {
  sourceName: 'manual', title: 'Test Scholarship', organization: 'Org', country: 'UK', level: "Master's",
  funding: 'Fully Funded', field: 'All', deadline: '2030-01-01',
  description: 'A funded scholarship for international master students worldwide.',
  eligibility: [], requirements: [], benefits: [], applicationUrl: 'https://www.chevening.org/apply/',
};
describe('validation', () => {
  it('accepts a good listing', () => expect(runStaticChecks(base, '2026-10-14')).toEqual([]));
  it('rejects past deadline, unknown host, http, missing fields', () => {
    expect(runStaticChecks({ ...base, deadline: '2020-01-01' }, '2026-10-14').some((i) => i.field === 'deadline')).toBe(true);
    expect(runStaticChecks({ ...base, applicationUrl: 'https://best-scholarships-4u.com/' }).some((i) => i.problem.includes('recognised official'))).toBe(true);
    expect(runStaticChecks({ ...base, applicationUrl: 'https://best-scholarships-4u.com/', urlTrust: 'official_feed' })).toEqual([]);
    expect(runStaticChecks({ ...base, applicationUrl: 'http://www.chevening.org/' }).length).toBeGreaterThan(0);
    expect(runStaticChecks({ ...base, organization: ' ' }).length).toBeGreaterThan(0);
  });
  it('keeps the platform scholarships-only', () => {
    expect(isScholarshipContent({ title: 'Software Engineering Internship', description: 'Join our team' })).toBeTruthy();
    expect(isScholarshipContent({ title: 'Young Leaders Fellowship', description: 'funded fellowship' })).toBeTruthy();
    expect(isScholarshipContent({ title: 'Global Hackathon Competition', description: 'win a scholarship' })).toBeTruthy();
    expect(isScholarshipContent({ title: 'Chevening Scholarships', description: 'x' })).toBeNull();
    expect(looksTemplated({ title: 'Global Scholarship Program 133', organization: 'International Education Foundation' })).toBe(true);
  });
});

describe('duplicates', () => {
  const a = { title: 'DAAD Scholarship', organization: 'DAAD', applicationUrl: 'https://www.daad.de/en/x/', deadline: '2027-01-01' };
  it('same url + title is a duplicate; unrelated is not', () => {
    expect(duplicateScore(a, { ...a, applicationUrl: 'https://daad.de/en/x' })).toBeGreaterThanOrEqual(0.6);
    expect(duplicateScore(a, { title: 'Fulbright', organization: 'US', applicationUrl: 'https://foreign.fulbrightonline.org/', deadline: 'Varies' })).toBeLessThan(0.6);
  });
});

describe('sitemap + extraction', () => {
  it('parses sitemaps, robots and picks candidates', () => {
    const idx = parseSitemap('<sitemapindex><sitemap><loc>https://x.org/s1.xml</loc></sitemap></sitemapindex>');
    expect(idx.sitemaps).toEqual(['https://x.org/s1.xml']);
    const set = parseSitemap('<urlset><url><loc>https://x.org/en/scholarships/abc</loc></url><url><loc>https://x.org/about</loc></url><url><loc>https://x.org/private/scholarship/z</loc></url><url><loc>https://evil.com/scholarship/q</loc></url><url><loc>https://x.org/scholarship/a.pdf</loc></url></urlset>');
    const robots = parseRobots('User-agent: *\nDisallow: /private/\nSitemap: https://x.org/sitemap.xml\nUser-agent: bad\nDisallow: /');
    expect(robots.sitemaps).toEqual(['https://x.org/sitemap.xml']);
    expect(robots.disallow).toEqual(['/private/']);
    expect(pickCandidates(set.urls, 'x.org', robots.disallow)).toEqual(['https://x.org/en/scholarships/abc']);
  });
  it('extraction parser is strict and confines the apply link to the page site', () => {
    const ok = { found: true, title: 'T', organization: 'O', country: 'C', deadline: '2030-01-01', description: 'D', level: 'L', funding: 'F', field: 'X', applicationUrl: 'https://evil.com/apply', eligibility: ['a'] };
    const r: any = parseExtractionResponse(JSON.stringify(ok), 's', 'https://www.daad.de/en/p');
    expect(r.listing.applicationUrl).toBe('https://www.daad.de/en/p');
    expect((parseExtractionResponse('not json', 's', 'https://daad.de/') as any).found).toBe(false);
    expect((parseExtractionResponse(JSON.stringify({ ...ok, title: '' }), 's', 'https://daad.de/') as any).found).toBe(false);
  });
});

describe('fetchWithRetry', () => {
  it('retries 429/5xx then succeeds, and gives up after N tries', async () => {
    let n = 0;
    const f = (async () => new Response('x', { status: ++n < 3 ? 503 : 200 })) as any;
    const r = await fetchWithRetry('https://a.org/', {}, { fetchImpl: f, sleep: async () => {} });
    expect(r.status).toBe(200);
    expect(n).toBe(3);
    const down = (async () => { throw new Error('down'); }) as any;
    await expect(fetchWithRetry('https://a.org/', {}, { fetchImpl: down, sleep: async () => {} })).rejects.toThrow('down');
  });
});

import { describeDeadline, splitList, byDeadline } from '../src/lib/format.js';
describe('frontend format helpers', () => {
  it('formats dates in UTC and leaves descriptive deadlines untouched', () => {
    expect(describeDeadline('2030-11-03').label).toBe('3 Nov 2030');
    const t = 'Applications for the next cohort open April 2027 (2027-28 cycle closed September 2026)';
    expect(describeDeadline(t)).toMatchObject({ label: t, isDate: false, tone: 'text' });
  });
  it('splits multi-value levels and sorts dated before undated', () => {
    expect(splitList("Undergraduate, Master's, PhD")).toEqual(['Undergraduate', "Master's", 'PhD']);
    const rows = [{ deadline: 'Varies' }, { deadline: '2031-02-01' }, { deadline: '2030-01-01' }];
    expect(rows.sort(byDeadline).map((r) => r.deadline)).toEqual(['2030-01-01', '2031-02-01', 'Varies']);
  });
});
