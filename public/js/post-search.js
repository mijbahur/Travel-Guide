// Task 4 JavaScript - Search and filter posts via AJAX calls.

// DOM elements
const searchBox = document.getElementById('searchBox');
const countryFilter = document.getElementById('countryFilter');
const genreFilter = document.getElementById('genreFilter');
const costFilter = document.getElementById('costFilter');
const resetFilters = document.getElementById('resetFilters');
const postGrid = document.getElementById('postGrid');

// Escape HTML to prevent XSS
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Render posts in the grid
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

// Fetch and display search results
async function performSearch() {
    const query = searchBox.value.trim();
    
    try {
        const response = await fetch(`../../api/posts/search.php?q=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (data.success) {
            renderPosts(data.posts);
        } else {
            postGrid.innerHTML = '<p class="no-posts">Error loading posts.</p>';
        }
    } catch (error) {
        console.error('Search error:', error);
        postGrid.innerHTML = '<p class="no-posts">Error loading posts.</p>';
    }
}

// Fetch and display filtered results
async function performFilter() {
    const country = countryFilter.value;
    const genre = genreFilter.value;
    const costLevel = costFilter.value;
    
    const params = new URLSearchParams();
    if (country) params.append('country', country);
    if (genre) params.append('genre', genre);
    if (costLevel) params.append('cost_level', costLevel);
    
    try {
        const response = await fetch(`../../api/posts/filter.php?${params.toString()}`);
        const data = await response.json();
        
        if (data.success) {
            renderPosts(data.posts);
        } else {
            postGrid.innerHTML = '<p class="no-posts">Error loading posts.</p>';
        }
    } catch (error) {
        console.error('Filter error:', error);
        postGrid.innerHTML = '<p class="no-posts">Error loading posts.</p>';
    }
}

// Reset all filters and reload all posts
async function resetAllFilters() {
    searchBox.value = '';
    countryFilter.value = '';
    genreFilter.value = '';
    costFilter.value = '';
    
    try {
        const response = await fetch('../../api/posts/search.php?q=');
        const data = await response.json();
        
        if (data.success) {
            renderPosts(data.posts);
        }
    } catch (error) {
        console.error('Reset error:', error);
    }
}

// Event listeners
searchBox.addEventListener('input', performSearch);
countryFilter.addEventListener('change', performFilter);
genreFilter.addEventListener('change', performFilter);
costFilter.addEventListener('change', performFilter);
resetFilters.addEventListener('click', resetAllFilters);
