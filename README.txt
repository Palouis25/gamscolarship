╔══════════════════════════════════════════════════════════════╗
║         GamScholarship — COMPLETE UPDATED SITE               ║
║                    Deployment Guide                           ║
╚══════════════════════════════════════════════════════════════╝

FILES IN THIS ZIP
─────────────────
public_html/
├── index.php            ← Homepage (reads from JSON)
├── scholarship.php      ← Full database page (reads from JSON)
├── scraper.php          ← Auto-fetches new scholarships daily
├── login.php            ← Unified design + CSRF
├── register.php         ← Unified design + CSRF
├── forget.php           ← Unified design + CSRF
├── reset.php            ← Unified design + CSRF
├── tips.php             ← Enriched with tips, checklist, resources
├── community.php        ← Fully built (was empty before)
├── privacy.php          ← Updated, correct branding + year
├── ads.txt              ← AdSense publisher file
├── robots.txt           ← Updated (blocks config/ from crawlers)
├── sitemap.xml          ← Updated (all pages, correct dates)
├── htaccess             ← Rename to .htaccess after upload
└── config/
    ├── db.php           ← DB credentials (one place)
    ├── security.php     ← CSRF + security headers
    └── layout.php       ← Shared header, footer, AdSense units


STEP 1 — UPLOAD
────────────────
Upload ALL files to your public_html directory via FTP or
cPanel File Manager. Keep the config/ folder structure.


STEP 2 — RENAME .htaccess
──────────────────────────
The file is named "htaccess" (no dot) to survive the zip.
After uploading, rename it to:   .htaccess


STEP 3 — FIX YOUR AD SLOT IDs  ⚠️ DO THIS OR ADS WON'T SHOW
──────────────────────────────────────────────────────────────
Open:  config/layout.php

Find these 3 lines and replace with your REAL slot IDs:

    define('AD_SLOT_LEADERBOARD', '1234567890');  ← replace
    define('AD_SLOT_RECTANGLE',   '1234567890');  ← replace
    define('AD_SLOT_BANNER',      '1234567890');  ← replace

To find your real slot IDs:
  1. Go to adsense.google.com
  2. Click Ads → By ad unit
  3. Each unit shows a data-ad-slot number — copy it here
  (Your publisher ID pub-8176186024669009 is already correct)


STEP 4 — RUN SCRAPER FOR THE FIRST TIME
─────────────────────────────────────────
This creates scholarships.json and populates your site.

Option A — cPanel Terminal:
  cd public_html
  php scraper.php

Option B — Browser (temporary):
  Visit: https://gamscolaship.online/scraper.php
  ⚠ Then block browser access (see Step 5)

You will see output like:
  ✓ opportunitydesk.org: 14 entries
  ✓ scholars4dev.com: 9 entries
  ✅ Saved 55 unique scholarships → scholarships.json


STEP 5 — SET UP DAILY CRON JOB
────────────────────────────────
1. cPanel → Cron Jobs
2. Set to: Once Daily (3:00 AM recommended)
3. Command:
   /usr/bin/php /home/ficibank/public_html/scraper.php

   (Replace "ficibank" with your actual cPanel username)

After this, your site updates itself every day. No work needed.


STEP 6 — DELETE OLD DUPLICATE FILE
─────────────────────────────────────
In cPanel File Manager, delete this file:
  public_html/privacy (1).php
It's a leftover duplicate — it should not be there.


STEP 7 — PROTECT scraper.php FROM BROWSER
───────────────────────────────────────────
After first run, add to your .htaccess:

  <Files "scraper.php">
    Order allow,deny
    Deny from all
  </Files>

This prevents anyone from triggering the scraper via browser.
The cron job will still run it fine.


WHAT WAS FIXED
───────────────
✅ DB credentials — now in config/db.php only (was in 5 files)
✅ display_errors OFF — was exposing PHP errors to visitors
✅ CSRF — now active on ALL forms (was built but never used)
✅ Unified design — all pages match (login, register, tips, etc.)
✅ "Floriance" branding removed from scholarship.php
✅ Footer typo fixed — "GamScholarship" (was "Gamscolaship")
✅ Copyright year updated to 2026
✅ AdSense — centralised in layout.php (fix once, works everywhere)
✅ Scholarships — now auto-loaded from JSON (unlimited, grows daily)
✅ community.php — fully built (was empty)
✅ Open Graph meta tags — WhatsApp/Facebook sharing now works
✅ sitemap.xml — updated with all pages and correct dates
✅ robots.txt — blocks config/ folder from Google crawlers
✅ pagination on scholarship.php (18 per page)
✅ NEW badge on scholarships added in last 7 days


EXPECTED SCHOLARSHIP GROWTH
──────────────────────────────
Day 1 (after first scraper run): 50–80 scholarships
After 1 month of daily runs:     200–400+
After 6 months:                  1,000+

Every scholarship posted to OpportunityDesk, Scholars4Dev,
ScholarshipTab, and AfricanScholarships appears on your site
within 24 hours — automatically.
