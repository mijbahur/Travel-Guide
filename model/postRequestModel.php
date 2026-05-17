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

?>