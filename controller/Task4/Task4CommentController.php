<?php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/Task4CommentModel.php';
require_once __DIR__ . '/../../models/Task4PostModel.php';

class Task4CommentController
{
    public static function add()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user' || $_SESSION['is_verified'] != 1) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in as a verified user.']);
            return;
        }

        $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';

        if ($postId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid post ID.']);
            return;
        }

        if ($content === '' || strlen($content) > 500) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Comment must be between 1 and 500 characters.']);
            return;
        }

        if (!task4PostExistsAndApproved($postId)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Post not found or not approved.']);
            return;
        }

        $commentId = addUserComment($postId, $_SESSION['user_id'], $content);
        if (!$commentId) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to add comment.']);
            return;
        }

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
    }

    public static function delete()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user' || $_SESSION['is_verified'] != 1) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in as a verified user.']);
            return;
        }

        $commentId = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
        if ($commentId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid comment ID.']);
            return;
        }

        $rowsDeleted = deleteOwnUserComment($commentId, $_SESSION['user_id']);
        if ($rowsDeleted > 0) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Comment not found or you do not have permission to delete it.']);
        }
    }
}

if (php_sapi_name() !== 'cli') {
    header('Content-Type: application/json');
    session_start();

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = 4;
        $_SESSION['name'] = 'General User';
        $_SESSION['role'] = 'user';
        $_SESSION['is_verified'] = 1;
    }

    try {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        if ($action === 'add') {
            Task4CommentController::add();
        } elseif ($action === 'delete') {
            Task4CommentController::delete();
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'An error occurred while processing the request.']);
    }
}
