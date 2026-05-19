<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();

$pageTitle = 'Home Travel Guide';
$baseUrl = './';
include './view/layout/header.php';

$loggedIn = isset($_SESSION['user_id']);
$verified = $_SESSION['is_verified'] ?? 0;
$role = $_SESSION['role'] ?? '';

if ($loggedIn && $verified) {
    require_once './model/user/UserModel.php';
    $posts = getLatestApprovedPosts(6);
    $wishlistIds = [];
    if ($role === 'user') {
        require_once './model/wishlist/WishlistModel.php';
        $wishlistItems = getWishlistByUser($_SESSION['user_id']);
        foreach ($wishlistItems as $wi) {
            $wishlistIds[] = $wi['post_id'];
        }
    }
}
?>

<div class="container">

    <?php if (!$loggedIn): ?>
        <!-- Non-registered users -->
        <div class="hero">
            <h1>🌍 Welcome to Travel Guide</h1>
            <p>Discover amazing places around the world. Get suggestions, travel costs, and traveller reviews.</p>
            <a href="./view/auth/register.php" class="btn btn-success" style="margin-right:10px;">Get Started</a>
            <a href="./view/auth/login.php" class="btn btn-primary">Login</a>
        </div>

        <div class="card" style="text-align:center;">
            <h2>Why Travel Guide?</h2>
            <p style="margin-top:12px;color:#666;">
                Explore beaches, mountains, historical sites and more — curated by our network of Scouts worldwide.
                Register as a General User to save your wishlist, post comments, and get cost estimates.
            </p>
        </div>

    <?php elseif (!$verified): ?>
        <!-- Logged in but not verified -->
        <div class="alert alert-warning" style="font-size:15px;">
            ⏳ <strong>Your account is pending admin approval.</strong>
            You will get full access once an admin verifies your account. Please check back later.
        </div>

        <div class="card" style="text-align:center;">
            <p style="color:#666;">While you wait, you can update your <a href="./view/profile/index.php">profile</a>.</p>
        </div>

    <?php else: ?>
        <!-- Verified user – show latest posts -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h2 style="color:#1a3c5e;">✈️ Latest Destinations</h2>
            <a href="./view/posts/browse.php" class="btn btn-primary">Browse All Posts →</a>
        </div>

        <?php if (empty($posts)): ?>
            <div class="alert alert-info">No approved posts yet. Check back soon!</div>
        <?php else: ?>
            <div class="post-grid">
                <?php foreach ($posts as $post): ?>
                    <?php $isAdded = in_array($post['id'], $wishlistIds); ?>
                    <div class="post-card">
                        <h3><?= htmlspecialchars($post['title']) ?></h3>
                        <p>📍 <strong><?= htmlspecialchars($post['country']) ?></strong></p>
                        <p>🏷️ <?= htmlspecialchars($post['genre']) ?></p>
                        <span class="badge badge-<?= $post['cost_level'] ?>">
                            <?= ucfirst($post['cost_level']) ?> Cost
                        </span>
                        <p style="margin-top:8px;font-size:13px;color:#555;line-height:1.5;">
                            <?= htmlspecialchars(substr($post['short_history'], 0, 100)) ?>…
                        </p>

                        <?php if ($role === 'user'): ?>
                            <button class="wishlist-btn <?= $isAdded ? 'added' : '' ?>" id="wb-<?= $post['id'] ?>"
                                onclick="toggleWishlist(<?= $post['id'] ?>, this)">
                                <?= $isAdded ? '♥ Saved!' : '♡ Save to Wishlist' ?>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>

<div id="toast"
    style="display:none;position:fixed;bottom:24px;right:24px;background:#333;color:#fff;padding:12px 20px;border-radius:6px;font-size:14px;z-index:999;">
</div>

<?php if ($loggedIn && $verified && $role === 'user'): ?>
    <script>
        function showToast(msg, isError = false) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.style.background = isError ? '#e05c3a' : '#28a745';
            t.style.display = 'block';
            setTimeout(() => t.style.display = 'none', 2500);
        }

        function toggleWishlist(postId, btn) {
            var isAdded = btn.classList.contains('added');
            var action = isAdded ? 'remove' : 'add';

            var xhttp = new XMLHttpRequest();
            xhttp.open('post', './controller/wishlist/WishlistController.php?action=' + action, true);
            xhttp.setRequestHeader("Content-type", "application/json");
            xhttp.send(JSON.stringify({ post_id: postId }));

            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    var data = JSON.parse(this.responseText);
                    if (data.success) {
                        if (isAdded) {
                            btn.classList.remove('added');
                            btn.textContent = '♡ Save to Wishlist';
                        } else {
                            btn.classList.add('added');
                            btn.textContent = '♥ Saved!';
                        }
                    }
                }
            }
        }
    </script>
<?php endif; ?>

<?php include './view/layout/footer.php'; ?>