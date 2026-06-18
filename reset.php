<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/layout.php';

$errors  = [];
$success = false;
$valid   = false;
$user_id = null;

$token = trim($_GET['token'] ?? '');
if (!$token) {
    $errors[] = 'No reset token provided. Please use the link from your email.';
} else {
    $token_hash = hash('sha256', $token);
    $link = db_connect();
    $stmt = mysqli_prepare($link, 'SELECT id FROM users WHERE reset_token = ? AND token_expiry > NOW()');
    mysqli_stmt_bind_param($stmt, 's', $token_hash);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) === 1) {
        $valid = true;
        mysqli_stmt_bind_result($stmt, $user_id);
        mysqli_stmt_fetch($stmt);
    } else {
        $errors[] = 'This reset link is invalid or has expired. Please request a new one.';
    }
    mysqli_stmt_close($stmt);

    if ($valid && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $errors[] = 'Invalid request. Please refresh and try again.';
            $valid = false;
        } else {
            $new_pass  = $_POST['new_password'] ?? '';
            $conf_pass = $_POST['confirm_password'] ?? '';
            if (!$new_pass || !$conf_pass)         $errors[] = 'Both fields are required.';
            elseif ($new_pass !== $conf_pass)       $errors[] = 'Passwords do not match.';
            elseif (strlen($new_pass) < 8)          $errors[] = 'Password must be at least 8 characters.';

            if (empty($errors)) {
                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $upd  = mysqli_prepare($link, 'UPDATE users SET password_hash=?, reset_token=NULL, token_expiry=NULL WHERE id=?');
                mysqli_stmt_bind_param($upd, 'si', $hash, $user_id);
                mysqli_stmt_execute($upd);
                mysqli_stmt_close($upd);
                $success = true;
                $valid   = false;
            }
        }
    }
    mysqli_close($link);
}

$extra_css = '
.auth-wrap{min-height:calc(100vh - 120px);display:flex;align-items:center;justify-content:center;padding:2rem 1.2rem}
.auth-card{background:var(--surface);border-radius:var(--radius);box-shadow:0 8px 32px rgba(0,0,0,.1);border:1px solid var(--border);width:100%;max-width:420px;overflow:hidden}
.auth-card-head{background:var(--grad);padding:1.75rem;text-align:center;color:#fff}
.auth-card-head i{font-size:2rem;margin-bottom:.5rem;display:block;opacity:.9}
.auth-card-head h2{margin:0;font-size:1.4rem;font-weight:800}
.auth-card-body{padding:1.75rem}
';

echo page_head('Reset Password — GamScholarship', '', $extra_css);
echo '<body>';
echo site_header();
?>

<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-card-head">
            <i class="fas fa-lock"></i>
            <h2>Set New Password</h2>
        </div>
        <div class="auth-card-body">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Your password has been reset successfully!
                </div>
                <div style="text-align:center;margin-top:1rem">
                    <a href="login.php" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Log In Now</a>
                </div>
            <?php elseif ($valid): ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $e): echo htmlspecialchars($e) . '<br>'; endforeach; ?>
                    </div>
                <?php endif; ?>
                <form method="post">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <div class="form-group">
                        <label>New Password <small style="color:var(--muted);font-weight:400">(min. 8 chars)</small></label>
                        <input class="form-control" type="password" name="new_password" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input class="form-control" type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.75rem;font-size:1rem">
                        <i class="fas fa-lock"></i> Reset Password
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $e): echo htmlspecialchars($e); endforeach; ?>
                </div>
                <div style="text-align:center;margin-top:1rem">
                    <a href="forget.php" class="btn btn-primary">Request New Link</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo site_footer(); ?>
</body>
</html>
