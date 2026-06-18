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
    // CSRF check
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please refresh and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $recap    = $_POST['g-recaptcha-response'] ?? '';

        if (!verify_recaptcha_response($recap)) {
            $errors[] = 'Please confirm you are not a robot.';
        }

        if (!$username || !$password) {
            $errors[] = 'Both username and password are required.';
        }

        if (empty($errors)) {
            $link = db_connect();
            $stmt = mysqli_prepare($link, 'SELECT id, password_hash FROM users WHERE username = ?');
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $user_id, $hash);
            if (mysqli_stmt_fetch($stmt) && password_verify($password, $hash)) {
                session_regenerate_id(true);
                $_SESSION['user_id']  = $user_id;
                $_SESSION['username'] = $username;
                mysqli_stmt_close($stmt);
                mysqli_close($link);
                header('Location: index.php'); exit;
            } else {
                $errors[] = 'Incorrect username or password.';
            }
            mysqli_stmt_close($stmt);
            mysqli_close($link);
        }
    }
}

$extra_css = '
.auth-wrap{min-height:calc(100vh - 120px);display:flex;align-items:center;justify-content:center;padding:2rem 1.2rem}
.auth-card{background:var(--surface);border-radius:var(--radius);box-shadow:0 8px 32px rgba(0,0,0,.1);border:1px solid var(--border);width:100%;max-width:420px;overflow:hidden}
.auth-card-head{background:var(--grad);padding:1.75rem;text-align:center;color:#fff}
.auth-card-head i{font-size:2rem;margin-bottom:.5rem;display:block;opacity:.9}
.auth-card-head h2{margin:0;font-size:1.4rem;font-weight:800}
.auth-card-head p{margin:.35rem 0 0;opacity:.88;font-size:.9rem}
.auth-card-body{padding:1.75rem}
.g-recaptcha{margin:.5rem 0 1rem;transform-origin:left}
';

echo page_head('Login — GamScholarship', '', $extra_css);
echo '<body>';
echo site_header();
?>

<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-card-head">
            <i class="fas fa-graduation-cap"></i>
            <h2>Welcome Back</h2>
            <p>Log in to your GamScholarship account</p>
        </div>
        <div class="auth-card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $e): ?>
                        <div><?php echo htmlspecialchars($e); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="login.php" autocomplete="off">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" class="form-control" type="text" name="username" required
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" class="form-control" type="password" name="password" required>
                </div>

                <?php if (recaptcha_enabled()): ?>
                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars(RECAPTCHA_SITE_KEY); ?>"></div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:1rem;padding:.75rem">
                    <i class="fas fa-sign-in-alt"></i> Log In
                </button>
            </form>

            <div style="text-align:center;margin-top:1.25rem;font-size:.9rem;color:var(--muted)">
                <a href="forget.php" style="color:var(--primary-light)">Forgot your password?</a>
                &nbsp;·&nbsp;
                <a href="register.php" style="color:var(--primary-light)">Create an account</a>
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
