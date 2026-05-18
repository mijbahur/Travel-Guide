<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('location: ../../view/auth/login.php');
    exit;
}

require_once '../../model/user/UserModel.php';

$pageTitle = 'My Profile — Travel Guide';
$baseUrl   = '../../';
include '../../view/layout/header.php';

$user   = getUserById($_SESSION['user_id']);
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<div class="container">

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): echo '<div>' . htmlspecialchars($e) . '</div>'; endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Profile Info -->
    <div class="card">
        <h2>My Profile</h2>

        <div style="display:flex;align-items:center;gap:20px;margin-bottom:20px;">
            <?php if ($user['profile_picture']): ?>
                <img src="../../asset/img/<?= htmlspecialchars($user['profile_picture']) ?>" class="profile-pic" alt="Profile">
            <?php else: ?>
                <div style="width:90px;height:90px;border-radius:50%;background:#1a3c5e;display:flex;align-items:center;justify-content:center;color:#fff;font-size:32px;">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div>
                <strong style="font-size:18px;"><?= htmlspecialchars($user['name']) ?></strong><br>
                <span style="color:#666;font-size:14px;"><?= htmlspecialchars($user['email']) ?></span><br>
                <span class="badge badge-<?= $user['role'] === 'admin' ? 'high' : ($user['role'] === 'scout' ? 'medium' : 'low') ?>" style="margin-top:6px;">
                    <?= ucfirst($user['role']) ?>
                </span>
                <?php if ($user['is_verified']): ?>
                    <span class="badge badge-low" style="margin-top:6px;margin-left:4px;">✓ Verified</span>
                <?php else: ?>
                    <span class="badge badge-medium" style="margin-top:6px;margin-left:4px;">⏳ Pending</span>
                <?php endif; ?>
            </div>
        </div>

        <form id="profileForm" method="POST" action="../../controller/profile/ProfileController.php?action=update" enctype="multipart/form-data" novalidate>

            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>">
                    <div class="error-msg" id="err-name"></div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                    <div class="error-msg" id="err-email"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture <small>(JPG/PNG/GIF/WEBP, max 2MB)</small></label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                <div class="error-msg" id="err-pic"></div>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="card">
        <h2>Change Password</h2>

        <form id="passwordForm" method="POST" action="../../controller/profile/ProfileController.php?action=changePassword" novalidate>

            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" placeholder="••••••••">
                <div class="error-msg" id="err-current"></div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="new_password">New Password <small>(min 8 chars)</small></label>
                    <input type="password" id="new_password" name="new_password" placeholder="••••••••">
                    <div class="error-msg" id="err-new"></div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••">
                    <div class="error-msg" id="err-confirm"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>
</div>

<script>
// Profile form JS validation
document.getElementById('profileForm').addEventListener('submit', function(e) {
    let valid = true;
    document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');

    const name  = document.getElementById('name');
    const email = document.getElementById('email');
    const pic   = document.getElementById('profile_picture');

    if (name.value.trim() === '') {
        document.getElementById('err-name').textContent = 'Name is required.';
        valid = false;
    }
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value.trim())) {
        document.getElementById('err-email').textContent = 'Enter a valid email.';
        valid = false;
    }
    if (pic.files.length > 0) {
        const allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        if (!allowed.includes(pic.files[0].type)) {
            document.getElementById('err-pic').textContent = 'Only JPG, PNG, GIF, WEBP allowed.';
            valid = false;
        }
        if (pic.files[0].size > 2 * 1024 * 1024) {
            document.getElementById('err-pic').textContent = 'File must be under 2MB.';
            valid = false;
        }
    }
    if (!valid) e.preventDefault();
});

// Password form JS validation
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    let valid = true;
    ['err-current','err-new','err-confirm'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = '';
    });

    const current = document.getElementById('current_password');
    const newPass = document.getElementById('new_password');
    const confirm = document.getElementById('confirm_password');

    if (current.value.trim() === '') {
        document.getElementById('err-current').textContent = 'Current password is required.';
        valid = false;
    }
    if (newPass.value.length < 8) {
        document.getElementById('err-new').textContent = 'New password must be at least 8 characters.';
        valid = false;
    }
    if (newPass.value !== confirm.value) {
        document.getElementById('err-confirm').textContent = 'Passwords do not match.';
        valid = false;
    }
    if (!valid) e.preventDefault();
});
</script>

<?php include '../../view/layout/footer.php'; ?>
