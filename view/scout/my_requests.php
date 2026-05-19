<?php
session_start();
require_once __DIR__ . '/../../config/auth/AuthController.php';
requireVerified();

require_once __DIR__ . '/../../model/postRequestModel.php';

$requests = getRequestsByScout($_SESSION['user_id']);
$activePage = 'my-requests';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Requests - Travel Guide</title>
    <link rel="stylesheet" href="../../asset/css/style.css">
</head>

<body>
    <?php include 'partials/navbar.php'; ?>

    <div class="container">
        <div class="d-flex align-center justify-between" style="margin-bottom:1.5rem">
            <h1 class="page-title" style="margin-bottom:0">&#128203; My Post Requests</h1>
            <a href="create_request.php" class="btn btn-primary">+ New Request</a>
        </div>

        <div id="alertBox"></div>

        <?php if (empty($requests)): ?>
            <div class="card">
                <p class="text-muted">You haven't submitted any requests yet.
                    <a href="create_request.php">Create your first one!</a>
                </p>
            </div>
        <?php else: ?>
            <div class="card" style="padding:0;overflow:hidden">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Country</th>
                            <th>Genre</th>
                            <th>Cost</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="requestsTable">
                        <?php foreach ($requests as $i => $req):
                            $d = json_decode($req['post_data'], true);
                            ?>
                            <tr id="row-<?= $req['id'] ?>">
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($d['title'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($d['country'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($d['genre'] ?? '-') ?></td>
                                <td><span
                                        class="badge badge-<?= $d['cost_level'] ?? 'pending' ?>"><?= $d['cost_level'] ?? '-' ?></span>
                                </td>
                                <td><span class="badge badge-<?= $req['status'] ?>"><?= $req['status'] ?></span></td>
                                <td><?= date('d M Y', strtotime($req['requested_at'])) ?></td>
                                <td>
                                    <?php if ($req['status'] === 'pending'): ?>
                                        <a href="edit_request.php?id=<?= $req['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm" onclick="deleteRequest(<?= $req['id'] ?>)"
                                            id="del-<?= $req['id'] ?>">Delete</button>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size:.8rem">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const csrfToken = "<?= $_SESSION['csrf_token'] ?? '' ?>";
    </script>
    <script src="../../asset/js/deletePost.js"></script>
</body>

</html>