<?php

function requireLogin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../../view/auth/login.php');
        exit;
    }
}

function requireVerified() {
    requireLogin();
    if (!$_SESSION['is_verified']) {
        header('Location: ../../index.php');
        exit;
    }
}

function requireRole($role) {
    requireVerified();
    if ($_SESSION['role'] !== $role) {
        header('Location: ../../index.php');
        exit;
    }
}

function isLoggedIn()  { return isset($_SESSION['user_id']); }
function isVerified()  { return isLoggedIn() && $_SESSION['is_verified']; }
function isRole($role) { return isLoggedIn() && $_SESSION['role'] === $role; }
