<?php
/**
 * config/security.php — Security headers & CSRF protection
 * Include at the TOP of every PHP page.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly'  => true,
        'cookie_secure'    => isset($_SERVER['HTTPS']),
        'cookie_samesite'  => 'Lax',
        'use_strict_mode'  => true,
    ]);
}

// ── CSRF helpers ──────────────────────────────────────────────────────────────
function generate_csrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(string $token): bool {
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/** Emit a hidden CSRF input — call inside every <form> */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generate_csrf()) . '">';
}

// ── Security headers ──────────────────────────────────────────────────────────
function set_security_headers(): void {
    if (headers_sent()) return;

    header_remove('X-Powered-By');

    $csp = implode(' ', [
        "default-src 'self' https:;",
        "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://www.googletagmanager.com https://www.google-analytics.com https://www.google.com https://www.gstatic.com https://pagead2.googlesyndication.com https://googleads.g.doubleclick.net https://tpc.googlesyndication.com https://cdn.ywxi.net;",
        "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com;",
        "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com;",
        "img-src 'self' data: https:;",
        "connect-src 'self' https://www.google-analytics.com https://pagead2.googlesyndication.com https://googleads.g.doubleclick.net;",
        "frame-src https://www.google.com https://googleads.g.doubleclick.net https://tpc.googlesyndication.com;",  // reCAPTCHA + AdSense
        "frame-ancestors 'self';",
        "form-action 'self';",
        "base-uri 'self';",
        "object-src 'none';",
    ]);

    $headers = [
        'Content-Security-Policy'   => $csp,
        'X-Content-Type-Options'    => 'nosniff',
        'X-Frame-Options'           => 'SAMEORIGIN',
        'X-XSS-Protection'          => '1; mode=block',
        'Referrer-Policy'           => 'strict-origin-when-cross-origin',
        'Permissions-Policy'        => 'geolocation=(), microphone=(), camera=()',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
    ];

    foreach ($headers as $k => $v) {
        header("$k: $v", true);
    }
}

set_security_headers();
generate_csrf();

// ── Error reporting ────────────────────────────────────────────────────────────
// Log errors but NEVER show them to visitors on a live site.
error_reporting(E_ALL);
ini_set('display_errors', '0');   // CHANGED from 1 → 0
ini_set('log_errors', '1');
