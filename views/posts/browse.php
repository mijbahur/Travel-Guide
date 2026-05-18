<?php
session_start();
require_once __DIR__ . '/../../models/Task4PostModel.php';

$posts = getApprovedPostsForBrowse();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Guide | Browse Posts</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eef2f7;
            color: #263238;
        }

        .container {
            width: min(1120px, 100%);
            margin: 0 auto;
            padding: 24px 18px 32px;
        }

        .page-title {
            text-align: center;
            margin-bottom: 24px;
            color: #1f3a5d;
            font-size: 2rem;
            letter-spacing: 0.02em;
        }

        .search-panel {
            background: #fff;
            border: 1px solid #dce3ea;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 12px 30px rgba(40, 58, 84, 0.08);
            margin-bottom: 28px;
        }

        .form-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-weight: 600;
            color: #4a5a6a;
            font-size: 0.95rem;
        }

        input[type="text"], select {
            width: 100%;
            min-height: 44px;
            border: 1px solid #d7dde6;
            border-radius: 12px;
            padding: 12px 14px;
            background: #f8fbff;
            color: #27323f;
            font-size: 0.95rem;
        }

        input[type="text"]:focus,
        select:focus {
            outline: 2px solid rgba(74, 144, 226, 0.18);
            border-color: #4a90e2;
        }

        .btn-reset,
        .btn-primary {
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .btn-reset {
            background: #1f3a5d;
            color: #fff;
            padding: 12px 18px;
            min-height: 44px;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .btn-reset:hover {
            background: #16304b;
            transform: translateY(-1px);
        }

        .post-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 22px;
        }

        .card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #dce3ea;
            overflow: hidden;
            box-shadow: 0 14px 32px rgba(40, 58, 84, 0.08);
            display: flex;
            flex-direction: column;
            min-height: 320px;
        }

        .card-header {
            padding: 20px;
            background: #f7fbff;
        }

        .card-header h2 {
            font-size: 1.3rem;
            color: #1f3a5d;
            line-height: 1.2;
        }

        .card-body {
            padding: 18px 20px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .post-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }

        .badge {
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #fff;
        }

        .badge-country {
            background: #1f3a5d;
        }

        .badge-genre {
            background: #7b3b8a;
        }

        .badge-cost {
            background: #2d9a57;
        }

        .card-body p {
            color: #4a5b72;
            line-height: 1.7;
            font-size: 0.95rem;
            margin-bottom: 18px;
        }

        .card-footer {
            padding: 0 20px 20px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            background: #4a90e2;
            color: #fff;
            text-decoration: none;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background: #3a78c2;
            transform: translateY(-1px);
        }

        .empty-message {
            grid-column: 1 / -1;
            color: #5e6f85;
            padding: 32px 0;
            text-align: center;
            font-size: 1rem;
        }

        @media (max-width: 900px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 18px 14px 26px;
            }
        }
    </style>
