// Adapter for the Erasmus Mundus Catalogue RSS feed, published by EACEA
// (European Education and Culture Executive Agency -- an EU government
// body) at:
//
//   https://www.eacea.ec.europa.eu/node/253/rss_en
//
// This is a genuine, credential-free, government-run structured feed --
// no API key, no Google search, no LLM extraction needed. It ranks above
// the "official site" Google-CSE-plus-extraction adapter
// (convex/sources/officialSiteAdapter.ts) in the source priority order
// because it's an official feed (priority tier 2) rather than a
// search-assisted fallback (priority tier 6).
//
// What the feed actually contains (confirmed by fetching it directly while
// building this adapter): for each of ~200+ currently EU-funded Erasmus
// Mundus Joint Master programmes, a title, the programme's own official
// website as <link>, an EACEA node id as a stable guid, a short
// description naming the project acronym and its Erasmus+ project-details
// page, and <category> tags for field(s) of study, ECTS duration,
// participating universities, and funding year(s). It does NOT include a
// per-programme application deadline or a rich description -- those live
// on each programme's own official site (the <link> URL), which is why
// `deadline` below is an honest descriptive string rather than an invented
// ISO date, and why `applicationUrl`/`officialSourceUrl` both point at the
// programme's own site for GamScholarship's existing reachability check to
// verify.
//
// Every fact placed in the normalized listing below is either read
// directly from the feed item or copied verbatim from EACEA's own general
// description of the programme (fetched from
// https://www.eacea.ec.europa.eu/scholarships/erasmus-mundus-catalogue_en
// while building this adapter) -- nothing here is invented.

import { XMLParser } from 'fast-xml-parser';
import type { NormalizedListing } from './types';

export type RssCategory = { domain?: string; value: string };

export type RssItem = {
  title: string;
  link: string;
  guid: string;
  descriptionHtml: string;
  pubDate?: string;
  categories: RssCategory[];
};

/**
 * Parses one page of the Erasmus Mundus Catalogue RSS XML into a flat list
 * of items. Tolerant of a feed with zero or one <item> (fast-xml-parser
 * gives an object instead of an array for a single element) and of items
 * missing optional fields.
 */
export function parseRssFeed(xml: string): RssItem[] {
  const parser = new XMLParser({
    ignoreAttributes: false,
    attributeNamePrefix: '@_',
    textNodeName: '#text',
  });
  const doc = parser.parse(xml);
  const rawItems = doc?.rss?.channel?.item;
  if (!rawItems) return [];
  const items = Array.isArray(rawItems) ? rawItems : [rawItems];

  return items
    .map((item: any): RssItem | null => {
      const title = typeof item.title === 'string' ? item.title.trim() : '';
      const link = typeof item.link === 'string' ? item.link.trim() : '';
      const guidRaw = item.guid;
      const guid =
        typeof guidRaw === 'string'
          ? guidRaw
          : typeof guidRaw === 'object' && typeof guidRaw?.['#text'] === 'string'
          ? guidRaw['#text']
          : link;
      if (!title || !link || !guid) return null;

      const rawCategories = item.category;
      const categoryList = Array.isArray(rawCategories) ? rawCategories : rawCategories ? [rawCategories] : [];
      const categories: RssCategory[] = categoryList.map((c: any) => ({
        domain: typeof c === 'object' ? c['@_domain'] : undefined,
        value: typeof c === 'object' ? String(c['#text'] ?? '') : String(c ?? ''),
      }));

      return {
        title,
        link,
        guid,
        descriptionHtml: typeof item.description === 'string' ? item.description : '',
        pubDate: typeof item.pubDate === 'string' ? item.pubDate : undefined,
        categories,
      };
    })
    .filter((i: RssItem | null): i is RssItem => i !== null);
}

function categoryValues(item: RssItem, domain: string): string[] {
  return item.categories.filter((c) => c.domain === domain).map((c) => c.value);
}

/** Strips the description's HTML down to a plain project-code + link line. */
function plainDescriptionSnippet(html: string): string {
  return html
    .replace(/<[^>]+>/g, ' ')
    .replace(/&amp;/g, '&')
    .replace(/&#039;/g, "'")
    .replace(/\s+/g, ' ')
    .trim();
}

const EC_RECORD = /https:\/\/erasmus-plus\.ec\.europa\.eu\/[^\s"'<>]+/;

const GENERIC_EMJM_ELIGIBILITY = [
  "Open to master's-level students from all over the world",
  "Entry requirements (including the required first degree) are set by each programme, so check the programme's own site",
];

const GENERIC_EMJM_REQUIREMENTS = [
  "Apply directly on the programme's own official website (linked above), not through EACEA",
  'Application dates and documents are set by each consortium: confirm them on the programme site',
];

const GENERIC_EMJM_BENEFITS = [
  'Erasmus Mundus scholarships are offered to selected applicants on listed programmes; what they cover is set by each consortium',
];

/** Maps one feed item to a listing. Every fact is read from the feed item;
 *  nothing is invented. The feed carries no per-programme deadline, so the
 *  deadline is descriptive text and the listing never auto-expires. */
export function normalizeErasmusMundusItem(item: RssItem): NormalizedListing {
  const fields = categoryValues(item, 'Studies');
  const duration = categoryValues(item, 'ECTS Duration')[0];
  const universities = categoryValues(item, 'EMJMD Universities');
  const projectSnippet = plainDescriptionSnippet(item.descriptionHtml);
  const ecRecord = EC_RECORD.exec(item.descriptionHtml)?.[0];

  const descriptionParts = [
    'Erasmus Mundus Joint Master programme, funded by the European Union and listed in the official EACEA Erasmus Mundus Catalogue.',
    fields.length > 0 ? `Field(s) of study: ${fields.join(', ')}.` : undefined,
    duration ? `Programme length: ${duration}.` : undefined,
    universities.length > 0 ? `Partner universities include: ${universities.slice(0, 6).join(', ')}${universities.length > 6 ? ', and others' : ''}.` : undefined,
    projectSnippet ? `EACEA record: ${projectSnippet}` : undefined,
  ].filter(Boolean);

  return {
    sourceName: 'erasmus-mundus-catalogue-rss',
    sourceId: item.guid,
    title: item.title,
    organization: 'Erasmus Mundus (European Commission / EACEA) — ' + (universities[0] ?? 'multi-university consortium'),
    country: 'Multiple (EU + partner countries — see programme site)',
    level: "Master's",
    funding: 'Erasmus Mundus scholarships for selected applicants; confirm current funding on the programme site',
    field: fields.length > 0 ? fields.join(', ') : 'See programme site',
    deadline: 'Varies by programme — confirm the exact date on the official programme website',
    description: descriptionParts.join(' '),
    eligibility: GENERIC_EMJM_ELIGIBILITY,
    requirements: GENERIC_EMJM_REQUIREMENTS,
    benefits: GENERIC_EMJM_BENEFITS,
    applicationUrl: item.link,
    officialSourceUrl: ecRecord ?? item.link,
    urlTrust: 'official_feed',
  };
}
