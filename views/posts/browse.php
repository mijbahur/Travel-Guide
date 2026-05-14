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
    <link rel="stylesheet" href="../../public/css/posts.css">
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

    <script src="../../public/js/post-search.js"></script>
</body>
</html>
