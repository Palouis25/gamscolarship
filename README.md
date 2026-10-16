# GamScholarship

A global scholarship discovery site. React + Vite + React Router frontend, Convex backend, plain CSS, deployed on Vercel. **Scholarships only**: no jobs, internships, fellowships, accounts or payments.

## How listings get published (fully automatic)

```
source (RSS / official sitemap / curated seed)
  -> adapter normalises to one shape            convex/sources/*
  -> static validation                          convex/validation.ts
       required fields, https + safe URL, recognised official host,
       deadline not past, scholarships-only content, template-farm check
  -> upsert by (sourceName, sourceId)           convex/ingestion.ts
       duplicates across sources are detected (convex/duplicates.ts);
       re-runs update in place, never re-insert
  -> status "pending"  (never public)
  -> automatic link check                       convex/lib/linkCheck.ts
       2xx = verified; 404/410 = dead; 5xx/429/timeout = retry later;
       redirect to an unrecognised domain = rejected
  -> status "verified" -> visible on the site
```

* The public queries (`convex/scholarships.ts`) return only `active` + `verified` rows whose deadline has not passed, and strip all pipeline-internal fields. There are **no public mutations** and **no admin approval queue**.
* **"Verified" means:** the listing passed validation, its application URL is on a recognised official domain (or comes from an official feed), and the link was live when last checked (re-checked at most weekly). It does **not** mean every detail (deadline, eligibility) was fact-checked, so the UI always tells students to confirm on the official page.
* One failed re-check never hides a good listing; it is demoted to `pending` only after repeated failures (a 404 counts double). Listings that never verify within 14 days are rejected.
* Deadlines: a strict calendar date expires automatically. Descriptive deadlines ("Varies by programme") never expire and are shown as written.

## Sources and their status

| Source | Needs | Status |
|---|---|---|
| **Erasmus Mundus Catalogue RSS** (EACEA, European Commission) `eacea.ec.europa.eu/node/253/rss_en` | nothing | Implemented, runs every 84 h. Parser/adapter/pipeline tested against a fixture of the feed format and a mocked network. **Not tested against the live feed** (the build environment had no outbound access). Pagination (`?page=N`) is unconfirmed; the loop stops safely if it is not supported. The feed has no per-programme deadline, so those listings show "Varies by programme". |
| **Official-site discovery** (Chevening, DAAD, Mastercard Foundation, CSC UK, Gates Cambridge, Fulbright, Erasmus+) | `ANTHROPIC_API_KEY` | Implemented, runs weekly: robots.txt + sitemap -> candidate pages -> Anthropic extraction -> same pipeline. **Not tested against the live sites.** Without the key each run records a clear failure on the `sources` row. Replaces the old Google Custom Search step: that API is closed to new customers and shuts down on 2027-01-01. |
| **Curated seed** (`convex/seedData.ts`, 12 programmes) | nothing | Run once with `seed:run`. Entries with `isSample: true` are shown with a "details not confirmed" notice until you re-check them against the official page and flip the flag. |

Check health in the Convex dashboard: table `sources` (`lastRunAt`, `lastError`, `consecutiveFailures`, `lastRunStats`) and `ingestionRuns`.

## Environment variables

| Where | Name | Purpose |
|---|---|---|
| Vercel / `.env.local` | `VITE_CONVEX_URL` | Convex deployment URL. **Public.** Must be your production deployment on Vercel. |
| Convex env | `ANTHROPIC_API_KEY` | Optional. Enables official-site discovery. Secret. |
| Convex env | `ANTHROPIC_MODEL` | Optional. Overrides the default extraction model. |

Never put secrets in `.env*` files or `VITE_` variables. `.env.local` is git-ignored; `.env.example` holds placeholders only.

## Commands

```bash
npm ci                 # install from the lockfile
npm run check          # lint + typecheck + tests + build
npm run dev            # frontend (needs VITE_CONVEX_URL)
npx convex dev         # backend dev loop (regenerates convex/_generated)
```

## Deploy

```bash
# 1. Backend -> production Convex deployment
npx convex deploy
npx convex run seed:run --prod                       # curated entries
npx convex run rssIngestion:runErasmusMundusSync --prod   # first RSS pull (also on cron)
npx convex env set ANTHROPIC_API_KEY "<key>" --prod  # optional

# 2. Frontend: set VITE_CONVEX_URL (production URL) in Vercel, push to GitHub; Vercel builds `npm run build`.
```

One-time clean-up of the retired features:

```bash
npx convex run maintenance:purgeLegacyOpportunities --prod   # empties the old `opportunities` table
# then delete the "LEGACY opportunities" table block from convex/schema.ts and redeploy
npx convex env remove ADMIN_KEY --prod                       # no longer used
```

## Adding a provider

1. Confirm its domain is the provider's own official site.
2. Add the domain to `OFFICIAL_DOMAINS` in `convex/lib/urlSafety.ts` (otherwise its links are rejected).
3. For a feed/API, write an adapter in `convex/sources/` that returns the normalised shape (`sources/types.ts`), register it in `sources/registry.ts`, and add a cron in `convex/crons.ts`. For a site with a sitemap, add it to `sources/officialSiteSources.ts`.

Only integrate sources whose terms permit this use.

## Known limitations

* Live source fetches are untested (see table). Check `sources.lastError` after the first runs.
* `npm audit` reports two moderate `react-router` advisories (open redirect via crafted `<Link>` targets; SSR-only deserialisation). Fixing them needs a major upgrade (v7). Neither applies here: the app never navigates to user-supplied URLs and does no SSR. Dev-only `vite`/`esbuild` advisories affect the local dev server only.
* Link checks cannot detect a page whose content became wrong while staying reachable.
