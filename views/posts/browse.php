<?php
session_start();
require_once __DIR__ . '/../../models/Task4PostModel.php';

// Fetch all approved posts for initial page load
$posts = getApprovedPostsForBrowse();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Travel Posts</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eef2f7;
            color: #2a3a4a;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 25px 20px 40px;
        }

        h1 {
            text-align: center;
            color: #1f3a5d;
            margin-bottom: 25px;
            font-size: 2.2rem;
            letter-spacing: 0.02em;
        }

        .search-filter-section {
            background: #ffffff;
            border-radius: 18px;
            padding: 24px;
            margin-bottom: 32px;
            box-shadow: 0 18px 40px rgba(41, 56, 77, 0.08);
            border: 1px solid rgba(34, 60, 80, 0.08);
        }

        .search-box {
            margin-bottom: 24px;
        }

        .search-box input {
            width: 100%;
            padding: 14px 18px;
            font-size: 16px;
            border: 1px solid #d7dde6;
            border-radius: 14px;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
            background-color: #f8fbff;
        }

        .search-box input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 4px rgba(74, 144, 226, 0.12);
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(4, minmax(170px, 1fr));
            gap: 18px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            color: #465a74;
            font-size: 14px;
            font-weight: 600;
        }

        .filter-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d7dde6;
            border-radius: 12px;
            background-color: #fafcff;
            color: #2a3a4a;
            font-size: 14px;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .filter-group select:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 4px rgba(74, 144, 226, 0.12);
        }

        .btn-reset {
            width: 100%;
            padding: 14px 18px;
            margin-top: 6px;
            background: #1f3a5d;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.25s ease, transform 0.2s ease;
        }

        .btn-reset:hover {
            background: #16304b;
            transform: translateY(-1px);
        }

        .post-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 10px;
        }

        .post-card {
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid rgba(34, 60, 80, 0.08);
            box-shadow: 0 12px 30px rgba(34, 60, 80, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            display: flex;
            flex-direction: column;
            min-height: 320px;
        }

        .post-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 26px 45px rgba(34, 60, 80, 0.12);
        }

        .post-header {
            padding: 22px 20px 16px;
            background-color: #f7fbff;
        }

        .post-header h2 {
            font-size: 1.35rem;
            color: #1f3a5d;
            line-height: 1.35;
        }

        .post-meta {
            padding: 0 20px 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .badge-country {
            background-color: #e8f5ff;
            color: #165d9f;
        }

        .badge-genre {
            background-color: #f6edf9;
            color: #7b3b8a;
        }

        .badge-cost {
            color: #ffffff;
        }

        .cost-low {
            background-color: #2d9a57;
        }

        .cost-medium {
            background-color: #e18f24;
        }

        .cost-high {
            background-color: #d64545;
        }

        .post-content {
            padding: 0 20px 20px;
            flex-grow: 1;
        }

        .post-content p {
            line-height: 1.8;
            color: #4a5b72;
            font-size: 0.96rem;
        }

        .post-footer {
            padding: 18px 20px 22px;
            border-top: 1px solid rgba(34, 60, 80, 0.06);
            background-color: #fbfdff;
        }

        .btn-read-more {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            background-color: #4a90e2;
            color: #ffffff;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            transition: background-color 0.25s ease, transform 0.2s ease;
        }

        .btn-read-more:hover {
            background-color: #3a78c2;
            transform: translateY(-1px);
        }

        .no-posts {
            text-align: center;
            padding: 40px 20px;
            color: #62738f;
            font-size: 1rem;
            grid-column: 1 / -1;
        }

        @media (max-width: 900px) {
            .filters {
                grid-template-columns: repeat(2, minmax(170px, 1fr));
            }
        }

        @media (max-width: 700px) {
            .container {
                padding: 18px 14px 30px;
            }
            
            h1 {
                font-size: 1.9rem;
                margin-bottom: 20px;
            }
            
            .search-filter-section {
                padding: 20px;
            }
            
            .filters {
                grid-template-columns: 1fr;
            }
            
            .btn-reset {
                width: 100%;
            }
            
            .post-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        @media (max-width: 480px) {
            .search-box input,
            .filter-group select {
                font-size: 15px;
            }
            
            .post-header h2 {
                font-size: 1.2rem;
            }
            
            .post-content p {
                font-size: 0.95rem;
            }
            
            .btn-read-more {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Browse Travel Posts</h1>
        
        <div class="search-filter-section">
            <div class="search-box">
                <input 
                    type="text" 
                    id="searchBox" 
                    placeholder="Search by title or country..." 
                    autocomplete="off"
                >
            </div>
            
            <div class="filters">
                <div class="filter-group">
                    <label for="countryFilter">Country:</label>
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
                
                <div class="filter-group">
                    <label for="genreFilter">Genre:</label>
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
                
                <div class="filter-group">
                    <label for="costFilter">Cost Level:</label>
                    <select id="costFilter">
                        <option value="">All Costs</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                
                <button id="resetFilters" class="btn-reset">Reset Filters</button>
            </div>
        </div>
        
        <div id="postGrid" class="post-grid">
            <?php if (empty($posts)): ?>
                <p class="no-posts">No posts found.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                        </div>
                        <div class="post-meta">
                            <span class="badge badge-country"><?php echo htmlspecialchars($post['country']); ?></span>
                            <span class="badge badge-genre"><?php echo htmlspecialchars($post['genre']); ?></span>
                            <span class="badge badge-cost cost-<?php echo htmlspecialchars($post['cost_level']); ?>">
                                <?php echo htmlspecialchars(ucfirst($post['cost_level'])); ?>
                            </span>
                        </div>
                        <div class="post-content">
                            <p><?php echo htmlspecialchars(substr($post['short_history'], 0, 150)); ?>...</p>
                        </div>
                        <div class="post-footer">
                            <a href="details.php?id=<?php echo (int)$post['id']; ?>" class="btn-read-more">
                                Read More
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const searchBox = document.getElementById('searchBox');
        const countryFilter = document.getElementById('countryFilter');
        const genreFilter = document.getElementById('genreFilter');
        const costFilter = document.getElementById('costFilter');
        const resetFilters = document.getElementById('resetFilters');
        const postGrid = document.getElementById('postGrid');

        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderPosts(posts) {
            if (!posts || posts.length === 0) {
                postGrid.innerHTML = '<p class="no-posts">No posts found.</p>';
                return;
            }

            postGrid.innerHTML = posts.map(post => `
                <div class="post-card">
                    <div class="post-header">
                        <h2>${escapeHtml(post.title)}</h2>
                    </div>
                    <div class="post-meta">
                        <span class="badge badge-country">${escapeHtml(post.country)}</span>
                        <span class="badge badge-genre">${escapeHtml(post.genre)}</span>
                        <span class="badge badge-cost cost-${escapeHtml(post.cost_level)}">
                            ${escapeHtml(post.cost_level.charAt(0).toUpperCase() + post.cost_level.slice(1))}
                        </span>
                    </div>
                    <div class="post-content">
                        <p>${escapeHtml(post.short_history.substring(0, 150))}...</p>
                    </div>
                    <div class="post-footer">
                        <a href="details.php?id=${post.id}" class="btn-read-more">Read More</a>
                    </div>
                </div>
            `).join('');
        }

        async function performSearch() {
            const query = searchBox.value.trim();
            try {
                const response = await fetch(`../../controllers/Task4PostController.php?action=search&q=${encodeURIComponent(query)}`);
                const data = await response.json();
                if (data.success) {
                    renderPosts(data.posts);
                } else {
                    postGrid.innerHTML = '<p class="no-posts">No posts found.</p>';
                }
            } catch (error) {
                console.error('Search error:', error);
                postGrid.innerHTML = '<p class="no-posts">No posts found.</p>';
            }
        }

        async function performFilter() {
            const country = countryFilter.value;
            const genre = genreFilter.value;
            const costLevel = costFilter.value;
            const params = new URLSearchParams();
            if (country) params.append('country', country);
            if (genre) params.append('genre', genre);
            if (costLevel) params.append('cost_level', costLevel);

            try {
                const response = await fetch(`../../controllers/Task4PostController.php?action=filter&${params.toString()}`);
                const data = await response.json();
                if (data.success) {
                    renderPosts(data.posts);
                } else {
                    postGrid.innerHTML = '<p class="no-posts">No posts found.</p>';
                }
            } catch (error) {
                console.error('Filter error:', error);
                postGrid.innerHTML = '<p class="no-posts">No posts found.</p>';
            }
        }

        async function resetAllFilters() {
            searchBox.value = '';
            countryFilter.value = '';
            genreFilter.value = '';
            costFilter.value = '';
            await performSearch();
        }

        searchBox.addEventListener('input', performSearch);
        countryFilter.addEventListener('change', performFilter);
        genreFilter.addEventListener('change', performFilter);
        costFilter.addEventListener('change', performFilter);
        resetFilters.addEventListener('click', resetAllFilters);
    </script>
</body>
</html>
