<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/newsletter.php';
require_once __DIR__ . '/config/layout.php';

$token = trim($_GET['token'] ?? '');
$ok = false;

if ($token !== '') {
    $link = db_connect();
    $ok = newsletter_unsubscribe($link, $token);
    mysqli_close($link);
}

$extra_css = '.msg-wrap{max-width:680px;margin:0 auto;padding:3rem 1.2rem;text-align:center}.msg-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);padding:2rem}';

echo page_head('Unsubscribe - GamScholarship', 'Unsubscribe from GamScholarship scholarship alerts.', $extra_css);
echo '<body>';
echo site_header();
?>
<main class="msg-wrap">
    <div class="msg-card">
        <h1><?php echo $ok ? 'You are unsubscribed' : 'Unsubscribe link not found'; ?></h1>
        <p style="color:var(--muted)">
            <?php echo $ok ? 'You will no longer receive weekly scholarship alerts.' : 'This link may be invalid or already used.'; ?>
        </p>
        <a href="index.php" class="btn btn-primary">Back to Home</a>
    </div>
</main>
<?php echo site_footer(); ?>
</body>
</html>
