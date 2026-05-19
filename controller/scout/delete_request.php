<?php
session_start();
header('Content-Type: application/json');

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'scout' ||
    $_SESSION['is_verified'] != 1
) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

// CSRF check
if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? 'x')) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid request ID.']);
    exit;
}

require_once __DIR__ . '/../../model/postRequestModel.php';

$rows = deletePostRequest($id, $_SESSION['user_id']);

if ($rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Request deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not delete. It may already be reviewed.']);
}
?>