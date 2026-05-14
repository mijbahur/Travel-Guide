<?php
// Task 4 Post Model - Browse, Search & Filter approved posts for users
require_once __DIR__ . '/../config/db.php';

/**
 * Get all approved posts for browsing
 * @return array Array of approved posts ordered by newest first
 */
function getApprovedPostsForBrowse()
{
    $db = getConnection();
    $sql = "SELECT id, scout_id, title, short_history, country, genre, cost_level, travel_medium_info, created_at 
            FROM posts 
            WHERE status = 'approved' 
            ORDER BY created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get approved post details by ID
 * @param int $postId The post ID
 * @return array|false Post details or false if not found
 */
function getApprovedPostDetailsById($postId)
{
    $postId = (int)$postId;
    $db = getConnection();
    $sql = "SELECT id, scout_id, title, short_history, country, genre, cost_level, travel_medium_info, status, created_at, updated_at 
            FROM posts 
            WHERE id = :post_id AND status = 'approved'";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':post_id' => $postId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Search approved posts by title or country
 * @param string $query Search query string
 * @return array Array of matching posts
 */
function searchApprovedPosts($query)
{
    $db = getConnection();
    $searchTerm = '%' . $query . '%';
    $sql = "SELECT id, title, short_history, country, genre, cost_level, travel_medium_info, created_at 
            FROM posts 
            WHERE status = 'approved' AND (title LIKE :search OR country LIKE :search) 
            ORDER BY created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':search' => $searchTerm]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Filter approved posts by optional criteria
 * @param string $country Optional country filter
 * @param string $genre Optional genre filter
 * @param string $costLevel Optional cost level filter
 * @return array Array of filtered posts
 */
function filterApprovedPosts($country = '', $genre = '', $costLevel = '')
{
    $db = getConnection();
    $sql = "SELECT id, title, short_history, country, genre, cost_level, travel_medium_info, created_at 
            FROM posts 
            WHERE status = 'approved'";
    
    $params = [];
    
    if (!empty($country)) {
        $sql .= " AND country = :country";
        $params[':country'] = $country;
    }
    
    if (!empty($genre)) {
        $sql .= " AND genre = :genre";
        $params[':genre'] = $genre;
    }
    
    if (!empty($costLevel)) {
        $sql .= " AND cost_level = :cost_level";
        $params[':cost_level'] = $costLevel;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Check if a post exists and is approved
 * @param int $postId The post ID
 * @return bool True if post exists and is approved, false otherwise
 */
function task4PostExistsAndApproved($postId)
{
    $postId = (int)$postId;
    $db = getConnection();
    $sql = "SELECT 1 FROM posts WHERE id = :post_id AND status = 'approved' LIMIT 1";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':post_id' => $postId]);
    return $stmt->fetch() !== false;
}
