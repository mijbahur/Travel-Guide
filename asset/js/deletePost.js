
function deleteRequest(id) {
    if (!confirm('Are you sure you want to delete this request? This cannot be undone.')) return;

    const btn = document.getElementById('del-' + id);
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span>';

    let xhttp = new XMLHttpRequest();
    xhttp.open('POST', '../../api/delete_request.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send('id=' + id + '&csrf_token=' + csrfToken);

    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const res = JSON.parse(this.responseText);
            const alert = document.getElementById('alertBox');
            if (res.success) {
                document.getElementById('row-' + id).remove();
                alert.innerHTML = '<div class="alert alert-success">' + res.message + '</div>';
            } else {
                alert.innerHTML = '<div class="alert alert-danger">' + res.message + '</div>';
                btn.disabled = false;
                btn.textContent = 'Delete';
            }
            setTimeout(() => alert.innerHTML = '', 3000);
        }
    };
}