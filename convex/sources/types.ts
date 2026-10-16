// Every source adapter normalizes its upstream data to this shape before it
// reaches the ingestion pipeline (convex/ingestion.ts). GamScholarship lists
// scholarships only.

export type NormalizedScholarship = {
  sourceName: string; // must match a row in the `sources` table
  sourceId?: string; // upstream's own stable id for this record
  title: string;
  organization: string;
  country: string;
  level: string;
  funding: string;
  field: string;
  deadline: string; // ISO date, or descriptive text
  description: string;
  eligibility: string[];
  requirements: string[];
  benefits: string[];
  applicationUrl: string;
  officialSourceUrl?: string;
  /** Set only by adapters whose upstream is itself an official feed; lets
   *  the application URL live on a partner site outside OFFICIAL_DOMAINS. */
  urlTrust?: 'official_feed';
  isSample?: boolean;
};

export type NormalizedListing = NormalizedScholarship;
