<?php
require_once __DIR__ . '/../db.php';

function addToWishlist($userId, $postId) {
    $con = getConnection();
    $stmt = $con->prepare("INSERT IGNORE INTO wishlist (user_id, post_id) VALUES (?, ?)");
    return $stmt->execute([$userId, $postId]);
}

function removeFromWishlist($userId, $postId) {
    $con = getConnection();
    $stmt = $con->prepare("DELETE FROM wishlist WHERE user_id=? AND post_id=?");
    return $stmt->execute([$userId, $postId]);
}

function getWishlistByUser($userId) {
    $con = getConnection();
    $stmt = $con->prepare("
        SELECT w.id as wishlist_id, w.added_at, p.id as post_id, p.title, p.country, p.cost_level, p.genre
        FROM wishlist w
        JOIN posts p ON w.post_id = p.id
        WHERE w.user_id = ?
        ORDER BY w.added_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function isInWishlist($userId, $postId) {
    $con = getConnection();
    $stmt = $con->prepare("SELECT id FROM wishlist WHERE user_id=? AND post_id=?");
    $stmt->execute([$userId, $postId]);
    return $stmt->fetch() !== false;
}
