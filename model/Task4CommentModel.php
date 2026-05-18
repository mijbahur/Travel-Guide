<?php
// Task 4 Comment Model - Manage comments on posts
require_once __DIR__ . '/../config/auth.php';

/**
 * Get all comments for a post with user information
 * @param int $postId The post ID
 * @return array Array of comments with user details
 */
function getCommentsByPostId($postId)
{
    $postId = (int)$postId;
    $db = getConnection();
    $sql = "SELECT c.id, c.post_id, c.user_id, c.content, c.created_at, u.name 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.post_id = :post_id 
            ORDER BY c.created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':post_id' => $postId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Add a new comment by user on a post
 * @param int $postId The post ID
 * @param int $userId The user ID
 * @param string $content Comment content
 * @return int|false The inserted comment ID or false
 */
function addUserComment($postId, $userId, $content)
{
    $postId = (int)$postId;
    $userId = (int)$userId;
    $db = getConnection();
    $sql = "INSERT INTO comments (post_id, user_id, content, created_at) 
            VALUES (:post_id, :user_id, :content, NOW())";
    
    $stmt = $db->prepare($sql);
    $success = $stmt->execute([
        ':post_id' => $postId,
        ':user_id' => $userId,
        ':content' => $content
    ]);
    
    return $success ? $db->lastInsertId() : false;
}

/**
 * Delete a comment if owned by the user
 * @param int $commentId The comment ID
 * @param int $userId The user ID (ownership check)
 * @return int Number of rows deleted
 */
function deleteOwnUserComment($commentId, $userId)
{
    $commentId = (int)$commentId;
    $userId = (int)$userId;
    $db = getConnection();
    $sql = "DELETE FROM comments WHERE id = :comment_id AND user_id = :user_id";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':comment_id' => $commentId,
        ':user_id' => $userId
    ]);
    
    return $stmt->rowCount();
}

/**
 * Get a comment by ID
 * @param int $commentId The comment ID
 * @return array|false Comment details or false if not found
 */
function getTask4CommentById($commentId)
{
    $commentId = (int)$commentId;
    $db = getConnection();
    $sql = "SELECT id, post_id, user_id, content, created_at FROM comments WHERE id = :comment_id";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':comment_id' => $commentId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
