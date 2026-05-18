<?php
session_start();
require_once __DIR__ . '/../../config/auth.php';
requireVerified();

require_once __DIR__ . '/../../model/postRequestModel.php';

$posts = getApprovedPostsByScout($_SESSION['user_id']);
$activePage = 'approved';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Approved Posts - Travel Guide</title>
    <link rel="stylesheet" href="../../public/style.css">
</head>

<body>
    <?php include 'partials/navbar.php'; ?>

    <div class="container">
        <h1 class="page-title">&#9989; My Approved Posts</h1>
        <p class="text-muted" style="margin-bottom:1.5rem">These are your submissions that have been published by the
            admin. You can submit a change request if needed.</p>

        <?php if (empty($posts)): ?>
            <div class="card">
                <p class="text-muted">None of your posts have been approved yet. Keep submitting!</p>
            </div>
        <?php else: ?>
            <div class="post-grid">
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <?php if (!empty($post['image'])): ?>
                            <img src="../../<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                        <?php else: ?>
                            <div
                                style="height:160px;background:linear-gradient(135deg,#ebf8ff,#bee3f8);display:flex;align-items:center;justify-content:center;font-size:2rem">
                                &#127757;</div>
                        <?php endif; ?>
                        <div class="pc-body">
                            <div class="pc-title"><?= htmlspecialchars($post['title']) ?></div>
                            <div class="pc-meta">
                                &#128205; <?= htmlspecialchars($post['country']) ?>
                                &nbsp;|&nbsp; &#127911;<?= htmlspecialchars($post['genre']) ?>
                                &nbsp;|&nbsp; &#128176; <?= ucfirst($post['cost_level']) ?>
                            </div>
                            <div class="pc-meta mt-1"><?= htmlspecialchars(substr($post['short_history'], 0, 80)) ?>...</div>
                            <div class="mt-2">
                                <span class="badge badge-approved">Published</span>
                                <a href="../../controller/scout/changeRequestController.php?post_id=<?= $post['id'] ?>"
                                    class="btn btn-warning btn-sm" style="margin-left:.5rem">Request Change</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>