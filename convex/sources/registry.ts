// The complete, code-defined list of automated sources. The cron jobs
// register these rows themselves (see sourceRegistry.ensureSources), so a
// fresh deployment needs no manual setup script. Pausing a source = set
// `enabled: false` on its row in the Convex dashboard; the code never
// re-enables a source it did not just create.

import { OFFICIAL_SITE_SOURCES } from './officialSiteSources';

export const ERASMUS_SOURCE = 'erasmus-mundus-catalogue-rss';

export type RegistrySource = {
  name: string;
  label: string;
  kind: 'manual' | 'api' | 'official_site';
  trustLevel: 'high' | 'medium' | 'low';
  notes: string;
};

export const SOURCE_REGISTRY: RegistrySource[] = [
  { name: 'manual', label: 'Curated entries (convex/seedData.ts)', kind: 'manual', trustLevel: 'high', notes: 'Hand-curated; still link-checked automatically.' },
  { name: ERASMUS_SOURCE, label: 'Erasmus Mundus Catalogue (EACEA / European Commission) — official RSS feed', kind: 'api', trustLevel: 'high', notes: 'eacea.ec.europa.eu/node/253/rss_en — no credentials required' },
  ...OFFICIAL_SITE_SOURCES.map((s) => ({
    name: s.slug,
    label: s.label,
    kind: 'official_site' as const,
    trustLevel: s.trustLevel,
    notes: `${s.domain} — sitemap discovery + Anthropic extraction (needs ANTHROPIC_API_KEY)`,
  })),
];
