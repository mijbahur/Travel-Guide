<?php
session_start();
header('Content-Type: application/json');

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role']        !== 'scout' ||
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

$id      = (int)($_POST['id'] ?? 0);
$title   = trim($_POST['title']             ?? '');
$history = trim($_POST['history']           ?? '');
$country = trim($_POST['country']           ?? '');
$genre   = $_POST['genre']                  ?? '';
$cost    = $_POST['cost_level']             ?? '';
$travel  = trim($_POST['travel_medium_info'] ?? '');

$errs = [];
if ($id <= 0)         $errs[] = 'Invalid request ID.';
if ($title   === '')  $errs[] = 'Title is required.';
if ($history === '')  $errs[] = 'History is required.';
if ($country === '')  $errs[] = 'Country is required.';
if (!in_array($genre, ['beach','mountain','city','historical','adventure','nature','cultural']))
    $errs[] = 'Invalid genre.';
if (!in_array($cost, ['low','medium','high']))
    $errs[] = 'Invalid cost level.';
if ($travel  === '')  $errs[] = 'Travel medium info is required.';

if (!empty($errs)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errs)]);
    exit;
}

require_once __DIR__ . '/../fileUpload.php';
$imagePath = $_POST['existing_image'] ?? null;

try {
    $newImage = handleImageUpload('post_image');
    if ($newImage) $imagePath = $newImage;
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

// Update DB 
require_once __DIR__ . '/../model/postRequestModel.php';

$postData = [
    'title'      => $title,
    'history'    => $history,
    'country'    => $country,
    'genre'      => $genre,
    'cost_level' => $cost,
    'travel'     => $travel,
];
if ($imagePath) $postData['image'] = $imagePath;

$rows = updatePostRequest($id, $_SESSION['user_id'], $postData);

if ($rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Request updated successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'No changes were saved. The request may have already been reviewed.']);
}
?>
