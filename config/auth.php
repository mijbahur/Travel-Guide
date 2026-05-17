<?php

function requireLogin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location:  ../view/user/login.php');
        return false;
    }
}

function requireVerified() {
    requireLogin();
    if (!$_SESSION['is_verified']) {
        header('Location: ../user/pending.php');
        exit;
        // return false;
    }
}

function requireRole($role) {
    requireVerified();
    if ($_SESSION['role'] !== $role) {
        header('Location: ../user/home.php');
        exit;
        // return false;
    }
}

function isLoggedIn()  { return isset($_SESSION['user_id']); }
function isVerified()  { return isLoggedIn() && $_SESSION['is_verified']; }
function isRole($role) { return isLoggedIn() && $_SESSION['role'] === $role; }
?>
