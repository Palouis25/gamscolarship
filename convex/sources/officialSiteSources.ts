// Curated allowlist for the "official site" discovery adapter
// (convex/discovery.ts). Nothing outside this list is ever fetched by
// discovery. Each domain is the provider's own site, which avoids the
// SEO-farm aggregators that polluted the legacy PHP cache.
//
// Add a source only after independently confirming the domain is the
// provider's own official site, and add the domain to OFFICIAL_DOMAINS in
// convex/lib/urlSafety.ts too, otherwise its links cannot pass verification.

export type OfficialSiteSource = {
  slug: string; // sourceName stored on scholarships rows
  label: string;
  domain: string; // hostname; sitemap pages must be on this domain
  trustLevel: 'high' | 'medium' | 'low';
  notes: string;
};

export const OFFICIAL_SITE_SOURCES: OfficialSiteSource[] = [
  { slug: 'chevening-official-site', label: 'Chevening Scholarships (UK FCDO) — official site', domain: 'chevening.org', trustLevel: 'high', notes: 'UK government scholarship programme.' },
  { slug: 'daad-official-site', label: 'DAAD — German Academic Exchange Service — official site', domain: 'daad.de', trustLevel: 'high', notes: 'German government academic exchange agency.' },
  { slug: 'mastercard-foundation-official-site', label: 'Mastercard Foundation Scholars Program — official site', domain: 'mastercardfdn.org', trustLevel: 'high', notes: 'Foundation-run programme.' },
  { slug: 'cscuk-official-site', label: 'Commonwealth Scholarship Commission in the UK — official site', domain: 'cscuk.fcdo.gov.uk', trustLevel: 'high', notes: 'UK government body (FCDO).' },
  { slug: 'gatescambridge-official-site', label: 'Gates Cambridge Scholarship — official site', domain: 'gatescambridge.org', trustLevel: 'high', notes: 'University of Cambridge / Gates Cambridge Trust.' },
  { slug: 'fulbright-official-site', label: 'Fulbright Foreign Student Program — official site', domain: 'foreign.fulbrightonline.org', trustLevel: 'high', notes: 'US Department of State exchange programme.' },
  { slug: 'erasmusplus-official-site', label: 'Erasmus+ — official EU site', domain: 'erasmus-plus.ec.europa.eu', trustLevel: 'high', notes: 'European Commission official Erasmus+ site.' },
];
