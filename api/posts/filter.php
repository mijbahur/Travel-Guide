<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../../models/Task4PostModel.php';
    
    // Get filter parameters from GET
    $country = isset($_GET['country']) ? trim($_GET['country']) : '';
    $genre = isset($_GET['genre']) ? trim($_GET['genre']) : '';
    $costLevel = isset($_GET['cost_level']) ? trim($_GET['cost_level']) : '';
    
    // Fetch filtered posts
    $posts = filterApprovedPosts($country, $genre, $costLevel);
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'posts' => $posts
    ]);
} catch (Exception $e) {
    // Return error response without exposing details
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while filtering posts.'
    ]);
}
