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
function createPostRequest($scoutId, $postData, $imagePath = null)
{
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


// single approved post 
function getPostById($id)
{
    $con = getConnection();
    $stmt = $con->prepare("SELECT * FROM posts WHERE id = :id AND status = 'approved'");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}
//  single request 
function getRequestById($id)
{
    $con = getConnection();
    $stmt = $con->prepare("SELECT * FROM post_requests WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

//  CREATE change request (for approved post) 
function createChangeRequest($scoutId, $originalPostId, $postData, $imagePath = null)
{
    $con = getConnection();

    if ($imagePath) {
        $postData['image'] = $imagePath;
    }
    $postData['original_post_id'] = $originalPostId;

    $json = json_encode($postData);

    $stmt = $con->prepare(
        "INSERT INTO post_requests (scout_id, post_data, requested_at, status)
         VALUES (:scout_id, :post_data, NOW(), 'pending')"
    );
    $stmt->execute([':scout_id' => $scoutId, ':post_data' => $json]);
    return $con->lastInsertId();
}


//  DELETE 
function deletePostRequest($id, $scoutId)
{
    $con = getConnection();
    $stmt = $con->prepare(
        "DELETE FROM post_requests
         WHERE id = :id AND scout_id = :scout_id AND status = 'pending'"
    );
    $stmt->execute([':id' => $id, ':scout_id' => $scoutId]);
    return $stmt->rowCount();
}

// UPDATE 
function updatePostRequest($id, $scoutId, $postData, $imagePath = null)
{
    $con = getConnection();

    // Keep existing image if no new one uploaded
    if ($imagePath) {
        $postData['image'] = $imagePath;
    }

    $json = json_encode($postData);

    $stmt = $con->prepare(
        "UPDATE post_requests
         SET post_data = :post_data
         WHERE id = :id AND scout_id = :scout_id AND status = 'pending'"
    );
    $stmt->execute([
        ':post_data' => $json,
        ':id' => $id,
        ':scout_id' => $scoutId,
    ]);
    return $stmt->rowCount();
}

?>