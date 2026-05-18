<?php
require_once __DIR__ . '/../db.php';

function getUserByEmail($email) {
    $con = getConnection();
    $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function getUserById($id) {
    $con = getConnection();
    $stmt = $con->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createUser($name, $email, $passwordHash, $role) {
    $con = getConnection();
    $stmt = $con->prepare("INSERT INTO users (name, email, password_hash, role, is_verified) VALUES (?, ?, ?, ?, 0)");
    return $stmt->execute([$name, $email, $passwordHash, $role]);
}

function updateUserProfile($id, $name, $email, $picturePath = null) {
    $con = getConnection();
    if ($picturePath) {
        $stmt = $con->prepare("UPDATE users SET name=?, email=?, profile_picture=? WHERE id=?");
        return $stmt->execute([$name, $email, $picturePath, $id]);
    } else {
        $stmt = $con->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        return $stmt->execute([$name, $email, $id]);
    }
}

function updateUserPassword($id, $passwordHash) {
    $con = getConnection();
    $stmt = $con->prepare("UPDATE users SET password_hash=? WHERE id=?");
    return $stmt->execute([$passwordHash, $id]);
}

function setRememberToken($id, $tokenHash) {
    $con = getConnection();
    $stmt = $con->prepare("UPDATE users SET remember_token=? WHERE id=?");
    $stmt->execute([$tokenHash, $id]);
}

function getUserByRememberToken($tokenHash) {
    $con = getConnection();
    $stmt = $con->prepare("SELECT * FROM users WHERE remember_token=?");
    $stmt->execute([$tokenHash]);
    return $stmt->fetch();
}

function clearRememberToken($id) {
    $con = getConnection();
    $stmt = $con->prepare("UPDATE users SET remember_token=NULL WHERE id=?");
    $stmt->execute([$id]);
}

function getLatestApprovedPosts($limit = 6) {
    $con = getConnection();
    $stmt = $con->prepare("SELECT * FROM posts WHERE status='approved' ORDER BY created_at DESC LIMIT ?");
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
