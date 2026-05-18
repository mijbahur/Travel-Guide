<?php
session_start();
require_once __DIR__ . '/../../config/auth.php';
requireVerified();
require_once __DIR__ . '/../../model/postRequestModel.php';

$id = (int) ($_GET['id'] ?? 0);
$req = getRequestById($id);

if (!$req || $req['scout_id'] != $_SESSION['user_id'] || $req['status'] !== 'pending') {
    header('Location: my_requests.php');
    exit;
}

$postData = json_decode($req['post_data'], true);
$activePage = 'my-requests';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Request - Travel Guide</title>
    <link rel="stylesheet" href="../../asset/css/style.css">
</head>

<body>
    <?php include 'partials/navbar.php'; ?>

    <div class="container">
        <h1 class="page-title">&#9999; Edit Post Request</h1>

        <div id="alertBox"></div>

        <div class="card">
            <form method="POST" action="../../controller/scout/update_request.php" enctype="multipart/form-data" id="editForm"
                novalidate>
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <!-- keep existing image path -->
                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($postData['image'] ?? '') ?>">

                <div class="form-group">
                    <label for="title">Place Title *</label>
                    <input type="text" id="title" name="title" class="form-control"
                        value="<?= htmlspecialchars($postData['title'] ?? '') ?>">
                    <div class="error-msg" id="err-title"></div>
                </div>

                <div class="form-group">
                    <label for="country">Country *</label>
                    <input type="text" id="country" name="country" class="form-control"
                        value="<?= htmlspecialchars($postData['country'] ?? '') ?>">
                    <div class="error-msg" id="err-country"></div>
                </div>

                <div class="form-group">
                    <label for="genre">Genre *</label>
                    <select id="genre" name="genre" class="form-control">
                        <option value="">-- Select Genre --</option>
                        <?php foreach (['beach', 'mountain', 'city', 'historical', 'adventure', 'nature', 'cultural'] as $g): ?>
                            <option value="<?= $g ?>" <?= ($postData['genre'] ?? '') === $g ? 'selected' : '' ?>><?= ucfirst($g) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="error-msg" id="err-genre"></div>
                </div>

                <div class="form-group">
                    <label>Cost Level *</label>
                    <div class="d-flex gap-2 mt-1">
                        <?php foreach (['low', 'medium', 'high'] as $c): ?>
                            <label style="font-weight:400;cursor:pointer">
                                <input type="radio" name="cost_level" value="<?= $c ?>"
                                    <?= ($postData['cost_level'] ?? '') === $c ? 'checked' : '' ?>>
                                <?= ucfirst($c) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="error-msg" id="err-cost"></div>
                </div>

                <div class="form-group">
                    <label for="travel_medium_info">Travel Medium Info *</label>
                    <input type="text" id="travel_medium_info" name="travel_medium_info" class="form-control"
                        value="<?= htmlspecialchars($postData['travel'] ?? '') ?>">
                    <div class="error-msg" id="err-travel"></div>
                </div>

                <div class="form-group">
                    <label for="history">Short History / Country Representation *</label>
                    <textarea id="history" name="history"
                        class="form-control"><?= htmlspecialchars($postData['history'] ?? '') ?></textarea>
                    <small class="text-muted" id="charCount">0 / 2000 characters</small>
                    <div class="error-msg" id="err-history"></div>
                </div>

                <?php if (!empty($postData['image'])): ?>
                    <div class="form-group">
                        <label>Current Image</label><br>
                        <img src="../../<?= htmlspecialchars($postData['image']) ?>"
                            style="max-width:180px;border-radius:6px;border:1px solid #cbd5e0">
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="post_image">Replace Image (optional, max 2 MB)</label>
                    <input type="file" id="post_image" name="post_image" class="form-control"
                        accept="image/jpeg,image/png,image/gif,image/webp">
                    <div id="imagePreview" style="margin-top:.5rem"></div>
                    <div class="error-msg" id="err-image"></div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">Save Changes</button>
                <a href="my_requests.php" class="btn btn-warning" style="margin-left:.7rem">Cancel</a>
            </form>
        </div>
    </div>

    <script>
        const historyTa = document.getElementById('history');
        const charCount = document.getElementById('charCount');
        historyTa.addEventListener('input', () => {
            charCount.textContent = historyTa.value.length + ' / 2000 characters';
        });
        // Image preview
        document.getElementById('post_image').addEventListener('change', function () {
            const preview = document.getElementById('imagePreview');
            const errImg = document.getElementById('err-image');
            preview.innerHTML = '';
            errImg.textContent = '';
            if (!this.files.length) return;
            const file = this.files[0];
            if (!['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(file.type)) {
                errImg.textContent = 'Invalid file type.';
                this.value = ''; return;
            }
            if (file.size > 2 * 1024 * 1024) {
                errImg.textContent = 'File too large. Max 2 MB.';
                this.value = ''; return;
            }
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style = 'max-width:180px;border-radius:6px;border:1px solid #cbd5e0';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });

        const form = document.getElementById('editForm');
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            let valid = true;
            const checks = [
                { id: 'title', errId: 'err-title', msg: 'Title is required.' },
                { id: 'country', errId: 'err-country', msg: 'Country is required.' },
                { id: 'travel_medium_info', errId: 'err-travel', msg: 'Travel medium is required.' },
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
            const errG = document.getElementById('err-genre');
            errG.textContent = '';
            if (!genre.value) { errG.textContent = 'Select a genre.'; valid = false; }

            const costChecked = document.querySelector('input[name="cost_level"]:checked');
            if (!costChecked) {
                document.getElementById('err-cost').textContent = 'Select a cost level.';
                valid = false;
            }
            if (historyTa.value.length > 2000) {
                document.getElementById('err-history').textContent = 'History must be under 2000 characters.';
                valid = false;
            }

            if (!valid) return;

            // AJAX submit using FormData
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span> Saving...';

            const fd = new FormData(form);

            let xhr = new XMLHttpRequest();
            xhr.open('POST', '../../controller/scout/update_request.php', true);
            xhr.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let res;
                    try { res = JSON.parse(this.responseText); } catch { res = { success: false, message: 'Server error.' }; }
                    const alertBox = document.getElementById('alertBox');
                    if (res.success) {
                        alertBox.innerHTML = '<div class="alert alert-success">' + res.message + '</div>';
                        setTimeout(() => window.location.href = 'my_requests.php', 1500);
                    } else {
                        alertBox.innerHTML = '<div class="alert alert-danger">' + res.message + '</div>';
                        btn.disabled = false;
                        btn.textContent = 'Save Changes';
                    }
                }
            };
            xhr.send(fd);
        });
    </script>
</body>

</html>