# AdSense Setup

Your publisher ID is the `ca-pub-...` value. Your ad slot IDs are the shorter numeric `data-ad-slot` values for each ad unit.

The site currently has placeholder slot IDs:

```php
AD_SLOT_LEADERBOARD=1234567890
AD_SLOT_RECTANGLE=1234567890
AD_SLOT_BANNER=1234567890
```

Ads will not render from those placeholders. Replace them with real slot IDs from your AdSense account.

## Get the Correct Slot IDs

1. Sign in to Google AdSense.
2. Open **Ads**.
3. Open **By ad unit**.
4. Create or open the ad unit you want.
5. Copy the `data-ad-slot` number from the ad unit code.

Example:

```html
data-ad-client="ca-pub-8176186024669009"
data-ad-slot="9876543210"
```

In that example, `9876543210` is the value you need.

## Where to Put Them

Best option: set these environment variables on your host:

```text
AD_SLOT_LEADERBOARD=your_real_leaderboard_slot
AD_SLOT_RECTANGLE=your_real_rectangle_slot
AD_SLOT_BANNER=your_real_banner_slot
```

If your host cannot set environment variables, edit `config/layout.php` on the server copy and replace the fallback `1234567890` values.

## Also Check

- Your domain must be approved in AdSense.
- `ads.txt` must be reachable at `https://gamscolaship.online/ads.txt`.
- Ads usually will not show on localhost.
- Browser ad blockers can hide ads during testing.
- New ad units can take time before they start serving.
