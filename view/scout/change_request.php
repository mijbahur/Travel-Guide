<?php

require_once __DIR__ . '/../../config/auth/auth.php';
requireVerified();

$activePage = 'approved';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request Change - Travel Guide</title>
    <link rel="stylesheet" href="../../asset/css/style.css">
</head>

<body>
    <?php include 'partials/navbar.php'; ?>

    <div class="container">
        <h1 class="page-title">&#128260; Request Change for "<?= htmlspecialchars($post['title']) ?>"</h1>
        <div class="alert alert-info">Fill in the updated information below. Admin will review and apply your changes.
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" action="../../controller/scout/changeRequestController.php?post_id=<?= $postId ?>"
                enctype="multipart/form-data" id="changeForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="original_post_id" value="<?= $postId ?>">

                <div class="form-group">
                    <label for="title">Updated Title *</label>
                    <input type="text" id="title" name="title"
                        class="form-control <?= isset($errors['title']) ? 'field-error' : '' ?>"
                        value="<?= htmlspecialchars($old['title'] ?? $post['title']) ?>">
                    <?php if (isset($errors['title'])): ?>
                        <div class="error-msg"><?= $errors['title'] ?></div>
                    <?php endif; ?>
                    <div class="error-msg" id="err-title"></div>
                </div>

                <div class="form-group">
                    <label for="country">Country *</label>
                    <input type="text" id="country" name="country"
                        class="form-control <?= isset($errors['country']) ? 'field-error' : '' ?>"
                        value="<?= htmlspecialchars($old['country'] ?? $post['country']) ?>">
                    <?php if (isset($errors['country'])): ?>
                        <div class="error-msg"><?= $errors['country'] ?></div>
                    <?php endif; ?>
                    <div class="error-msg" id="err-country"></div>
                </div>

                <div class="form-group">
                    <label for="genre">Genre *</label>
                    <select id="genre" name="genre"
                        class="form-control <?= isset($errors['genre']) ? 'field-error' : '' ?>">
                        <option value="">-- Select Genre --</option>
                        <?php foreach (['beach', 'mountain', 'city', 'historical', 'adventure', 'nature', 'cultural'] as $g): ?>
                            <option value="<?= $g ?>" <?= ($old['genre'] ?? $post['genre']) === $g ? 'selected' : '' ?>>
                                <?= ucfirst($g) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['genre'])): ?>
                        <div class="error-msg"><?= $errors['genre'] ?></div>
                    <?php endif; ?>
                    <div class="error-msg" id="err-genre"></div>
                </div>

                <div class="form-group">
                    <label>Cost Level *</label>
                    <div class="cost-level">
                        <?php foreach (['low', 'medium', 'high'] as $c): ?>
                            <label style="font-weight:400;cursor:pointer;display:flex;align-items:center;gap:6px;">
                                <input type="radio" name="cost_level" value="<?= $c ?>" 
                                <?= ($old['cost_level'] ?? $post['cost_level']) === $c ? 'checked' : '' ?>>
                                <?= ucfirst($c) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php if (isset($errors['cost'])): ?>
                        <div class="error-msg"><?= $errors['cost'] ?></div>
                    <?php endif; ?>
                    <div class="error-msg" id="err-cost"></div>
                </div>

                <div class="form-group">
                    <label for="travel_medium_info">Travel Medium Info *</label>
                    <input type="text" id="travel_medium_info" name="travel_medium_info"
                        class="form-control <?= isset($errors['travel']) ? 'field-error' : '' ?>"
                        value="<?= htmlspecialchars($old['travel_medium_info'] ?? $post['travel_medium_info']) ?>">
                    <?php if (isset($errors['travel'])): ?>
                        <div class="error-msg"><?= $errors['travel'] ?></div>
                    <?php endif; ?>
                    <div class="error-msg" id="err-travel"></div>
                </div>

                <div class="form-group">
                    <label for="history">Updated Short History *</label>
                    <textarea id="history" name="history"
                        class="form-control <?= isset($errors['history']) ? 'field-error' : '' ?>"><?= htmlspecialchars($old['history'] ?? $post['short_history']) ?></textarea>
                    <?php if (isset($errors['history'])): ?>
                        <div class="error-msg"><?= $errors['history'] ?></div>
                    <?php endif; ?>
                    <div class="error-msg" id="err-history"></div>
                </div>

                <div class="form-group">
                    <label for="change_reason">Reason for Change *</label>
                    <textarea id="change_reason" name="change_reason"
                        class="form-control <?= isset($errors['reason']) ? 'field-error' : '' ?>"
                        placeholder="Explain what you updated and why..."><?= htmlspecialchars($old['change_reason'] ?? '') ?></textarea>
                    <?php if (isset($errors['reason'])): ?>
                        <div class="error-msg"><?= $errors['reason'] ?></div>
                    <?php endif; ?>
                    <div class="error-msg" id="err-reason"></div>
                </div>

                <div class="form-group">
                    <label for="post_image">New Image (optional, max 2 MB)</label>
                    <input type="file" id="post_image" name="post_image" class="form-control"
                        accept="image/jpeg,image/png,image/gif,image/webp">
                    <?php if (isset($errors['post_image'])): ?>
                        <div class="error-msg"><?= $errors['post_image'] ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Submit Change Request</button>
                <a href="approved_posts.php" class="btn btn-warning" style="margin-left:.7rem">Cancel</a>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('changeForm').addEventListener('submit', function (e) {
            let valid = true;
            const checks = [
                { id: 'title', errId: 'err-title', msg: 'Title is required.' },
                { id: 'country', errId: 'err-country', msg: 'Country is required.' },
                { id: 'travel_medium_info', errId: 'err-travel', msg: 'Travel medium is required.' },
                { id: 'history', errId: 'err-history', msg: 'History is required.' },
                { id: 'change_reason', errId: 'err-reason', msg: 'Please provide a reason.' },
            ];
            checks.forEach(({ id, errId, msg }) => {
                const el = document.getElementById(id);
                const err = document.getElementById(errId);
                err.textContent = '';
                el.classList.remove('field-error');
                if (!el.value.trim()) {
                    err.textContent = msg;
                    el.classList.add('field-error');
                    valid = false;
                }
            });
            if (!document.getElementById('genre').value) {
                document.getElementById('err-genre').textContent = 'Select a genre.';
                valid = false;
            }
            if (!document.querySelector('input[name="cost_level"]:checked')) {
                document.getElementById('err-cost').textContent = 'Select a cost level.';
                valid = false;
            }
            if (!valid) e.preventDefault();
        });
    </script>
</body>

</html>

<?php include '../../view/layout/footer.php'; ?>