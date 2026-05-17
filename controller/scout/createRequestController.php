<?php
session_start();
require_once __DIR__ . '/../../config/auth.php';
requireVerified();

require_once __DIR__ . '/../fileUpload.php';
require_once __DIR__ . '/../../model/postRequestModel.php';

$errors = [];
$success = '';
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Invalid form token. Please try again.';
    }

    $title = trim($_POST['title'] ?? '');
    $history = trim($_POST['history'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $genre = $_POST['genre'] ?? '';
    $cost = $_POST['cost_level'] ?? '';
    $travel = trim($_POST['travel_medium_info'] ?? '');

    $old = $_POST;

    if ($title === '')
        $errors['title'] = 'Title is required.';
    if ($history === '')
        $errors['history'] = 'Short history is required.';
    if ($country === '')
        $errors['country'] = 'Country is required.';
    if (strlen($history) > 2000)
        $errors['history'] = 'History must be under 2000 characters.';

    $validGenres = ['beach', 'mountain', 'city', 'historical', 'adventure', 'nature', 'cultural'];
    if (!in_array($genre, $validGenres, true))
        $errors['genre'] = 'Please select a valid genre.';

    if (!in_array($cost, ['low', 'medium', 'high'], true))
        $errors['cost'] = 'Please select a cost level.';

    if ($travel === '')
        $errors['travel'] = 'Travel medium info is required.';

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
        ];

        createPostRequest($_SESSION['user_id'], $postData, $imagePath);

        $success = 'Your post request has been submitted! Admin will review it soon.';
        $old = [];
    }
}

include_once __DIR__ . '/../../view/scout/create_request.php';
