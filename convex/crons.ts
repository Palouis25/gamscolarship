import { cronJobs } from 'convex/server';
import { internal } from './_generated/api';

const crons = cronJobs();

// Hide lapsed listings (public queries already hide them instantly).
crons.daily('expire stale listings', { hourUTC: 3 }, internal.maintenance.expireStaleListings);

// Link-check anything pending (new listings, or ones that failed a check).
crons.interval('verify pending listings', { hours: 6 }, internal.ingestion.verifyPendingListings);

// Re-check verified listings; each is re-checked at most weekly, in batches.
crons.daily('revalidate verified listings', { hourUTC: 4, minuteUTC: 0 }, internal.ingestion.revalidateVerifiedListings);

// Tier 1: Erasmus Mundus Catalogue RSS (official EU feed, no credentials).
crons.interval('sync erasmus mundus catalogue (RSS)', { hours: 84 }, internal.rssIngestion.runErasmusMundusSync);

// Tier 2: official-site sitemap discovery (needs ANTHROPIC_API_KEY; no-ops with a recorded reason without it).
crons.weekly('discover scholarships from official sites', { dayOfWeek: 'wednesday', hourUTC: 5, minuteUTC: 0 }, internal.discovery.runAllDiscovery);

crons.weekly('prune ingestion run log', { dayOfWeek: 'sunday', hourUTC: 2, minuteUTC: 0 }, internal.maintenance.pruneIngestionRuns);

export default crons;
