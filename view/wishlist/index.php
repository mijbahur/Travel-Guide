<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('location: ../../view/auth/login.php');
    exit;
}

if ($_SESSION['role'] !== 'user' || !$_SESSION['is_verified']) {
    header('location: ../../index.php');
    exit;
}

require_once '../../model/WishlistModel.php';

$pageTitle = 'My Wishlist — Travel Guide';
$baseUrl   = '../../';
include '../../view/layout/header.php';

$items = getWishlistByUser($_SESSION['user_id']);
?>

<div class="container">
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
            <h2>⭐ My Wishlist</h2>
            <a href="../../index.php" class="btn btn-primary btn-sm">← Back to Home</a>
        </div>

        <?php if (empty($items)): ?>
            <div class="alert alert-info">Your wishlist is empty. Browse posts and add places you want to visit!</div>
        <?php else: ?>
            <p style="color:#666;font-size:14px;margin-bottom:16px;">
                <?= count($items) ?> place(s) saved. Check off what you've visited!
            </p>

            <div id="wishlist-container">
                <?php foreach ($items as $item): ?>
                    <div class="wishlist-check" id="wish-<?= $item['post_id'] ?>">
                        <input type="checkbox" title="Mark as visited">
                        <div class="post-info">
                            <strong><?= htmlspecialchars($item['title']) ?></strong>
                            <span>
                                📍 <?= htmlspecialchars($item['country']) ?> &nbsp;|&nbsp;
                                🏷️ <?= htmlspecialchars($item['genre']) ?> &nbsp;|&nbsp;
                                💰 <span class="badge badge-<?= $item['cost_level'] ?>"><?= ucfirst($item['cost_level']) ?></span>
                            </span>
                        </div>
                        <button
                            class="btn btn-danger btn-sm"
                            onclick="removeWishlist(<?= $item['post_id'] ?>, this)">
                            Remove
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="toast" style="display:none;position:fixed;bottom:24px;right:24px;background:#333;color:#fff;padding:12px 20px;border-radius:6px;font-size:14px;z-index:999;"></div>

<script>
function showToast(msg, isError = false) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.background = isError ? '#e05c3a' : '#28a745';
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 2500);
}

function removeWishlist(postId, btn) {
    if (!confirm('Remove this place from your wishlist?')) return;

    fetch('../../controller/WishlistController.php?action=remove', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ post_id: postId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById('wish-' + postId);
            if (row) row.remove();
            showToast('Removed from wishlist.');

            const container = document.getElementById('wishlist-container');
            if (container && container.children.length === 0) {
                container.innerHTML = '<div class="alert alert-info" style="margin-top:10px;">Your wishlist is empty.</div>';
            }
        } else {
            showToast(data.message || 'Error removing.', true);
        }
    })
    .catch(() => showToast('Network error.', true));
}
</script>

<?php include '../../view/layout/footer.php'; ?>
