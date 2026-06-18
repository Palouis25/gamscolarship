# GamScholarship Deployment Checklist

Use the contents of this folder as your `public_html` upload. Do not upload the older duplicate files from the project root.

## Before Upload

- Rotate the database password in cPanel because the old value existed in a project archive.
- Rotate the reCAPTCHA secret in Google reCAPTCHA for the same reason.
- Set these production values on your host:
  - `DB_HOST`
  - `DB_USER`
  - `DB_PASS`
  - `DB_NAME`
  - `APP_BASE_URL`
  - `RECAPTCHA_SITE_KEY`
  - `RECAPTCHA_SECRET_KEY`
  - `AD_SLOT_LEADERBOARD`
  - `AD_SLOT_RECTANGLE`
  - `AD_SLOT_BANNER`

If your host cannot set environment variables, edit the matching fallback values in `config/db.php`, `config/app.php`, and `config/layout.php` on the server copy only.

## Upload

- Upload every file in this folder to `public_html`.
- Rename `htaccess` to `.htaccess` after upload.
- Confirm `config/`, `scraper.php`, `scraper.log`, and `scholarships.json` are not directly reachable in the browser.

## Database

Your `users` table must include at least:

```sql
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(100) NOT NULL UNIQUE,
email VARCHAR(255) NOT NULL UNIQUE,
password_hash VARCHAR(255) NOT NULL,
reset_token CHAR(64) NULL,
token_expiry DATETIME NULL
```

`reset_token` stores a SHA-256 hash, not the raw email link token.

Import `database.sql` as well. It creates the `newsletter_subscribers` table used for scholarship alerts and deadline reminders.

## Scraper

Run the scraper from cron only:

```bash
/usr/bin/php /home/YOUR_CPANEL_USER/public_html/scraper.php
```

Recommended schedule: once daily around 3:00 AM.

## Weekly Scholarship Alerts

After importing `database.sql`, add a weekly cron job:

```bash
/usr/bin/php /home/YOUR_CPANEL_USER/public_html/send_weekly_digest.php
```

Recommended schedule: once weekly, for example Saturday morning. The script emails active subscribers using the current `scholarships.json` data and includes unsubscribe links.

## Final Checks

- Visit `/`, `/scholarship.php`, `/login.php`, `/register.php`, `/forget.php`, `/tips.php`, `/community.php`, and `/privacy.php`.
- Submit the login/register forms only after the database values are configured.
- Submit `sitemap.xml` in Google Search Console.
- Add the real AdSense slot IDs before expecting ads to show.
