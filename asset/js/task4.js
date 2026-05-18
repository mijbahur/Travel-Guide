/* ===================================
   Task 4: Travel Guide - Browse & Details Pages JavaScript
   =================================== */

// ============ Browse Page Functions ============

// Escape HTML to prevent XSS attacks
function escapeHtml(value) {
    if (typeof value !== 'string') {
        value = String(value);
    }
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Render posts in the grid (used by browse.php)
function renderPosts(posts) {
    const postGrid = document.getElementById('postGrid');
    if (!postGrid) return;

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

// Request posts from controller
async function requestPosts(action, params) {
    const response = await fetch(`../../controllers/Task4/Task4PostController.php?action=${action}&${params}`);
    return await response.json();
}

// Update search results
async function updateSearch() {
    const searchBox = document.getElementById('searchBox');
    if (!searchBox) return;

    const params = new URLSearchParams({ q: searchBox.value.trim() });
    const data = await requestPosts('search', params.toString());
    renderPosts(data.success ? data.posts : []);
}

// Update filter results
async function updateFilter() {
    const countryFilter = document.getElementById('countryFilter');
    const genreFilter = document.getElementById('genreFilter');
    const costFilter = document.getElementById('costFilter');
    if (!countryFilter || !genreFilter || !costFilter) return;

    const params = new URLSearchParams();
    if (countryFilter.value) {
        params.append('country', countryFilter.value);
    }
    if (genreFilter.value) {
        params.append('genre', genreFilter.value);
    }
    if (costFilter.value) {
        params.append('cost_level', costFilter.value);
    }
    const data = await requestPosts('filter', params.toString());
    renderPosts(data.success ? data.posts : []);
}

// Initialize browse page event listeners
function initBrowsePage() {
    const searchBox = document.getElementById('searchBox');
    const countryFilter = document.getElementById('countryFilter');
    const genreFilter = document.getElementById('genreFilter');
    const costFilter = document.getElementById('costFilter');
    const resetFilters = document.getElementById('resetFilters');

    if (searchBox) {
        searchBox.addEventListener('input', updateSearch);
    }
    if (countryFilter) {
        countryFilter.addEventListener('change', updateFilter);
    }
    if (genreFilter) {
        genreFilter.addEventListener('change', updateFilter);
    }
    if (costFilter) {
        costFilter.addEventListener('change', updateFilter);
    }
    if (resetFilters) {
        resetFilters.addEventListener('click', () => {
            if (searchBox) searchBox.value = '';
            if (countryFilter) countryFilter.value = '';
            if (genreFilter) genreFilter.value = '';
            if (costFilter) costFilter.value = '';
            updateSearch();
        });
    }
}

// ============ Details Page Functions ============

// Add a comment
async function addComment(event) {
    event.preventDefault();
    const commentForm = document.getElementById('commentForm');
    const commentContent = document.getElementById('commentContent');
    const commentsList = document.getElementById('commentsList');
    if (!commentForm || !commentContent || !commentsList) return;

    const content = commentContent.value.trim();
    if (content.length === 0 || content.length > 500) {
        alert('Comment must be between 1 and 500 characters.');
        return;
    }

    const formData = new FormData(commentForm);
    try {
        const response = await fetch(`../../controllers/Task4/Task4CommentController.php?action=add`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            const commentDiv = document.createElement('div');
            commentDiv.className = 'comment';
            commentDiv.setAttribute('data-comment-id', data.comment.id);
            commentDiv.innerHTML = `
                <div class="comment-header">
                    <strong>${escapeHtml(data.comment.name)}</strong>
                    <span>${escapeHtml(new Date().toLocaleString())}</span>
                    <button class="delete-comment-btn" data-comment-id="${data.comment.id}">Delete</button>
                </div>
                <div class="comment-content">
                    ${escapeHtml(data.comment.content)}
                </div>
            `;
            commentsList.appendChild(commentDiv);
            commentDiv.querySelector('.delete-comment-btn').addEventListener('click', deleteComment);
            commentContent.value = '';
        } else {
            alert(data.message || 'Unable to add comment.');
        }
    } catch (error) {
        console.error('Add comment error:', error);
        alert('An error occurred while adding the comment.');
    }
}

// Delete a comment
async function deleteComment(event) {
    const button = event.currentTarget;
    const commentId = button.getAttribute('data-comment-id');
    if (!confirm('Delete this comment?')) {
        return;
    }

    const formData = new FormData();
    formData.append('comment_id', commentId);

    try {
        const response = await fetch(`../../controllers/Task4/Task4CommentController.php?action=delete`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            button.closest('.comment').remove();
        } else {
            alert(data.message || 'Unable to delete comment.');
        }
    } catch (error) {
        console.error('Delete comment error:', error);
        alert('An error occurred while deleting the comment.');
    }
}

// Attach delete listeners to all delete buttons
function attachDeleteListeners() {
    document.querySelectorAll('.delete-comment-btn').forEach(button => {
        button.addEventListener('click', deleteComment);
    });
}

// Calculate cost estimate
async function calculateCost() {
    const travelersInput = document.getElementById('travelers');
    const daysInput = document.getElementById('days');
    const postIdInput = document.getElementById('postId');
    const costResult = document.getElementById('costResult');
    if (!travelersInput || !daysInput || !postIdInput || !costResult) return;

    const travelers = parseInt(travelersInput.value, 10);
    const days = parseInt(daysInput.value, 10);
    const postId = postIdInput.value;

    if (travelers < 1 || travelers > 10) {
        alert('Travelers must be between 1 and 10.');
        return;
    }
    if (days < 1) {
        alert('Days must be at least 1.');
        return;
    }

    const formData = new FormData();
    formData.append('post_id', postId);
    formData.append('travelers', travelers);
    formData.append('days', days);

    try {
        const response = await fetch(`../../controllers/Task4/Task4CostController.php?action=estimate`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            costResult.innerHTML = `
                <h4>Cost Estimate</h4>
                <p><strong>Base Cost:</strong> ${escapeHtml(data.currency)} ${escapeHtml(data.base_cost.toString())}</p>
                <p><strong>Travelers:</strong> ${escapeHtml(data.travelers.toString())}</p>
                <p><strong>Days:</strong> ${escapeHtml(data.days.toString())}</p>
                <p><strong>Total Estimated Cost:</strong> ${escapeHtml(data.currency)} ${escapeHtml(data.total.toString())}</p>
            `;
        } else {
            alert(data.message || 'Unable to calculate cost.');
        }
    } catch (error) {
        console.error('Cost calculation error:', error);
        alert('An error occurred while calculating the cost.');
    }
}

// Initialize details page event listeners
function initDetailsPage() {
    const commentForm = document.getElementById('commentForm');
    const calculateCostButton = document.getElementById('calculateCost');

    if (commentForm) {
        commentForm.addEventListener('submit', addComment);
    }
    attachDeleteListeners();
    if (calculateCostButton) {
        calculateCostButton.addEventListener('click', calculateCost);
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    initBrowsePage();
    initDetailsPage();
});
