<?php
session_start();
require_once __DIR__ . '/../../config/auth/auth.php';
requireRole('scout');

require_once __DIR__ . '/../../model/postRequestModel.php';

$scoutId = $_SESSION['user_id'];
$requests = getRequestsByScout($scoutId);
$approved = getApprovedPostsByScout($scoutId);

$pending = count(array_filter($requests, fn($r) => $r['status'] === 'pending'));
$rejected = count(array_filter($requests, fn($r) => $r['status'] === 'rejected'));
$approvedCount = count($approved);
$total = count($requests);

$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scout Dashboard – Travel Guide</title>
    <link rel="stylesheet" href="../../asset/css/style.css">
</head>

<body>
    <?php include 'partials/navbar.php'; ?>

    <div class="container">
        <h1 class="page-title">&#x1F44B; Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>

        <div class="stats">
            <div class="stat-card">
                <div class="num"><?= $total ?></div>
                <div class="label">Total Requests</div>
            </div>
            <div class="stat-card" style="border-top-color:#d69e2e">
                <div class="num" style="color:#d69e2e"><?= $pending ?></div>
                <div class="label">Pending Review</div>
            </div>
            <div class="stat-card" style="border-top-color:#38a169">
                <div class="num" style="color:#38a169"><?= $approvedCount ?></div>
                <div class="label">Approved Posts</div>
            </div>
            <div class="stat-card" style="border-top-color:#e53e3e">
                <div class="num" style="color:#e53e3e"><?= $rejected ?></div>
                <div class="label">Rejected</div>
            </div>
        </div>

        <div class="card">
            <h2 style="margin-bottom:1rem;font-size:1.1rem;color:#2b6cb0">Quick Actions</h2>
            <a href="../../controller/scout/createRequestController.php" class="btn btn-primary">+ New Post Request</a>
            <a href="my_requests.php" class="btn btn-warning mt-1" style="margin-left:.7rem">&#128203; My Requests</a>
            <a href="approved_posts.php" class="btn btn-success" style="margin-left:.7rem">&#x1F44B; Approved Posts</a>
        </div>

        <div class="card">
            <h2 style="margin-bottom:1rem;font-size:1.1rem;color:#2b6cb0">Recent Requests</h2>
            <?php if (empty($requests)): ?>
                <p class="text-muted">No requests yet. <a href="create_request.php">Create your first one!</a></p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Country</th>
                            <th>Genre</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($requests, 0, 5) as $req):
                            $d = json_decode($req['post_data'], true);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($d['title'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($d['country'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($d['genre'] ?? '—') ?></td>
                                <td><span class="badge badge-<?= $req['status'] ?>"><?= $req['status'] ?></span></td>
                                <td><?= date('d M Y', strtotime($req['requested_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if ($total > 5): ?>
                    <a href="my_requests.php" class="text-muted mt-2" style="display:block">View all <?= $total ?> requests
                        →</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php include '../../view/layout/footer.php'; ?>