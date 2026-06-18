<?php
/**
 * config/layout.php
 * Shared HTML components: <head> snippet, header, footer, AdSense helper.
 *
 * Usage:
 *   require_once __DIR__ . '/config/layout.php';
 *   echo page_head('Page Title', 'meta description');
 *   echo site_header('scholarships');   // active nav key
 *   ...page content...
 *   echo site_footer();
 */

// ── AdSense publisher ID (one place to update) ───────────────────────────────
define('ADSENSE_PUB',  'ca-pub-8176186024669009');
if (!defined('SITE_URL')) {
    define('SITE_URL', getenv('APP_BASE_URL') ?: 'https://gamscolaship.online');
}

// ── Real ad slot IDs — replace these with your actual slot IDs from AdSense ──
// Go to AdSense → Ads → By ad unit → get the data-ad-slot number for each unit.
define('AD_SLOT_LEADERBOARD', getenv('AD_SLOT_LEADERBOARD') ?: '1234567890'); // TODO: replace with real slot ID
define('AD_SLOT_RECTANGLE',   getenv('AD_SLOT_RECTANGLE')   ?: '1234567890'); // TODO: replace with real slot ID
define('AD_SLOT_BANNER',      getenv('AD_SLOT_BANNER')      ?: '1234567890'); // TODO: replace with real slot ID

/** Render an AdSense unit */
function adsense_unit(string $slot = AD_SLOT_RECTANGLE, string $format = 'auto'): string {
    if ($slot === '' || $slot === '1234567890') {
        return '<!-- AdSense slot is not configured yet. Replace the placeholder slot ID in config/layout.php or set the matching environment variable. -->';
    }

    return '
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="' . ADSENSE_PUB . '"
     data-ad-slot="' . htmlspecialchars($slot) . '"
     data-ad-format="' . htmlspecialchars($format) . '"
     data-full-width-responsive="true"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
}

// ── GA4 measurement ID ───────────────────────────────────────────────────────
define('GA4_ID', 'G-BJ6QS8JY97');

