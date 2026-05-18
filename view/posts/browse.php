<?php
session_start();
require_once __DIR__ . '/../../model/Task4PostModel.php';

$posts = getApprovedPostsForBrowse();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Guide | Browse Posts</title>
    <link rel="stylesheet" href="../../asset/css/task4.css">
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

    <script src="../../asset/js/task4.js"></script>
</body>
</html>
