document.addEventListener('DOMContentLoaded', function() {
    // Dropdown functionality
    const dropdowns = document.querySelectorAll('.dropdown');
    let activeDropdown = null;
    
    dropdowns.forEach(dropdown => {
        const button = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            if (activeDropdown === dropdown) {
                menu.classList.remove('show');
                activeDropdown = null;
                return;
            }
            if (activeDropdown) {
                activeDropdown.querySelector('.dropdown-menu').classList.remove('show');
            }
            menu.classList.add('show');
            activeDropdown = dropdown;
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', () => {
        if (activeDropdown) {
            activeDropdown.querySelector('.dropdown-menu').classList.remove('show');
            activeDropdown = null;
        }
    });

    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'book_id=' + this.getAttribute('data-book-id')
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) window.location.reload();
                else alert('Failed to add item to cart');
            });
        });
    });
});

// Function to update cart dropdown
function updateCartDropdown() {
    fetch('get_cart_items.php')
        .then(response => response.json())
        .then(data => {
            const cartItems = document.querySelector('.cart-items');
            const cartText = document.querySelector('.dropdown-toggle span');
            if (!cartItems || !cartText) return;

            if (data.empty) {
                cartText.textContent = 'Cart';
                cartItems.innerHTML = '<p class="empty-cart-message">Your cart is empty</p>';
                return;
            }

            const totalQuantity = data.items.reduce((sum, item) => sum + parseInt(item.quantity), 0);
            cartText.textContent = `Cart (${totalQuantity})`;

            cartItems.innerHTML = data.items.map(item => `
                <div class="cart-item" data-id="${item.id}">
                    <img src="uploads/${item.book_cover}" alt="${item.title}">
                    <div class="cart-item-details">
                        <h4>${item.title}</h4>
                        <p>Quantity: ${item.quantity}</p>
                        <p class="cart-price">$${item.subtotal.toFixed(2)}</p>
                    </div>
                    <button class="remove-item" onclick="removeFromCart(${item.id}, event)">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            `).join('');
        })
        .catch(error => console.error('Error updating cart:', error));
}

// Function to remove item from cart
function removeFromCart(bookId, event) {
    event.preventDefault();
    event.stopPropagation();
    
    if (!confirm('Remove this item from cart?')) return;

    fetch('update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove&book_id=${bookId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) updateCartDropdown();
    })
    .catch(error => console.error('Error removing item:', error));
}

// Function to clear cart
function clearCart(event) {
    event.preventDefault();
    event.stopPropagation();
    
    if (!confirm('Clear entire cart?')) return;

    fetch('update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=clear'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) updateCartDropdown();
        else alert(data.error || 'Failed to clear cart');
    })
    .catch(error => console.error('Error:', error));
}
