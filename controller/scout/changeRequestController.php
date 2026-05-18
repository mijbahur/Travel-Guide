<?php
session_start();
require_once __DIR__ . '/../../config/auth.php';
requireVerified();

require_once __DIR__ . '/../../model/postRequestModel.php';
require_once __DIR__ . '/../fileUpload.php';

// Load the original approved post
$postId = (int) ($_GET['post_id'] ?? $_POST['original_post_id'] ?? 0);
$post = getPostById($postId);

if (!$post || $post['scout_id'] != $_SESSION['user_id']) {
    header('Location: ../../view/scout/approved_posts.php');
    exit;
}

$errors = [];
$success = '';
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF
    if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Invalid form token. Please try again.';
    }

    //  Collect & validate 
    $title = trim($_POST['title'] ?? '');
    $history = trim($_POST['history'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $genre = $_POST['genre'] ?? '';
    $cost = $_POST['cost_level'] ?? '';
    $travel = trim($_POST['travel_medium_info'] ?? '');
    $reason = trim($_POST['change_reason'] ?? '');

    $old = $_POST;

    if ($title === '')
        $errors['title'] = 'Title is required.';
    if ($history === '')
        $errors['history'] = 'Short history is required.';
    if ($country === '')
        $errors['country'] = 'Country is required.';
    if (strlen($history) > 2000)
        $errors['history'] = 'History must be under 2000 characters.';
    if ($reason === '')
        $errors['reason'] = 'Please explain what changed.';

    $validGenres = ['beach', 'mountain', 'city', 'historical', 'adventure', 'nature', 'cultural'];
    if (!in_array($genre, $validGenres, true))
        $errors['genre'] = 'Please select a valid genre.';

    if (!in_array($cost, ['low', 'medium', 'high'], true))
        $errors['cost'] = 'Please select a cost level.';

    if ($travel === '')
        $errors['travel'] = 'Travel medium info is required.';

    //  Image upload 
    $imagePath = null;
    if (empty($errors)) {
        try {
            $imagePath = handleImageUpload('post_image');
        } catch (Exception $e) {
            $errors['post_image'] = $e->getMessage();
        }
    }

    if (empty($errors)) {
        $postData = [
            'title' => $title,
            'history' => $history,
            'country' => $country,
            'genre' => $genre,
            'cost_level' => $cost,
            'travel' => $travel,
            'change_reason' => $reason,
        ];

        createChangeRequest($_SESSION['user_id'], $postId, $postData, $imagePath);

        $success = 'Change request submitted! Admin will review it.';
        $old = [];
    }
}

include_once __DIR__ . '/../../view/scout/change_request.php';
