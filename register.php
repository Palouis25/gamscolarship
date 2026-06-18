<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/layout.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please refresh and try again.';
    } else {
        $username  = trim($_POST['username'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['password'] ?? '';
        $confirm   = $_POST['password_confirm'] ?? '';
        $recap     = $_POST['g-recaptcha-response'] ?? '';

        if (!verify_recaptcha_response($recap)) $errors[] = 'Please verify you are not a robot.';

        if (!$username || !$email || !$password || !$confirm) $errors[] = 'All fields are required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))   $errors[] = 'Invalid email format.';
        elseif ($password !== $confirm)                       $errors[] = 'Passwords do not match.';
        elseif (strlen($password) < 8)                        $errors[] = 'Password must be at least 8 characters.';

        if (empty($errors)) {
            $link = db_connect();
            $stmt = mysqli_prepare($link, 'SELECT id FROM users WHERE username = ? OR email = ?');
            mysqli_stmt_bind_param($stmt, 'ss', $username, $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $errors[] = 'That username or email is already taken.';
                mysqli_stmt_close($stmt);
            } else {
                mysqli_stmt_close($stmt);
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins  = mysqli_prepare($link, 'INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
                mysqli_stmt_bind_param($ins, 'sss', $username, $email, $hash);
                if (mysqli_stmt_execute($ins)) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']  = mysqli_insert_id($link);
                    $_SESSION['username'] = $username;
                    mysqli_stmt_close($ins);
                    mysqli_close($link);
                    header('Location: index.php'); exit;
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                    mysqli_stmt_close($ins);
                }
            }
            mysqli_close($link);
        }
    }
}

$extra_css = '
.auth-wrap{min-height:calc(100vh - 120px);display:flex;align-items:center;justify-content:center;padding:2rem 1.2rem}
.auth-card{background:var(--surface);border-radius:var(--radius);box-shadow:0 8px 32px rgba(0,0,0,.1);border:1px solid var(--border);width:100%;max-width:440px;overflow:hidden}
.auth-card-head{background:var(--grad);padding:1.75rem;text-align:center;color:#fff}
.auth-card-head i{font-size:2rem;margin-bottom:.5rem;display:block;opacity:.9}
.auth-card-head h2{margin:0;font-size:1.4rem;font-weight:800}
.auth-card-head p{margin:.35rem 0 0;opacity:.88;font-size:.9rem}
.auth-card-body{padding:1.75rem}
.g-recaptcha{margin:.5rem 0 1rem;transform-origin:left}
';

echo page_head('Register — GamScholarship', '', $extra_css);
echo '<body>';
echo site_header();
?>

<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-card-head">
            <i class="fas fa-user-plus"></i>
            <h2>Create Your Account</h2>
            <p>Join thousands of Gambian students finding scholarships</p>
        </div>
        <div class="auth-card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $e): ?>
                        <div><?php echo htmlspecialchars($e); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="register.php" autocomplete="off">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" class="form-control" type="text" name="username" required
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" class="form-control" type="email" name="email" required
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password <small style="color:var(--muted);font-weight:400">(min. 8 chars)</small></label>
                    <input id="password" class="form-control" type="password" name="password" required minlength="8">
                </div>
                <div class="form-group">
                    <label for="password_confirm">Confirm Password</label>
                    <input id="password_confirm" class="form-control" type="password" name="password_confirm" required>
                </div>

                <?php if (recaptcha_enabled()): ?>
                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars(RECAPTCHA_SITE_KEY); ?>"></div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:1rem;padding:.75rem">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <div style="text-align:center;margin-top:1.25rem;font-size:.9rem;color:var(--muted)">
                Already have an account?
                <a href="login.php" style="color:var(--primary-light)">Log in here</a>
            </div>
        </div>
    </div>
</div>

<?php if (recaptcha_enabled()): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
<?php echo site_footer(); ?>
</body>
</html>
