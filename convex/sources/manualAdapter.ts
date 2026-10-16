import type { NormalizedListing } from './types';

/** The "manual" source: curated entries (see convex/seedData.ts). They run
 *  through the same validation / link-check pipeline as any automated source. */
export function normalizeManualEntry(raw: Record<string, any>): NormalizedListing {
  return {
    sourceName: 'manual',
    sourceId: raw.sourceId,
    title: raw.title,
    organization: raw.organization,
    country: raw.country,
    level: raw.level,
    funding: raw.funding,
    field: raw.field,
    deadline: raw.deadline,
    description: raw.description,
    eligibility: raw.eligibility ?? [],
    requirements: raw.requirements ?? [],
    benefits: raw.benefits ?? [],
    applicationUrl: raw.applicationUrl,
    officialSourceUrl: raw.officialSourceUrl,
    isSample: raw.isSample === true,
  };
}
