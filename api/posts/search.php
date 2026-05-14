<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../../models/Task4PostModel.php';
    
    // Get search query from GET parameter
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    // Fetch posts based on query
    if (empty($query)) {
        // If no query, return all approved posts
        $posts = getApprovedPostsForBrowse();
    } else {
        // Search for matching posts
        $posts = searchApprovedPosts($query);
    }
    
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
        'message' => 'An error occurred while searching posts.'
    ]);
}
