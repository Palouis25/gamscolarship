# GamScholarship

GamScholarship is a scholarship discovery platform. The public site focuses on scholarship listings and sends applicants to the official application page.

## Core rules

- Only active scholarships with `verificationStatus = verified` are public.
- API responses are not treated as proof by themselves.
- Listings go through automated validation, duplicate checks, and URL reachability checks.
- Unverified or rejected listings stay hidden.
- There is no human approval step in the scholarship publishing workflow.
- Source/API keys must stay server-side.

## Tech stack

- React 18
- Vite
- React Router v6
- Convex
- Plain CSS

## Local setup

```bash
npm install
npx convex dev
```

Copy the Convex URL into `.env.local`:

```env
VITE_CONVEX_URL=https://your-deployment.convex.cloud
```

For source setup/seed scripts, set a server-side admin key:

```bash
npx convex env set ADMIN_KEY "choose-a-long-random-string"
```

Then seed the current starter records:

```bash
CONVEX_URL=https://your-deployment.convex.cloud ADMIN_KEY="choose-a-long-random-string" node scripts/seed.mjs
```

The seed script uses the same automated ingestion pipeline as future source adapters.

## Development

```bash
npm run dev
npm run build
```

Convex regenerates `convex/_generated/` when `npx convex dev` runs. Do not edit generated files by hand.

## Adding a real scholarship source

Before connecting an external source, confirm:

1. Its terms permit the intended use and public display.
2. The source provides enough information to identify the scholarship and its official application/source page.
3. The data can be checked automatically.
4. API credentials can remain server-side.

A source adapter should normalize external records into the existing ingestion shape, then pass them through the automated verification pipeline. No source should bypass verification.
