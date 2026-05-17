<?php
require_once __DIR__ . '/db.php';

function getUserByEmail($email) {
    $con = getConnection();
    $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_close($con);
    return $user;
}

function getUserById($id) {
    $con = getConnection();
    $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_close($con);
    return $user;
}

function createUser($name, $email, $passwordHash, $role) {
    $con = getConnection();
    $stmt = mysqli_prepare($con, "INSERT INTO users (name, email, password_hash, role, is_verified) VALUES (?, ?, ?, ?, 0)");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $passwordHash, $role);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

function updateUserProfile($id, $name, $email, $picturePath = null) {
    $con = getConnection();
    if ($picturePath) {
        $stmt = mysqli_prepare($con, "UPDATE users SET name=?, email=?, profile_picture=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $picturePath, $id);
    } else {
        $stmt = mysqli_prepare($con, "UPDATE users SET name=?, email=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $id);
    }
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

function updateUserPassword($id, $passwordHash) {
    $con = getConnection();
    $stmt = mysqli_prepare($con, "UPDATE users SET password_hash=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "si", $passwordHash, $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

function setRememberToken($id, $tokenHash) {
    $con = getConnection();
    $stmt = mysqli_prepare($con, "UPDATE users SET remember_token=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "si", $tokenHash, $id);
    mysqli_stmt_execute($stmt);
    mysqli_close($con);
}

function getUserByRememberToken($tokenHash) {
    $con = getConnection();
    $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE remember_token=?");
    mysqli_stmt_bind_param($stmt, "s", $tokenHash);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_close($con);
    return $user;
}

function clearRememberToken($id) {
    $con = getConnection();
    $stmt = mysqli_prepare($con, "UPDATE users SET remember_token=NULL WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_close($con);
}

function getLatestApprovedPosts($limit = 6) {
    $con = getConnection();
    $stmt = mysqli_prepare($con, "SELECT * FROM posts WHERE status='approved' ORDER BY created_at DESC LIMIT ?");
    mysqli_stmt_bind_param($stmt, "i", $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $posts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }
    mysqli_close($con);
    return $posts;
}
