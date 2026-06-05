<?php
/** Login, forgot password, and reset password views — unauthenticated only */
$view = $view ?? 'login';
$recoveryEmailHint = adminGetRecoveryEmail($site_settings ?? []);
?>
<div class="admin-login-page">
    <div class="admin-login-card">
        <div class="admin-login-brand">
            <i class="bi bi-shield-lock"></i>
            <div>
                <h1 class="brand h4 mb-0">Site Admin</h1>
                <p class="text-muted small mb-0"><?= htmlspecialchars(SITE_NAME) ?></p>
            </div>
        </div>

        <?php if ($view === 'login'): ?>
            <p class="text-muted small mt-3 mb-3">Manage maintenance, services, about & contact content.</p>
            <?php if (!empty($loginLockedOut)): ?>
                <div class="alert alert-warning py-2">
                    Too many failed login attempts. Please wait <?= (int) adminLoginLockoutMinutesRemaining($pdo) ?> minute(s) before trying again.
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if (!empty($loginSuccess)): ?><div class="alert alert-success py-2"><?= htmlspecialchars($loginSuccess) ?></div><?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required autofocus autocomplete="username" <?= !empty($loginLockedOut) ? 'disabled' : '' ?>>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required autocomplete="current-password" <?= !empty($loginLockedOut) ? 'disabled' : '' ?>>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 mb-3" <?= !empty($loginLockedOut) ? 'disabled' : '' ?>>Login</button>
                <div class="text-center">
                    <a href="?view=forgot" class="small text-decoration-none">Forgot password?</a>
                </div>
            </form>

        <?php elseif ($view === 'forgot'): ?>
            <p class="text-muted small mt-3 mb-3">Enter the admin email address on file. We will send a reset link if it matches.</p>
            <?php if (!empty($error)): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if (!empty($loginSuccess)): ?>
                <div class="alert alert-success py-2"><?= htmlspecialchars($loginSuccess) ?></div>
                <?php if (!empty($_SESSION['admin_dev_reset_link']) && defined('IS_LOCAL') && IS_LOCAL): ?>
                <div class="alert alert-warning py-2 small">
                    <strong>Local dev:</strong> Email may not be configured. Use this link:<br>
                    <a href="<?= htmlspecialchars($_SESSION['admin_dev_reset_link']) ?>"><?= htmlspecialchars($_SESSION['admin_dev_reset_link']) ?></a>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($recoveryEmailHint !== ''): ?>
                <p class="hint mb-3">Use the same email configured in Contact settings (<?= htmlspecialchars($recoveryEmailHint) ?>).</p>
            <?php else: ?>
                <p class="hint mb-3">Your developer must set a contact email once before password recovery works.</p>
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="admin_public_csrf" value="<?= htmlspecialchars(adminPublicCsrfToken()) ?>">
                <div class="mb-3">
                    <label>Admin email</label>
                    <input type="email" name="email" class="form-control" required autofocus autocomplete="email" placeholder="admin@example.com">
                </div>
                <button type="submit" name="request_reset" class="btn btn-primary w-100 mb-3">Send reset link</button>
                <div class="text-center">
                    <a href="?" class="small text-decoration-none">← Back to login</a>
                </div>
            </form>

        <?php elseif ($view === 'reset'): ?>
            <p class="text-muted small mt-3 mb-3">Choose a strong admin password. All requirements below must be met.</p>
            <?php if (!empty($error)): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if (empty($resetTokenValid)): ?>
                <div class="alert alert-danger py-2">This reset link is invalid or has expired.</div>
                <div class="text-center mt-3">
                    <a href="?view=forgot" class="btn btn-outline-primary btn-sm">Request a new link</a>
                    <a href="?" class="btn btn-link btn-sm">Back to login</a>
                </div>
            <?php else: ?>
                <form method="post" class="js-admin-password-form">
                    <input type="hidden" name="admin_public_csrf" value="<?= htmlspecialchars(adminPublicCsrfToken()) ?>">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($resetToken ?? '') ?>">
                    <div class="mb-3">
                        <label>New password</label>
                        <input type="password" name="new_password" class="form-control js-admin-new-password" required autocomplete="new-password">
                    </div>
                    <?php include __DIR__ . '/password-requirements.php'; ?>
                    <div class="mb-2">
                        <label>Confirm new password</label>
                        <input type="password" name="confirm_password" class="form-control js-admin-confirm-password" required autocomplete="new-password">
                    </div>
                    <p class="js-admin-password-match admin-password-match small mb-3" aria-live="polite"></p>
                    <button type="submit" name="reset_password" class="btn btn-primary w-100 mb-3 js-admin-password-submit" disabled>Update password</button>
                    <div class="text-center">
                        <a href="?" class="small text-decoration-none">← Back to login</a>
                    </div>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
