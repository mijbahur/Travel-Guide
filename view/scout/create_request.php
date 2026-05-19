<?php
require_once __DIR__ . '/../../config/auth/auth.php';
requireRole('scout');

$activePage = 'create';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Post Request - Travel Guide</title>
    <link rel="stylesheet" href="../../asset/css/style.css">
</head>

<body>
    <?php include 'partials/navbar.php'; ?>

    <div class="container">
        <h1 class="page-title">&#128221; Create Post Request</h1>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" action="../../controller/scout/createRequestController.php"
                enctype="multipart/form-data" id="createForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="form-group">
                    <label for="title">Place Title *</label>
                    <input type="text" id="title" name="title"
                        class="form-control <?= isset($errors['title']) ? 'field-error' : '' ?>"
                        value="<?= htmlspecialchars($old['title'] ?? '') ?>"
                        placeholder="e.g. Sundarbans Mangrove Forest">
                    <?php if (isset($errors['title'])): ?>
                        <div class="error-msg"><?= $errors['title'] ?></div>
                    <?php endif; ?>
                    <div class="error-msg" id="err-title"></div>
                </div>

                <div class="form-group">
                    <label for="country">Country *</label>
                    <input type="text" id="country" name="country"
                        class="form-control <?= isset($errors['country']) ? 'field-error' : '' ?>"
                        value="<?= htmlspecialchars($old['country'] ?? '') ?>" placeholder="e.g. Bangladesh">
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
                            <option value="<?= $g ?>" <?= ($old['genre'] ?? '') === $g ? 'selected' : '' ?>>
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
                                <input type="radio" name="cost_level" value="<?= $c ?>" <?= ($old['cost_level'] ?? '') === $c ? 'checked' : '' ?>>
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
                        value="<?= htmlspecialchars($old['travel_medium_info'] ?? '') ?>"
                        placeholder="e.g. Flight + Boat, Train">
                    <?php if (isset($errors['travel'])): ?>
                        <div class="error-msg"><?= $errors['travel'] ?></div>
                    <?php endif; ?>
                    <div class="error-msg" id="err-travel"></div>
                </div>

                <div class="form-group">
                    <label for="history">Short History / Country Representation *</label>
                    <textarea id="history" name="history"
                        class="form-control <?= isset($errors['history']) ? 'field-error' : '' ?>"
                        placeholder="Write about the place's significance, culture, and history..."><?= htmlspecialchars($old['history'] ?? '') ?></textarea>
                    <small class="text-muted" id="charCount">0 / 2000 characters</small>
                    <?php if (isset($errors['history'])): ?>
                        <div class="error-msg"><?= $errors['history'] ?></div>
                    <?php endif; ?>
                    <div class="error-msg" id="err-history"></div>
                </div>

                <div class="form-group">
                    <label for="post_image">Place Image (optional, max 2 MB)</label>
                    <input type="file" id="post_image" name="post_image"
                        class="form-control <?= isset($errors['post_image']) ? 'field-error' : '' ?>"
                        accept="image/jpeg,image/png,image/gif,image/webp">
                    <div id="imagePreview" style="margin-top:.5rem"></div>
                    <?php if (isset($errors['post_image'])): ?>
                        <div class="error-msg"><?= $errors['post_image'] ?></div>
                    <?php endif; ?>
                    <div class="error-msg" id="err-image"></div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">Submit Request</button>
                <a href="my_requests.php" class="btn btn-warning" style="margin-left:.7rem">Cancel</a>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('createForm');
        const historyTa = document.getElementById('history');
        const charCount = document.getElementById('charCount');

        historyTa.addEventListener('input', () => {
            charCount.textContent = historyTa.value.length + ' / 2000 characters';
        });

        document.getElementById('post_image').addEventListener('change', function () {
            const preview = document.getElementById('imagePreview');
            const errImg = document.getElementById('err-image');
            preview.innerHTML = '';
            errImg.textContent = '';
            if (!this.files.length) return;
            const file = this.files[0];
            if (!['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(file.type)) {
                errImg.textContent = 'Invalid file type. JPG, PNG, GIF, WEBP only.';
                this.value = ''; return;
            }
            if (file.size > 2 * 1024 * 1024) {
                errImg.textContent = 'File too large. Maximum 2 MB.';
                this.value = ''; return;
            }
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style = 'max-width:200px;max-height:120px;border-radius:6px;border:1px solid #cbd5e0';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });

        form.addEventListener('submit', function (e) {
            let valid = true;
            const checks = [
                { id: 'title', errId: 'err-title', msg: 'Title is required.' },
                { id: 'country', errId: 'err-country', msg: 'Country is required.' },
                { id: 'travel_medium_info', errId: 'err-travel', msg: 'Travel medium info is required.' },
                { id: 'history', errId: 'err-history', msg: 'Short history is required.' },
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
            const genre = document.getElementById('genre');
            const errGenre = document.getElementById('err-genre');
            errGenre.textContent = '';
            genre.classList.remove('field-error');
            if (!genre.value) {
                errGenre.textContent = 'Please select a genre.';
                genre.classList.add('field-error');
                valid = false;
            }
            const errCost = document.getElementById('err-cost');
            errCost.textContent = '';
            if (!document.querySelector('input[name="cost_level"]:checked')) {
                errCost.textContent = 'Please select a cost level.';
                valid = false;
            }
            if (historyTa.value.length > 2000) {
                document.getElementById('err-history').textContent = 'History must be under 2000 characters.';
                valid = false;
            }
            if (!valid) e.preventDefault();
        });
    </script>
</body>

</html>

<?php include '../../view/layout/footer.php'; ?>