<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('location: ../view/auth/login.php');
    exit;
}

require_once __DIR__ . '/../model/UserModel.php';

$action = $_GET['action'] ?? 'show';

if ($action === 'update') {
    handleUpdateProfile();
} elseif ($action === 'changePassword') {
    handleChangePassword();
} else {
    header('location: ../view/profile/index.php');
    exit;
}

// ── UPDATE PROFILE ────────────────────────────────────────────────
function handleUpdateProfile() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('location: ../view/profile/index.php');
        exit;
    }

    $id    = $_SESSION['user_id'];
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $errors = [];
    if ($name === '')                               $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";

    // Check email unique (exclude current user)
    if (empty($errors)) {
        $existing = getUserByEmail($email);
        if ($existing && $existing['id'] != $id) {
            $errors[] = "This email is already taken.";
        }
    }

    // Handle profile picture upload
    $picturePath = null;
    if (!empty($_FILES['profile_picture']['name'])) {
        $file    = $_FILES['profile_picture'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowed)) $errors[] = "Profile picture must be JPG, PNG, GIF, or WEBP.";
        if ($file['size'] > $maxSize)       $errors[] = "Profile picture must be under 2MB.";

        if (empty($errors)) {
            $ext         = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename    = 'user_' . $id . '_' . time() . '.' . $ext;
            $destination = __DIR__ . '/../asset/img/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $picturePath = $filename;
            } else {
                $errors[] = "Failed to upload profile picture.";
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('location: ../view/profile/index.php');
        exit;
    }

    updateUserProfile($id, $name, $email, $picturePath);

    $_SESSION['name']       = $name;
    $_SESSION['flash']      = "Profile updated successfully!";
    $_SESSION['flash_type'] = 'success';
    header('location: ../view/profile/index.php');
    exit;
}

// ── CHANGE PASSWORD ───────────────────────────────────────────────
function handleChangePassword() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('location: ../view/profile/index.php');
        exit;
    }

    $id      = $_SESSION['user_id'];
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $errors = [];
    if ($current === '')   $errors[] = "Current password is required.";
    if (strlen($new) < 8) $errors[] = "New password must be at least 8 characters.";
    if ($new !== $confirm) $errors[] = "New passwords do not match.";

    if (empty($errors)) {
        $user = getUserById($id);
        if (!password_verify($current, $user['password_hash'])) {
            $errors[] = "Current password is incorrect.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('location: ../view/profile/index.php');
        exit;
    }

    $hash = password_hash($new, PASSWORD_DEFAULT);
    updateUserPassword($id, $hash);

    $_SESSION['flash']      = "Password changed successfully!";
    $_SESSION['flash_type'] = 'success';
    header('location: ../view/profile/index.php');
    exit;
}
