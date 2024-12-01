document.addEventListener('DOMContentLoaded', function () {
    const addToCartButtons = document.querySelectorAll('.add-to-cart:not([disabled])');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function () {
            const bookId = this.dataset.bookId;

            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `book_id=${bookId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh the page to update cart count
                        location.reload();
                    } else {
                        alert(data.message || 'Error adding item to cart');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding item to cart');
                });
        });
    });
});