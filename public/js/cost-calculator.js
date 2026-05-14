// Task 4 JavaScript - Calculate travel cost estimates based on travelers and duration.

document.getElementById('calculateCost').addEventListener('click', async function() {
    const travelers = parseInt(document.getElementById('travelers').value);
    const days = parseInt(document.getElementById('days').value);
    const postId = document.getElementById('postId').value;
    
    // JS validation
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
        const response = await fetch('../../api/cost/estimate.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            const resultDiv = document.getElementById('costResult');
            resultDiv.innerHTML = `
                <h4>Cost Estimate</h4>
                <p><strong>Base Cost:</strong> ${data.currency} ${data.base_cost}</p>
                <p><strong>Travelers:</strong> ${data.travelers}</p>
                <p><strong>Days:</strong> ${data.days}</p>
                <p><strong>Total Estimated Cost:</strong> ${data.currency} ${data.total}</p>
            `;
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Cost calculation error:', error);
        alert('An error occurred while calculating the cost.');
    }
});
