<?php
/**
 * Weekly scholarship digest cron.
 *
 * Run from cPanel cron:
 * /usr/bin/php /home/YOUR_CPANEL_USER/public_html/send_weekly_digest.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo 'Forbidden. Run from cron or command line.';
    exit;
}

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/db.php';

define('DIGEST_FROM', getenv('DIGEST_FROM') ?: 'no-reply@gamscolaship.online');
define('DIGEST_LIMIT', (int)(getenv('DIGEST_LIMIT') ?: 8));

function load_digest_scholarships(): array {
    $file = __DIR__ . '/scholarships.json';
    if (!is_file($file)) return [];

    $payload = json_decode(file_get_contents($file), true);
    $items = $payload['scholarships'] ?? [];

    usort($items, function($a, $b) {
        return strcmp($b['published'] ?? '', $a['published'] ?? '');
    });

    return array_slice($items, 0, DIGEST_LIMIT);
}

function deadline_line(array $s): string {
    $deadline = trim($s['deadline'] ?? 'See official page');
    $ts = strtotime($deadline);
    if ($ts && $ts < strtotime(date('Y-m-d'))) {
        return $deadline . ' (check official page; this may be closed)';
    }
    return $deadline ?: 'See official page';
}

function build_digest_body(array $items, string $unsubscribe_url): string {
    $lines = [
        'GamScholarship Weekly Alerts',
        '',
        'Here are scholarship opportunities to review this week. Always apply through the official link and confirm the deadline before submitting.',
        '',
    ];

    if (!$items) {
        $lines[] = 'No scholarship data is available yet. Please check https://gamscolaship.online/scholarship.php';
    }

    foreach ($items as $i => $s) {
        $lines[] = ($i + 1) . '. ' . ($s['title'] ?? 'Scholarship opportunity');
        $lines[] = '   Level: ' . ($s['level'] ?? 'See official page');
        $lines[] = '   Deadline: ' . deadline_line($s);
        $lines[] = '   Funding: ' . ($s['funding'] ?? 'See official page');
        $lines[] = '   Official link: ' . ($s['url'] ?? APP_BASE_URL . '/scholarship.php');
        $lines[] = '';
    }

    $lines[] = 'More scholarships: ' . APP_BASE_URL . '/scholarship.php';
    $lines[] = 'Verification policy: ' . APP_BASE_URL . '/verification.php';
    $lines[] = '';
    $lines[] = 'Unsubscribe: ' . $unsubscribe_url;

    return implode("\n", $lines);
}

$items = load_digest_scholarships();
$link = db_connect();
$result = mysqli_query($link, "SELECT id, email, unsubscribe_token FROM newsletter_subscribers WHERE status = 'active' ORDER BY id ASC");

if (!$result) {
    fwrite(STDERR, 'Subscriber query failed: ' . mysqli_error($link) . PHP_EOL);
    exit(1);
}

$sent = 0;
$failed = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $unsubscribe_url = APP_BASE_URL . '/unsubscribe.php?token=' . urlencode($row['unsubscribe_token']);
    $body = build_digest_body($items, $unsubscribe_url);
    $headers = "From: " . DIGEST_FROM . "\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    $ok = @mail($row['email'], 'This week\'s verified scholarship alerts', $body, $headers);

    if ($ok) {
        $sent++;
        $stmt = mysqli_prepare($link, 'UPDATE newsletter_subscribers SET last_sent_at = NOW(), updated_at = NOW() WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $row['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $failed++;
        error_log('Weekly digest failed for subscriber id ' . $row['id']);
    }
}

mysqli_free_result($result);
mysqli_close($link);

echo 'Weekly digest complete. Sent: ' . $sent . '. Failed: ' . $failed . PHP_EOL;
