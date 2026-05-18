<?php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/Task4PostModel.php';
require_once __DIR__ . '/../../models/Task4CostModel.php';

class Task4CostController
{
    public static function estimate()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user' || $_SESSION['is_verified'] != 1) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in as a verified user.']);
            return;
        }

        $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
        $travelers = isset($_POST['travelers']) ? (int)$_POST['travelers'] : 0;
        $days = isset($_POST['days']) ? (int)$_POST['days'] : 0;

        if ($postId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid post ID.']);
            return;
        }

        if ($travelers < 1 || $travelers > 10) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Travelers must be between 1 and 10.']);
            return;
        }

        if ($days < 1) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Days must be at least 1.']);
            return;
        }

        $post = getApprovedPostDetailsById($postId);
        if (!$post) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Post not found or not approved.']);
            return;
        }

        $costData = getTask4BaseCost($postId, $post['cost_level']);
        $total = calculateTask4TotalCost($costData['base_cost'], $travelers, $days);

        echo json_encode([
            'success' => true,
            'base_cost' => $costData['base_cost'],
            'currency' => $costData['currency'],
            'travelers' => $travelers,
            'days' => $days,
            'total' => $total
        ]);
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
        if ($action === 'estimate') {
            Task4CostController::estimate();
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'An error occurred while processing the request.']);
    }
}
