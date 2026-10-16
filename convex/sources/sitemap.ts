// Sitemap / robots.txt helpers for official-site discovery. Sitemaps are the
// providers' own published page lists, so no search API is needed.

import { XMLParser } from 'fast-xml-parser';
import { hostMatches, hostOf } from '../lib/urlSafety';

export function parseSitemap(xml: string): { urls: string[]; sitemaps: string[] } {
  const doc = new XMLParser({ ignoreAttributes: true }).parse(xml);
  const arr = (x: any) => (x == null ? [] : Array.isArray(x) ? x : [x]);
  const loc = (x: any) => (typeof x?.loc === 'string' ? x.loc.trim() : '');
  return {
    urls: arr(doc?.urlset?.url).map(loc).filter(Boolean),
    sitemaps: arr(doc?.sitemapindex?.sitemap).map(loc).filter(Boolean),
  };
}

/** Sitemap: lines and Disallow rules for the `*` user-agent group. */
export function parseRobots(text: string): { sitemaps: string[]; disallow: string[] } {
  const sitemaps: string[] = [];
  const disallow: string[] = [];
  let applies = false;
  for (const raw of text.split(/\r?\n/)) {
    const line = raw.replace(/#.*/, '').trim();
    const idx = line.indexOf(':');
    if (idx < 0) continue;
    const key = line.slice(0, idx).trim().toLowerCase();
    const val = line.slice(idx + 1).trim();
    if (key === 'sitemap' && val) sitemaps.push(val);
    else if (key === 'user-agent') applies = val === '*';
    else if (key === 'disallow' && applies && val) disallow.push(val);
  }
  return { sitemaps, disallow };
}

export function isAllowedByRobots(url: string, disallow: string[]): boolean {
  try {
    const u = new URL(url);
    const path = u.pathname + u.search;
    return !disallow.some((d) => path.startsWith(d.replace(/\*.*$/, '')));
  } catch {
    return false;
  }
}

const CANDIDATE = /scholarship|bursar|studentship|funding|grant|award|stipend/i;
const SKIP_EXT = /\.(pdf|jpe?g|png|gif|webp|svg|zip|docx?|xlsx?|mp4)$/i;

/** Pages on the source's own domain whose URL suggests scholarship content. */
export function pickCandidates(urls: string[], domain: string, disallow: string[]): string[] {
  const out: string[] = [];
  const seen = new Set<string>();
  for (const url of urls) {
    const host = hostOf(url);
    if (!host || !hostMatches(host, domain) || !url.startsWith('https://')) continue;
    if (!CANDIDATE.test(new URL(url).pathname) || SKIP_EXT.test(url)) continue;
    if (!isAllowedByRobots(url, disallow) || seen.has(url)) continue;
    seen.add(url);
    out.push(url);
  }
  return out;
}
