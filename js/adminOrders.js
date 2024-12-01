function updateOrderStatus(orderId, status, button) {
    fetch('update_order_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `order_id=${orderId}&status=${status}`
    })
    .then(response => response.text())
    .then(result => {
        if (result === 'success') {
            const orderCard = button.closest('.order-card');
            const statusBadge = orderCard.querySelector('.status-badge');
            const deleteButton = orderCard.querySelector('.delete-order');
            
            statusBadge.textContent = status;
            statusBadge.className = `status-badge ${status.toLowerCase()}`;
            
            // Show/hide delete button based on status
            if (status === 'Delivered') {
                deleteButton.style.display = 'block';
            } else {
                deleteButton.style.display = 'none';
            }
            
            // Hide confirm button after successful update
            button.classList.remove('visible');
        } else {
            alert('Failed to update order status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update order status');
    });
}

function deleteOrder(button) {
    const orderId = button.getAttribute('data-order-id');
    
    if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
        fetch('delete_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `order_id=${orderId}`
        })
        .then(response => response.text())
        .then(result => {
            if (result === 'success') {
                // Remove the order card from the UI
                const orderCard = button.closest('.order-card');
                orderCard.style.opacity = '0';
                setTimeout(() => {
                    orderCard.remove();
                    // If no orders left, show the no orders message
                    const ordersGrid = document.querySelector('.orders-grid');
                    if (ordersGrid.children.length === 0) {
                        ordersGrid.innerHTML = '<div class="no-orders"><p>No orders found</p></div>';
                    }
                }, 300);
            } else {
                alert('Failed to delete order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete order');
        });
    }
}

// Show/hide confirm button when status is selected
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const confirmButton = this.nextElementSibling;
        if (this.value) {
            confirmButton.classList.add('visible');
        } else {
            confirmButton.classList.remove('visible');
        }
    });
});

function confirmStatusUpdate(button) {
    const select = button.previousElementSibling;
    const orderId = select.getAttribute('data-order-id');
    const status = select.value;

    if (!status) return;

    if (confirm(`Are you sure you want to update this order's status to "${status}"?`)) {
        updateOrderStatus(orderId, status, button);
    }
}
