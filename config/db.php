<?php
/**
 * config/db.php — Database credentials
 *
 * SECURITY: Move this file ABOVE your public_html folder if your host allows it.
 * e.g. reference it as: require_once dirname(__DIR__, 2) . '/private/db.php';
 *
 * If you must keep it inside public_html, add this to your .htaccess:
 *   <Files "db.php">
 *     Order allow,deny
 *     Deny from all
 *   </Files>
 */

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: '');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: '');

function db_connect(): mysqli {
    if (DB_USER === '' || DB_NAME === '') {
        error_log('Database environment variables are not configured.');
        die('A database error occurred. Please try again later.');
    }

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$link) {
        error_log('DB connection failed: ' . mysqli_connect_error());
        die('A database error occurred. Please try again later.');
    }
    mysqli_set_charset($link, 'utf8mb4');
    return $link;
}
