<?php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/Task4PostModel.php';

class Task4PostController
{
    public static function search()
    {
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';

        if (strlen($query) > 255) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Search query is too long.']);
            return;
        }

        // Use the model search function for approved posts.
        $posts = searchApprovedPosts($query);

        echo json_encode([
            'success' => true,
            'posts' => $posts
        ]);
    }

    public static function filter()
    {
        $country = isset($_GET['country']) ? trim($_GET['country']) : '';
        $genre = isset($_GET['genre']) ? trim($_GET['genre']) : '';
        $costLevel = isset($_GET['cost_level']) ? trim($_GET['cost_level']) : '';

        if (strlen($country) > 100 || strlen($genre) > 100 || strlen($costLevel) > 50) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Filter values are too long.']);
            return;
        }

        $posts = filterApprovedPosts($country, $genre, $costLevel);

        echo json_encode([
            'success' => true,
            'posts' => $posts
        ]);
    }
}

if (php_sapi_name() !== 'cli') {
    header('Content-Type: application/json');

    try {
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        if ($action === 'search') {
            Task4PostController::search();
        } elseif ($action === 'filter') {
            Task4PostController::filter();
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'An error occurred while processing the request.']);
    }
}
