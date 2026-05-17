<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Register — Travel Guide';
$baseUrl   = '../../';
include '../../view/layout/header.php';

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<div class="container">
    <div class="card" style="max-width:500px;margin:0 auto;">
        <h2>Create Account</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $e): ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="../../controller/AuthController.php?action=register" novalidate>

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" placeholder="Your full name">
                <div class="error-msg" id="err-name"></div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="you@example.com">
                <div class="error-msg" id="err-email"></div>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="user"  <?= ($old['role'] ?? '') === 'user'  ? 'selected' : '' ?>>General User</option>
                    <option value="scout" <?= ($old['role'] ?? '') === 'scout' ? 'selected' : '' ?>>Scout</option>
                    <option value="admin" <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password <small>(min 8 characters)</small></label>
                <input type="password" id="password" name="password" placeholder="••••••••">
                <div class="error-msg" id="err-password"></div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••">
                <div class="error-msg" id="err-confirm"></div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">Register</button>
        </form>

        <p style="margin-top:16px;font-size:14px;text-align:center;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    let valid = true;

    const name     = document.getElementById('name');
    const email    = document.getElementById('email');
    const password = document.getElementById('password');
    const confirm  = document.getElementById('confirm_password');

    document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');

    if (name.value.trim() === '') {
        document.getElementById('err-name').textContent = 'Name is required.';
        valid = false;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value.trim())) {
        document.getElementById('err-email').textContent = 'Enter a valid email address.';
        valid = false;
    }

    if (password.value.length < 8) {
        document.getElementById('err-password').textContent = 'Password must be at least 8 characters.';
        valid = false;
    }

    if (password.value !== confirm.value) {
        document.getElementById('err-confirm').textContent = 'Passwords do not match.';
        valid = false;
    }

    if (!valid) e.preventDefault();
});
</script>

<?php include '../../view/layout/footer.php'; ?>
