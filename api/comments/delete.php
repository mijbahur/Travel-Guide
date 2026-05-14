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

    // Get comment_id from POST
    $commentId = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;

    // Validate comment_id
    if ($commentId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid comment ID.']);
        exit;
    }

    // Attempt to delete the comment (only if owned by user)
    $rowsDeleted = deleteOwnUserComment($commentId, $_SESSION['user_id']);

    if ($rowsDeleted > 0) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Comment not found or you do not have permission to delete it.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while deleting the comment.']);
}
?>
