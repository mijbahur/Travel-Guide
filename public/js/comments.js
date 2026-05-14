// Task 4 JavaScript - Add and delete comments via AJAX.

// Escape HTML to prevent XSS
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Add a new comment
document.getElementById('commentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const content = document.getElementById('commentContent').value.trim();
    
    // JS validation
    if (content.length === 0 || content.length > 500) {
        alert('Comment must be 1-500 characters.');
        return;
    }
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('../../api/comments/add.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Add comment to DOM
            const commentsList = document.getElementById('commentsList');
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
            
            // Clear form
            document.getElementById('commentContent').value = '';
            
            // Add delete event listener to new button
            commentDiv.querySelector('.delete-comment-btn').addEventListener('click', deleteComment);
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Add comment error:', error);
        alert('An error occurred while adding the comment.');
    }
});

// Delete a comment
function deleteComment(e) {
    const commentId = e.target.getAttribute('data-comment-id');
    
    if (confirm('Are you sure you want to delete this comment?')) {
        const formData = new FormData();
        formData.append('comment_id', commentId);
        
        fetch('../../api/comments/delete.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove comment from DOM
                e.target.closest('.comment').remove();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Delete comment error:', error);
            alert('An error occurred while deleting the comment.');
        });
    }
}

// Attach delete event listeners to existing delete buttons
document.querySelectorAll('.delete-comment-btn').forEach(btn => {
    btn.addEventListener('click', deleteComment);
});
