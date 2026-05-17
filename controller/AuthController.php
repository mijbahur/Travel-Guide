<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../model/UserModel.php';

$action = $_GET['action'] ?? '';

if ($action === 'register') {
    handleRegister();
} elseif ($action === 'login') {
    handleLogin();
} elseif ($action === 'logout') {
    handleLogout();
} else {
    header('location: ../index.php');
    exit;
}

// ── REGISTER ──────────────────────────────────────────────────────
function handleRegister() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('location: ../view/auth/register.php');
        exit;
    }

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $role     = $_POST['role'] ?? 'user';

    $errors = [];

    if ($name === '')                               $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
    if (strlen($password) < 8)                     $errors[] = "Password must be at least 8 characters.";
    if ($password !== $confirm)                    $errors[] = "Passwords do not match.";
    if (!in_array($role, ['admin','scout','user'])) $errors[] = "Invalid role selected.";

    if (empty($errors) && getUserByEmail($email)) {
        $errors[] = "This email is already registered.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = compact('name', 'email', 'role');
        header('location: ../view/auth/register.php');
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    createUser($name, $email, $hash, $role);

    $_SESSION['flash']      = "Registration successful! Please wait for admin approval before logging in.";
    $_SESSION['flash_type'] = 'success';
    header('location: ../view/auth/login.php');
    exit;
}

// ── LOGIN ─────────────────────────────────────────────────────────
function handleLogin() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('location: ../view/auth/login.php');
        exit;
    }

    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember_me']);

    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
    if ($password === '')                            $errors[] = "Password is required.";

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = ['email' => $email];
        header('location: ../view/auth/login.php');
        exit;
    }

    $user = getUserByEmail($email);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $_SESSION['errors'] = ["Invalid email or password."];
        $_SESSION['old']    = ['email' => $email];
        header('location: ../view/auth/login.php');
        exit;
    }

    // Set session
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['name']       = $user['name'];
    $_SESSION['role']       = $user['role'];
    $_SESSION['is_verified'] = $user['is_verified'];

    // Remember Me
    if ($rememberMe) {
        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        setRememberToken($user['id'], $tokenHash);
        setcookie('remember_token', $token, time() + 30 * 24 * 3600, '/', '', false, true);
    }

    header('location: ../index.php');
    exit;
}

// ── LOGOUT ────────────────────────────────────────────────────────
function handleLogout() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['user_id'])) {
        clearRememberToken($_SESSION['user_id']);
    }
    setcookie('remember_token', '', time() - 3600, '/');
    session_destroy();
    header('location: ../view/auth/login.php');
    exit;
}
