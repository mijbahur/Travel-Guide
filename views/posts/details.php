<?php
session_start();

// Demo session fallback for Task 4
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 4;
    $_SESSION['name'] = 'General User';
    $_SESSION['role'] = 'user';
    $_SESSION['is_verified'] = 1;
}

require_once __DIR__ . '/../../models/Task4PostModel.php';
require_once __DIR__ . '/../../models/Task4CommentModel.php';
require_once __DIR__ . '/../../models/Task4CostModel.php';

// Validate post ID
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($postId <= 0) {
    die('Invalid post ID.');
}

// Fetch post details
$post = getApprovedPostDetailsById($postId);
if (!$post) {
    echo '<p>Post not found or not approved.</p>';
    echo '<a href="browse.php">Back to Browse</a>';
    exit;
}

// Fetch comments
$comments = getCommentsByPostId($postId);

// Fetch base cost
$costData = getTask4BaseCost($postId, $post['cost_level']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Travel Guide</title>
    <link rel="stylesheet" href="../../public/css/posts.css">
</head>
<body>
    <div class="container">
        <a href="browse.php" class="back-link">&larr; Back to Browse</a>
        
        <div class="post-detail-card">
            <div class="post-header">
                <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                <div class="post-meta">
                    <span class="badge badge-country"><?php echo htmlspecialchars($post['country']); ?></span>
                    <span class="badge badge-genre"><?php echo htmlspecialchars($post['genre']); ?></span>
                    <span class="badge badge-cost cost-<?php echo htmlspecialchars($post['cost_level']); ?>">
                        <?php echo htmlspecialchars(ucfirst($post['cost_level'])); ?>
                    </span>
                </div>
            </div>
            
            <div class="post-content">
                <p><?php echo htmlspecialchars($post['short_history']); ?></p>
                <p><strong>Travel Medium Info:</strong> <?php echo htmlspecialchars($post['travel_medium_info']); ?></p>
                <?php if (!empty($post['image'])): ?>
                    <img src="../../public/uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
                <?php endif; ?>
            </div>
        </div>
        
        <div class="comments-section">
            <h2>Comments</h2>
            <div id="commentsList">
                <?php if (empty($comments)): ?>
                    <p>No comments yet. Be the first to comment!</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment" data-comment-id="<?php echo (int)$comment['id']; ?>">
                            <div class="comment-header">
                                <strong><?php echo htmlspecialchars($comment['name']); ?></strong>
                                <span><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($comment['created_at']))); ?></span>
                                <?php if ($comment['user_id'] == $_SESSION['user_id']): ?>
                                    <button class="delete-comment-btn" data-comment-id="<?php echo (int)$comment['id']; ?>">Delete</button>
                                <?php endif; ?>
                            </div>
                            <div class="comment-content">
                                <?php echo htmlspecialchars($comment['content']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="comment-form">
                <h3>Add a Comment</h3>
                <form id="commentForm">
                    <input type="hidden" name="post_id" value="<?php echo (int)$postId; ?>">
                    <textarea id="commentContent" name="content" placeholder="Write your comment..." maxlength="500" required></textarea>
                    <button type="submit" id="submitComment">Submit Comment</button>
                </form>
            </div>
        </div>
        
        <div class="cost-calculator-section">
            <h2>Probable Cost Estimate</h2>
            <div class="cost-calculator">
                <input type="hidden" id="postId" value="<?php echo (int)$postId; ?>">
                <input type="hidden" id="baseCost" value="<?php echo htmlspecialchars($costData['base_cost']); ?>">
                <input type="hidden" id="currency" value="<?php echo htmlspecialchars($costData['currency']); ?>">
                
                <div class="calculator-inputs">
                    <label for="travelers">Number of Travelers (1-10):</label>
                    <input type="number" id="travelers" min="1" max="10" value="1" required>
                    
                    <label for="days">Number of Days:</label>
                    <input type="number" id="days" min="1" value="1" required>
                    
                    <button id="calculateCost">Calculate Cost</button>
                </div>
                
                <div id="costResult" class="cost-result"></div>
            </div>
        </div>
    </div>

    <script src="../../public/js/comments.js"></script>
    <script src="../../public/js/cost-calculator.js"></script>
</body>
</html>
