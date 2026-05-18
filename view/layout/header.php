<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $rootPath = __DIR__ . '/../../';
    require_once $rootPath . 'model/user/UserModel.php';
    $tokenHash = hash('sha256', $_COOKIE['remember_token']);
    $user = getUserByRememberToken($tokenHash);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['is_verified'] = $user['is_verified'];
    }
}

$loggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? '';
$verified = $_SESSION['is_verified'] ?? 0;
$name = $_SESSION['name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Travel Guide') ?></title>
    <link rel="stylesheet" href="<?= $baseUrl ?? '../' ?>asset/css/style.css">
</head>

<body>

    <div class="navbar">
        <a class="brand" href="<?= $baseUrl ?? '../' ?>index.php">🌍 Travel Guide</a>
        <nav>
            <?php if (!$loggedIn): ?>
                <a href="<?= $baseUrl ?? '../' ?>view/auth/login.php">Login</a>
                <a href="<?= $baseUrl ?? '../' ?>view/auth/register.php" class="btn-nav">Register</a>

            <?php elseif (!$verified): ?>
                <span style="color:#cce0f5;font-size:13px;">Hi,
                    <?= htmlspecialchars($name) ?>
                </span>
                <a href="<?= $baseUrl ?? '../' ?>view/profile/index.php">Profile</a>
                <a href="<?= $baseUrl ?? '../' ?>controller/auth/AuthController.php?action=logout"
                    class="btn-nav">Logout</a>

            <?php else: ?>

                <?php if ($role === 'user'): ?>
                    <a href="<?= $baseUrl ?? '../' ?>index.php">Home</a>
                    <a href="<?= $baseUrl ?? '../' ?>view/wishlist/index.php">Wishlist</a>
                <?php endif; ?>

                <?php if ($role === 'scout'): ?>
                    <a href="<?= $baseUrl ?? '../' ?>view/scout/dashboard.php">Dashboard</a>
                    <a href="<?= $baseUrl ?? '../' ?>view/scout/create_request.php">New Request</a>
                    <a href="<?= $baseUrl ?? '../' ?>view/scout/my_requests.php">My Requests</a>
                    <a href="<?= $baseUrl ?? '../' ?>view/scout/approved_posts.php">Approved Posts</a>
                <?php endif; ?>

                <?php if ($role === 'admin'): ?>
                    <a href="<?= $baseUrl ?? '../' ?>index.php">Home</a>
                    <a href="<?= $baseUrl ?? '../' ?>view/admin/dashboard.php">Dashboard</a>
                <?php endif; ?>

                <a href="<?= $baseUrl ?? '../' ?>view/profile/index.php">Profile</a>
                <a href="<?= $baseUrl ?? '../' ?>controller/auth/AuthController.php?action=logout"
                    class="btn-nav">Logout</a>
            <?php endif; ?>
        </nav>
    </div>

    <?php
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash_type'] ?? 'success';
        echo '<div style="max-width:900px;margin:16px auto;padding:0 20px;">';
        echo '<div class="alert alert-' . $type . '">' . htmlspecialchars($_SESSION['flash']) . '</div>';
        echo '</div>';
        unset($_SESSION['flash'], $_SESSION['flash_type']);
    }
    ?>