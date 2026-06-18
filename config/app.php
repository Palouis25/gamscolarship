<?php
/**
 * config/app.php
 * Non-database application settings.
 *
 * On production, set these values from your host environment where possible.
 * If your host does not support environment variables, replace the empty
 * fallback values below on the server copy only.
 */

define('APP_BASE_URL', getenv('APP_BASE_URL') ?: 'https://gamscolaship.online');
define('RECAPTCHA_SITE_KEY', getenv('RECAPTCHA_SITE_KEY') ?: '');
define('RECAPTCHA_SECRET_KEY', getenv('RECAPTCHA_SECRET_KEY') ?: '');

function recaptcha_enabled(): bool {
    return RECAPTCHA_SITE_KEY !== '' && RECAPTCHA_SECRET_KEY !== '';
}

function verify_recaptcha_response(string $response): bool {
    if (!recaptcha_enabled()) {
        return true;
    }

    if ($response === '') {
        return false;
    }

    $query = http_build_query([
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $response,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);

    $verify = @file_get_contents('https://www.google.com/recaptcha/api/siteverify?' . $query);
    $data = $verify ? json_decode($verify, true) : null;

    return is_array($data) && !empty($data['success']);
}
