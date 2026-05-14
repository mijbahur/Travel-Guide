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

    require_once __DIR__ . '/../../models/Task4PostModel.php';
    require_once __DIR__ . '/../../models/Task4CostModel.php';

    // Get POST data
    $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $travelers = isset($_POST['travelers']) ? (int)$_POST['travelers'] : 0;
    $days = isset($_POST['days']) ? (int)$_POST['days'] : 0;

    // Validate inputs
    if ($postId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid post ID.']);
        exit;
    }

    if ($travelers < 1 || $travelers > 10) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Travelers must be between 1 and 10.']);
        exit;
    }

    if ($days < 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Days must be at least 1.']);
        exit;
    }

    // Check if post exists and is approved
    if (!task4PostExistsAndApproved($postId)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Post not found or not approved.']);
        exit;
    }

    // Get base cost
    $costData = getTask4BaseCost($postId, ''); // Cost level not needed here as it's handled in model
    $baseCost = $costData['base_cost'];
    $currency = $costData['currency'];

    // Calculate total cost
    $total = calculateTask4TotalCost($baseCost, $travelers, $days);

    // Return success with cost data
    echo json_encode([
        'success' => true,
        'base_cost' => $baseCost,
        'currency' => $currency,
        'travelers' => $travelers,
        'days' => $days,
        'total' => $total
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while calculating the cost.']);
}
?>
