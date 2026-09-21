import { cronJobs } from 'convex/server';
import { internal } from './_generated/api';

const crons = cronJobs();

// Runs once a day to move any scholarship/opportunity with a passed
// ISO deadline from "active" to "expired". See convex/maintenance.ts
// for what this does and does not do.
crons.daily(
  'expire stale listings',
  { hourUTC: 3 },
  internal.maintenance.expireStaleListings
);

// Re-checks listings that are waiting for their first automated link
// verification. Unverified listings never appear publicly.
crons.interval(
  'retry pending verification',
  { minutes: 360 },
  internal.ingestion.recheckPendingListings
);

// Re-checks link reachability for every already-verified listing once a
// week, demoting anything whose application/source link has gone dead back
// to pending so automated verification can retry. See convex/ingestion.ts.
crons.weekly(
  'revalidate verified listings',
  { dayOfWeek: 'monday', hourUTC: 4, minuteUTC: 0 },
  internal.ingestion.revalidateVerifiedListings
);


export default crons;
