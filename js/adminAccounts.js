function openEditModal(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_full_name').value = user.full_name || '';
    document.getElementById('edit_phone').value = user.phone || '';
    document.getElementById('edit_address').value = user.address || '';
    document.getElementById('editUserModal').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function confirmDelete(userId) {
    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'custom-alert-overlay';
    
    // Create alert container
    const alertBox = document.createElement('div');
    alertBox.className = 'custom-alert';
    
    // Alert content
    alertBox.innerHTML = `
        <div class="custom-alert-title">
            <i class="fas fa-exclamation-triangle"></i> Delete Customer
        </div>
        <div class="custom-alert-message">
            Are you sure you want to delete this customer? This action cannot be undone.
        </div>
        <div class="custom-alert-buttons">
            <button class="btn btn-alert-cancel" onclick="closeDeleteAlert()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="btn btn-alert-delete" onclick="proceedWithDelete(${userId})">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    `;
    
    // Add to document
    document.body.appendChild(overlay);
    document.body.appendChild(alertBox);
}

function closeDeleteAlert() {
    const overlay = document.querySelector('.custom-alert-overlay');
    const alert = document.querySelector('.custom-alert');
    if (overlay) overlay.remove();
    if (alert) alert.remove();
}

function proceedWithDelete(userId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="user_id" value="${userId}">
        <input type="hidden" name="delete_user" value="1">
    `;
    document.body.appendChild(form);
    form.submit();
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
    if (event.target.className === 'custom-alert-overlay') {
        closeDeleteAlert();
    }
}