</head>
<body>
    <main class="container">
        <header class="page-title">
            <h1>Travel Guide: Approved Posts</h1>
        </header>

        <section class="search-panel" aria-label="Post filters">
            <div class="form-grid">
                <div class="form-group">
                    <label for="searchBox">Search</label>
                    <input id="searchBox" type="text" placeholder="Find posts by title or country" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="countryFilter">Country</label>
                    <select id="countryFilter">
                        <option value="">All Countries</option>
                        <option value="USA">USA</option>
                        <option value="France">France</option>
                        <option value="Japan">Japan</option>
                        <option value="Italy">Italy</option>
                        <option value="Thailand">Thailand</option>
                        <option value="Germany">Germany</option>
                        <option value="Spain">Spain</option>
                        <option value="India">India</option>
                        <option value="Egypt">Egypt</option>
                        <option value="Australia">Australia</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="genreFilter">Genre</label>
                    <select id="genreFilter">
                        <option value="">All Genres</option>
                        <option value="Adventure">Adventure</option>
                        <option value="Cultural">Cultural</option>
                        <option value="Beach">Beach</option>
                        <option value="Historical">Historical</option>
                        <option value="Nature">Nature</option>
                        <option value="Urban">Urban</option>
                        <option value="Religious">Religious</option>
                        <option value="Wildlife">Wildlife</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="costFilter">Cost Level</label>
                    <select id="costFilter">
                        <option value="">All Costs</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <button id="resetFilters" class="btn-reset" type="button">Reset Filters</button>
            </div>
        </section>

        <section class="post-grid" id="postGrid">
            <?php if (empty($posts)): ?>
                <p class="empty-message">No approved posts found.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="card post-card">
                        <div class="card-header">
                            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                        </div>
                        <div class="card-body">
                            <div class="post-meta">
                                <span class="badge badge-country"><?php echo htmlspecialchars($post['country']); ?></span>
                                <span class="badge badge-genre"><?php echo htmlspecialchars($post['genre']); ?></span>
                                <span class="badge badge-cost cost-<?php echo htmlspecialchars($post['cost_level']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($post['cost_level'])); ?>
                                </span>
                            </div>
                            <p><?php echo htmlspecialchars(mb_substr($post['short_history'], 0, 140)); ?>...</p>
                        </div>
                        <div class="card-footer">
                            <a class="btn btn-primary" href="details.php?id=<?php echo (int) $post['id']; ?>">Read More</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <script>
        const searchBox = document.getElementById('searchBox');
        const countryFilter = document.getElementById('countryFilter');
        const genreFilter = document.getElementById('genreFilter');
        const resetFilters = document.getElementById('resetFilters');
        const postGrid = document.getElementById('postGrid');

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderPosts(posts) {
            if (!posts || posts.length === 0) {
                postGrid.innerHTML = '<p class="empty-message">No approved posts found.</p>';
                return;
            }

            postGrid.innerHTML = posts.map(post => {
                const title = escapeHtml(post.title);
                const country = escapeHtml(post.country);
                const genre = escapeHtml(post.genre);
                const costLevel = escapeHtml(post.cost_level);
                const excerpt = escapeHtml(post.short_history.substring(0, 140));

                return `
                    <article class="card post-card">
                        <div class="card-header">
                            <h2>${title}</h2>
                        </div>
                        <div class="card-body">
                            <div class="post-meta">
                                <span class="badge badge-country">${country}</span>
                                <span class="badge badge-genre">${genre}</span>
                                <span class="badge badge-cost cost-${costLevel}">${costLevel.charAt(0).toUpperCase() + costLevel.slice(1)}</span>
                            </div>
                            <p>${excerpt}...</p>
                        </div>
                        <div class="card-footer">
                            <a class="btn btn-primary" href="details.php?id=${encodeURIComponent(post.id)}">Read More</a>
                        </div>
                    </article>
                `;
            }).join('');
        }

        async function requestPosts(action, params) {
            const response = await fetch(`../../controllers/Task4PostController.php?action=${action}&${params}`);
            return await response.json();
        }

        async function updateSearch() {
            const params = new URLSearchParams({ q: searchBox.value.trim() });
            const data = await requestPosts('search', params.toString());
            renderPosts(data.success ? data.posts : []);
        }

        async function updateFilter() {
            const params = new URLSearchParams();
            if (countryFilter.value) {
                params.append('country', countryFilter.value);
            }
            if (genreFilter.value) {
                params.append('genre', genreFilter.value);
            }
            const data = await requestPosts('filter', params.toString());
            renderPosts(data.success ? data.posts : []);
        }

        searchBox.addEventListener('input', updateSearch);
        countryFilter.addEventListener('change', updateFilter);
        genreFilter.addEventListener('change', updateFilter);
        resetFilters.addEventListener('click', () => {
            searchBox.value = '';
            countryFilter.value = '';
            genreFilter.value = '';
            updateSearch();
        });
    </script>
</body>
</html>