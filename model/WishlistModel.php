<?php
require_once __DIR__ . '/db.php';

function addToWishlist($userId, $postId) {
    $con = getConnection();
    $stmt = mysqli_prepare($con, "INSERT IGNORE INTO wishlist (user_id, post_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ii", $userId, $postId);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

function removeFromWishlist($userId, $postId) {
    $con = getConnection();
    $stmt = mysqli_prepare($con, "DELETE FROM wishlist WHERE user_id=? AND post_id=?");
    mysqli_stmt_bind_param($stmt, "ii", $userId, $postId);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

function getWishlistByUser($userId) {
    $con = getConnection();
    $stmt = mysqli_prepare($con, "
        SELECT w.id as wishlist_id, w.added_at, p.id as post_id, p.title, p.country, p.cost_level, p.genre
        FROM wishlist w
        JOIN posts p ON w.post_id = p.id
        WHERE w.user_id = ?
        ORDER BY w.added_at DESC
    ");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    mysqli_close($con);
    return $items;
}

function isInWishlist($userId, $postId) {
    $con = getConnection();
    $stmt = mysqli_prepare($con, "SELECT id FROM wishlist WHERE user_id=? AND post_id=?");
    mysqli_stmt_bind_param($stmt, "ii", $userId, $postId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $found = mysqli_num_rows($result) > 0;
    mysqli_close($con);
    return $found;
}
