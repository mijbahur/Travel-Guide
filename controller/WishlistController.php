<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../model/WishlistModel.php';

// Auth check
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

// Role check – only verified general users
if ($_SESSION['role'] !== 'user' || !$_SESSION['is_verified']) {
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

if ($action === 'add') {
    handleAdd($userId);
} elseif ($action === 'remove') {
    handleRemove($userId);
} elseif ($action === 'list') {
    handleList($userId);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}

function handleAdd($userId) {
    $data   = json_decode(file_get_contents('php://input'), true);
    $postId = isset($data['post_id']) ? (int)$data['post_id'] : 0;
    if ($postId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid post.']);
        return;
    }
    $ok = addToWishlist($userId, $postId);
    echo json_encode(['success' => $ok, 'message' => $ok ? 'Added to wishlist.' : 'Already in wishlist or error.']);
}

function handleRemove($userId) {
    $data   = json_decode(file_get_contents('php://input'), true);
    $postId = isset($data['post_id']) ? (int)$data['post_id'] : 0;
    if ($postId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid post.']);
        return;
    }
    $ok = removeFromWishlist($userId, $postId);
    echo json_encode(['success' => $ok, 'message' => $ok ? 'Removed from wishlist.' : 'Error removing.']);
}

function handleList($userId) {
    $items = getWishlistByUser($userId);
    echo json_encode(['success' => true, 'data' => $items]);
}