// ── Shared CSS variables / design tokens ─────────────────────────────────────
define('SHARED_CSS_VARS', '
:root {
    --primary:      #1a3c6e;
    --primary-light:#2563eb;
    --secondary:    #f59e0b;
    --accent:       #06b6d4;
    --success:      #10b981;
    --error:        #ef4444;
    --bg:           #f0f4f8;
    --surface:      #ffffff;
    --text:         #1e293b;
    --muted:        #64748b;
    --border:       #e2e8f0;
    --shadow:       0 4px 20px rgba(0,0,0,.09);
    --radius:       14px;
    --grad:         linear-gradient(135deg,#1a3c6e 0%,#2563eb 55%,#06b6d4 100%);
}
');

// ── <head> block ─────────────────────────────────────────────────────────────
function page_head(string $title, string $desc = '', string $extra_css = ''): string {
    $desc = $desc ?: 'Find the latest scholarships, grants, and study-abroad opportunities for Gambian students.';
    $ga   = GA4_ID;
    $pub  = ADSENSE_PUB;
    $vars = SHARED_CSS_VARS;
    return <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>{$title}</title>
<meta name="description" content="{$desc}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Open Graph (social sharing) -->
<meta property="og:title"       content="{$title}">
<meta property="og:description" content="{$desc}">
<meta property="og:image"       content="https://gamscolaship.online/og-image.png">
<meta property="og:url"         content="https://gamscolaship.online/">
<meta property="og:type"        content="website">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Source+Serif+4:wght@400;600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<!-- GA4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$ga}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{$ga}');</script>

<!-- AdSense -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={$pub}" crossorigin="anonymous"></script>

<style>
{$vars}
*{box-sizing:border-box}
html,body{margin:0;padding:0;height:100%}
body{font-family:'Outfit',system-ui,sans-serif;color:var(--text);background:var(--bg);-webkit-font-smoothing:antialiased}

/* ── Shared Header ── */
.site-header{background:var(--surface);box-shadow:0 2px 12px rgba(0,0,0,.08);position:sticky;top:0;z-index:200}
.hdr-inner{max-width:1200px;margin:0 auto;padding:.7rem 1.2rem;display:flex;align-items:center;justify-content:space-between;gap:1rem}
.logo{display:flex;align-items:center;gap:.55rem;font-size:1.18rem;font-weight:800;color:var(--primary);text-decoration:none;letter-spacing:-.02em}
.logo i{background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.nav-links{display:flex;align-items:center;gap:.3rem}
.nav-links a{color:var(--text);text-decoration:none;font-weight:500;padding:.45rem .85rem;border-radius:99px;font-size:.95rem;transition:background .18s,color .18s}
.nav-links a:hover{background:#f1f5f9}
.nav-links a.active{background:var(--primary);color:#fff}
.nav-links .btn-login{background:var(--primary);color:#fff!important;padding:.45rem 1.1rem;border-radius:99px}
.nav-links .btn-login:hover{background:var(--primary-light)}
.menu-toggle{display:none;background:none;border:0;cursor:pointer;font-size:1.2rem;color:var(--text);padding:.4rem}

/* ── Shared Footer ── */
.site-footer{background:#0f172a;color:#cbd5e1;padding:2.5rem 1.2rem 1.2rem;margin-top:3rem}
.footer-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1.5rem}
.footer-brand h3{color:#f8fafc;margin:0 0 .5rem;font-size:1.05rem}
.footer-brand p{font-size:.9rem;line-height:1.6;margin:0;color:#94a3b8}
.footer-col h4{color:#f8fafc;font-size:.95rem;margin:0 0 .7rem}
.footer-col a{display:block;color:#94a3b8;text-decoration:none;font-size:.9rem;margin-bottom:.35rem;transition:color .15s}
.footer-col a:hover{color:var(--secondary)}
.footer-bottom{border-top:1px solid #1e293b;padding-top:.9rem;margin-top:1.5rem;text-align:center;font-size:.85rem;color:#475569}

/* ── Utility classes ── */
.container{max-width:1200px;margin:0 auto;padding:0 1.2rem}
.btn{display:inline-flex;align-items:center;gap:.45rem;padding:.6rem 1.2rem;border-radius:99px;font-weight:600;font-size:.95rem;text-decoration:none;border:none;cursor:pointer;transition:transform .15s,box-shadow .15s}
.btn:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.14)}
.btn-primary{background:var(--primary);color:#fff}
.btn-secondary{background:transparent;color:#fff;border:2px solid rgba(255,255,255,.9)}
.btn-amber{background:var(--secondary);color:#1e293b}
.tag{display:inline-block;padding:.2rem .55rem;background:#f1f5f9;color:var(--muted);border-radius:8px;font-size:.75rem;font-weight:500}
.alert{padding:.8rem 1rem;border-radius:10px;font-weight:500;margin-bottom:1rem}
.alert-success{background:#d1fae5;color:#065f46}
.alert-error{background:#fee2e2;color:#991b1b}

/* ── Form elements ── */
.form-group{margin-bottom:1rem}
.form-group label{display:block;font-weight:600;font-size:.9rem;margin-bottom:.35rem}
.form-control{width:100%;padding:.7rem .9rem;border:1.5px solid var(--border);border-radius:10px;font-size:.95rem;font-family:inherit;transition:border-color .18s}
.form-control:focus{outline:none;border-color:var(--primary-light);box-shadow:0 0 0 3px rgba(37,99,235,.1)}

/* ── Cookie banner ── */
.cookie-bar{position:fixed;bottom:1.2rem;left:50%;transform:translateX(-50%);width:min(92vw,860px);background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:0 12px 36px rgba(0,0,0,.16);padding:1.2rem 1.5rem;display:none;align-items:center;gap:1rem;flex-wrap:wrap;z-index:1000}
.cookie-bar p{flex:1;margin:0;font-size:.9rem;color:var(--muted)}
.cookie-bar a{color:var(--primary-light)}
.cookie-actions{display:flex;gap:.6rem;flex-shrink:0}
.cookie-actions button{padding:.5rem 1.1rem;border-radius:8px;border:none;cursor:pointer;font-weight:600;font-size:.85rem}
.cookie-btn-accept{background:var(--secondary);color:#1e293b}
.cookie-btn-decline{background:transparent;border:1.5px solid var(--border)!important;color:var(--text)}

/* ── Responsive ── */
@media(max-width:768px){
    .nav-links{display:none;flex-direction:column;position:absolute;top:100%;left:0;right:0;background:var(--surface);box-shadow:0 8px 24px rgba(0,0,0,.1);padding:1rem 1.2rem;gap:.25rem}
    .nav-links.open{display:flex}
    .menu-toggle{display:flex;align-items:center}
}
{$extra_css}
</style>
HTML;
}

// ── Site header ───────────────────────────────────────────────────────────────
function site_header(string $active = ''): string {
    $links = [
        'home'         => ['/index.php',       'Home'],
        'scholarships' => ['/scholarship.php',  'Scholarships'],
        'verify'       => ['/verification.php', 'Verification'],
        'alerts'       => ['/subscribe.php',    'Alerts'],
        'tips'         => ['/tips.php',         'Tips'],
        'community'    => ['/community.php',    'Community'],
    ];
    $nav = '';
    foreach ($links as $key => [$href, $label]) {
        $cls = ($active === $key) ? ' active' : '';
        $nav .= "<a href=\"{$href}\" class=\"{$cls}\">{$label}</a>";
    }
    return <<<HTML
<header class="site-header">
  <div class="hdr-inner">
    <a href="/index.php" class="logo">
      <i class="fas fa-graduation-cap"></i> GamScholarship
    </a>
    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu" aria-expanded="false">
      <i class="fas fa-bars"></i>
    </button>
    <nav class="nav-links" id="navLinks">
      {$nav}
      <a href="/login.php" class="btn-login">Login</a>
    </nav>
  </div>
</header>
<script>
(function(){
  var btn=document.getElementById('menuToggle'),nav=document.getElementById('navLinks');
  function sync(){if(window.innerWidth>768){nav.classList.remove('open');btn.setAttribute('aria-expanded','false');}}
  btn.addEventListener('click',function(){var o=nav.classList.toggle('open');btn.setAttribute('aria-expanded',o?'true':'false');});
  window.addEventListener('resize',sync);
})();
</script>
HTML;
}

// ── Cookie consent banner ─────────────────────────────────────────────────────
function cookie_banner(): string {
    return <<<HTML
<div class="cookie-bar" id="cookieBar">
  <p>We use cookies for analytics and advertising. See our <a href="/privacy.php">Privacy Policy</a>.</p>
  <div class="cookie-actions">
    <button class="cookie-btn-accept" onclick="setCookieConsent('all')">Accept All</button>
    <button class="cookie-btn-decline" onclick="setCookieConsent('necessary')">Necessary Only</button>
  </div>
</div>
<script>
function setCookieConsent(v){
  document.cookie='cookie_consent='+v+';max-age=2592000;path=/;SameSite=Lax';
  document.getElementById('cookieBar').style.display='none';
}
(function(){
  var c=document.cookie.match(/cookie_consent=([^;]+)/);
  if(!c) document.getElementById('cookieBar').style.display='flex';
})();
</script>
HTML;
}

function guide_verification_notice(string $status, string $checked, string $source_url, string $source_label, array $notes): string {
    $items = '';
    foreach ($notes as $note) {
        $items .= '<li>' . htmlspecialchars($note) . '</li>';
    }

    return '
<div class="guide-section" style="border-left:4px solid var(--success)">
  <h2><i class="fas fa-shield-alt"></i> Verified Update</h2>
  <p><strong>Status:</strong> ' . htmlspecialchars($status) . '</p>
  <p><strong>Last checked:</strong> ' . htmlspecialchars($checked) . '</p>
  <p><strong>Official source:</strong> <a href="' . htmlspecialchars($source_url) . '" target="_blank" rel="noopener">' . htmlspecialchars($source_label) . '</a></p>
  <ul>' . $items . '</ul>
</div>';
}

// ── Site footer ───────────────────────────────────────────────────────────────
function site_footer(): string {
    $year = date('Y');
    return <<<HTML
<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <h3><i class="fas fa-graduation-cap"></i> GamScholarship</h3>
      <p>Empowering Gambian students with timely scholarship opportunities and guidance since 2024.</p>
    </div>
    <div class="footer-col">
      <h4>Explore</h4>
      <a href="/scholarship.php">All Scholarships</a>
      <a href="/verification.php">Verification Policy</a>
      <a href="/subscribe.php">Scholarship Alerts</a>
      <a href="/tips.php">Application Tips</a>
      <a href="/community.php">Community</a>
    </div>
    <div class="footer-col">
      <h4>Account</h4>
      <a href="/login.php">Login</a>
      <a href="/register.php">Register</a>
    </div>
    <div class="footer-col">
      <h4>Legal</h4>
      <a href="/privacy.php">Privacy Policy</a>
      <a href="/index.php#contact">Contact Us</a>
    </div>
  </div>
  <div class="footer-bottom">
    &copy; {$year} GamScholarship.online &mdash; All rights reserved.
  </div>
</footer>
HTML;
}
