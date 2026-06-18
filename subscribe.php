<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/newsletter.php';
require_once __DIR__ . '/config/layout.php';

$message = null;
$selected = $_POST['interests'] ?? ['all'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $message = ['type' => 'error', 'text' => 'Invalid request. Please refresh and try again.'];
    } else {
        $email = trim($_POST['email'] ?? $_POST['subscriber_email'] ?? '');
        $selected = (array)($_POST['interests'] ?? ['all']);
        $link = db_connect();
        $result = newsletter_subscribe($link, $email, $selected, 'subscribe-page');
        mysqli_close($link);
        $message = ['type' => $result['ok'] ? 'success' : 'error', 'text' => $result['message']];
        if ($result['ok']) {
            $selected = ['all'];
            $_POST = [];
        }
    }
}

$extra_css = '
.sub-hero{background:var(--grad);color:#fff;padding:2.6rem 1.2rem;text-align:center}
.sub-hero h1{margin:0 0 .5rem;font-size:clamp(1.6rem,4vw,2.5rem);font-weight:800}
.sub-hero p{margin:0 auto;max-width:650px;opacity:.92;line-height:1.6}
.sub-wrap{max-width:820px;margin:0 auto;padding:2rem 1.2rem}
.sub-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);padding:1.5rem}
.interest-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:.65rem;margin:.8rem 0 1rem}
.interest{display:flex;gap:.5rem;align-items:center;background:var(--bg);border:1px solid var(--border);padding:.65rem .75rem;border-radius:10px;font-size:.9rem;font-weight:600}
.privacy-note{color:var(--muted);font-size:.85rem;line-height:1.55;margin-top:.9rem}
';

echo page_head('Scholarship Alerts - GamScholarship', 'Subscribe for weekly verified scholarship updates and deadline reminders for Gambian students.', $extra_css);
echo '<body>';
echo site_header();
?>

<section class="sub-hero">
    <h1>Weekly Scholarship Alerts</h1>
    <p>Get verified scholarship opportunities, closing deadlines, and application reminders by email. Free, practical, and focused on Gambian students.</p>
</section>

<main class="sub-wrap">
    <div class="sub-card">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message['type'] === 'success' ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message['text']); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="subscribe.php">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label for="email">Email address</label>
                <input id="email" class="form-control" type="email" name="email" placeholder="you@example.com" required
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <label style="display:block;font-weight:700;margin:.8rem 0 .35rem">What should we send you?</label>
            <div class="interest-grid">
                <?php
                $options = [
                    'all' => 'All scholarships',
                    'masters' => 'Masters',
                    'phd' => 'PhD',
                    'undergraduate' => 'Undergraduate',
                    'fully-funded' => 'Fully funded',
                    'closing-soon' => 'Closing soon',
                ];
                foreach ($options as $value => $label):
                    $checked = in_array($value, (array)$selected, true) ? 'checked' : '';
                ?>
                    <label class="interest">
                        <input type="checkbox" name="interests[]" value="<?php echo htmlspecialchars($value); ?>" <?php echo $checked; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <button class="btn btn-primary" type="submit" style="width:100%;justify-content:center">
                <i class="fas fa-bell"></i> Subscribe for Alerts
            </button>
            <p class="privacy-note">Students can unsubscribe from any alert email. We only use this email for scholarship updates and deadline reminders.</p>
        </form>
    </div>
</main>

<?php echo site_footer(); ?>
<?php echo cookie_banner(); ?>
</body>
</html>
