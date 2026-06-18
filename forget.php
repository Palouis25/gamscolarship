<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/layout.php';

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please refresh and try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } else {
            $link = db_connect();
            $stmt = mysqli_prepare($link, 'SELECT id FROM users WHERE email = ?');
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $user_id);
            if (mysqli_stmt_fetch($stmt)) {
                mysqli_stmt_close($stmt);
                $token  = bin2hex(random_bytes(32));
                $token_hash = hash('sha256', $token);
                $expiry = date('Y-m-d H:i:s', time() + 3600);
                $upd    = mysqli_prepare($link, 'UPDATE users SET reset_token = ?, token_expiry = ? WHERE id = ?');
                mysqli_stmt_bind_param($upd, 'ssi', $token_hash, $expiry, $user_id);
                mysqli_stmt_execute($upd);
                mysqli_stmt_close($upd);
                $link_url = APP_BASE_URL . "/reset.php?token={$token}";
                $subject  = 'Password Reset — GamScholarship';
                $body     = "Click the link below to reset your password (expires in 1 hour):\n\n{$link_url}\n\nIf you didn't request this, ignore this email.";
                $headers  = "From: no-reply@gamscolaship.online\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";
                @mail($email, $subject, $body, $headers);
                $success = true;
            } else {
                mysqli_stmt_close($stmt);
                // Intentionally vague to prevent email enumeration
                $success = true;
            }
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
';

echo page_head('Forgot Password — GamScholarship', '', $extra_css);
echo '<body>';
echo site_header();
?>

<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-card-head">
            <i class="fas fa-key"></i>
            <h2>Reset Password</h2>
            <p>Enter your email and we'll send a reset link</p>
        </div>
        <div class="auth-card-body">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    If an account exists for that email, a reset link has been sent. Check your inbox (and spam folder).
                </div>
                <div style="text-align:center;margin-top:1rem">
                    <a href="login.php" class="btn btn-primary">Back to Login</a>
                </div>
            <?php else: ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $e): echo htmlspecialchars($e); endforeach; ?>
                    </div>
                <?php endif; ?>
                <form method="post" action="forget.php">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input id="email" class="form-control" type="email" name="email" required
                               placeholder="you@example.com">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.75rem;font-size:1rem">
                        <i class="fas fa-paper-plane"></i> Send Reset Link
                    </button>
                </form>
                <div style="text-align:center;margin-top:1rem;font-size:.9rem;color:var(--muted)">
                    <a href="login.php" style="color:var(--primary-light)">← Back to Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo site_footer(); ?>
</body>
</html>
