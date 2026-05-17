<?php
require_once __DIR__ . '/../model/db.php';

// READ – all requests by a scout
function getRequestsByScout($scoutId)
{
    $con = getConnection();
    $stmt = $con->prepare(
        "SELECT * FROM post_requests WHERE scout_id = :sid ORDER BY requested_at DESC"
    );
    $stmt->execute([':sid' => $scoutId]);
    return $stmt->fetchAll();
}

// READ – approved posts for this scout
function getApprovedPostsByScout($scoutId)
{
    $con = getConnection();
    $stmt = $con->prepare(
        "SELECT * FROM posts WHERE scout_id = :sid AND status = 'approved'
         ORDER BY updated_at DESC"
    );
    $stmt->execute([':sid' => $scoutId]);
    return $stmt->fetchAll();
}


// CREATE
function createPostRequest($scoutId, $postData, $imagePath = null) {
    $con = getConnection();
    
    if ($imagePath) {
        $postData['image'] = $imagePath;
    }

    $json = json_encode($postData);

    $stmt = $con->prepare(
        "INSERT INTO post_requests (scout_id, post_data, requested_at, status)
         VALUES (:scout_id, :post_data, NOW(), 'pending')"
    );
    $stmt->execute([':scout_id' => $scoutId, ':post_data' => $json]);
    return $con->lastInsertId();
}

?>