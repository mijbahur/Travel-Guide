<?php
session_start();
header('Content-Type: application/json');

try {
    // Check user authentication
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user' || $_SESSION['is_verified'] != 1) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in as a verified user.']);
        exit;
    }

    require_once __DIR__ . '/../../models/Task4CommentModel.php';
    require_once __DIR__ . '/../../models/Task4PostModel.php';

    // Get POST data
    $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    // Validate post_id
    if ($postId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid post ID.']);
        exit;
    }

    // Validate content
    if (empty($content) || strlen($content) > 500) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Comment content must be 1-500 characters.']);
        exit;
    }

    // Check if post exists and is approved
    if (!task4PostExistsAndApproved($postId)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Post not found or not approved.']);
        exit;
    }

    // Add comment
    $commentId = addUserComment($postId, $_SESSION['user_id'], $content);
    if (!$commentId) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add comment.']);
        exit;
    }

    // Return success with comment data
    echo json_encode([
        'success' => true,
        'comment' => [
            'id' => $commentId,
            'post_id' => $postId,
            'user_id' => $_SESSION['user_id'],
            'name' => $_SESSION['name'],
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while adding the comment.']);
}
?>
