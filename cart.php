<?php
// Include session check
require_once 'check_user_session.php';

include 'db.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    
    if (isset($_POST['update_quantity'])) {
        $new_quantity = (int)$_POST['quantity'];
        if ($new_quantity > 0) {
            $stmt = $conn->prepare("SELECT stock_available FROM bookstore WHERE id = ?");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $book = $result->fetch_assoc();
            
            if ($new_quantity <= $book['stock_available']) {
                $_SESSION['cart'][$book_id] = $new_quantity;
            }
        }
    } elseif (isset($_POST['remove_item'])) {
        unset($_SESSION['cart'][$book_id]);
    }
    
    header("Location: cart.php");
    exit();
}

// Get cart items efficiently with a single query
$cart_items = array();
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $query = "SELECT * FROM bookstore WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($book = $result->fetch_assoc()) {
        $quantity = $_SESSION['cart'][$book['id']];
        $book['quantity'] = $quantity;
        $book['subtotal'] = $book['price'] * $quantity;
        $cart_items[] = $book;
        $total += $book['subtotal'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | Bookverse</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="css\shop.css">
    <link rel="stylesheet" href="css\cart.css">
</head>
<body>
    <nav>
        <div class="navbar">
            <div class="nav-left">
                <h1><a href="index.php" class="nav-logo"><i class="fa-solid fa-book-open-cover"></i> Bookverse</a></h1>
            </div>
        </div>
    </nav>

    <div class="cart-container">
        <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <div class="cart-items">
                <?php
                $total = 0;
                foreach($_SESSION['cart'] as $book_id => $quantity) {
                    $stmt = $conn->prepare("SELECT * FROM bookstore WHERE id = ?");
                    $stmt->bind_param("i", $book_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if($book = $result->fetch_assoc()):
                        $subtotal = $book['price'] * $quantity;
                        $total += $subtotal;
                ?>
                    <div class="cart-item" data-id="<?php echo $book['id']; ?>">
                        <div class="item-image">
                            <img src="uploads/<?php echo htmlspecialchars($book['book_cover']); ?>" 
                                 alt="<?php echo htmlspecialchars($book['title']); ?>">
                        </div>
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="author">by <?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="price">$<?php echo number_format($book['price'], 2); ?></p>
                        </div>
                        <div class="item-quantity">
                            <button class="quantity-btn minus" onclick="updateQuantity(<?php echo $book['id']; ?>, -1)">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <input type="number" name="quantity" value="<?php echo $quantity; ?>" 
                                   min="1" max="<?php echo $book['stock_available']; ?>" 
                                   onchange="updateQuantity(<?php echo $book['id']; ?>, this.value)">
                            <button class="quantity-btn plus" onclick="updateQuantity(<?php echo $book['id']; ?>, 1)">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                        <div class="item-subtotal">
                            <p class="subtotal">$<?php echo number_format($subtotal, 2); ?></p>
                            <button class="remove-btn" onclick="removeFromCart(<?php echo $book['id']; ?>)">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                <?php 
                    endif;
                }
                ?>
            </div>
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="cart-actions">
                    <a href="index.php" class="btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i>
                        Continue Shopping
                    </a>
                    <a href="checkout.php" class="btn-primary">
                        Proceed to Checkout
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <i class="fa-light fa-cart-shopping"></i>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added anything to your cart yet.</p>
                <a href="index.php" class="continue-shopping">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const cartItems = document.querySelectorAll('.cart-item');
        
        cartItems.forEach(item => {
            const removeBtn = item.querySelector('.remove-btn');
            const quantityBtns = item.querySelectorAll('.quantity-btn');
            const quantityInput = item.querySelector('input[name="quantity"]');
            
            if (removeBtn) {
                removeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (confirm('Are you sure you want to remove this item?')) {
                        removeFromCart(item.dataset.id);
                    }
                });
            }
            
            if (quantityInput) {
                quantityInput.addEventListener('change', () => {
                    updateQuantity(item.dataset.id, parseInt(quantityInput.value));
                });
            }
            
            quantityBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const change = btn.classList.contains('minus') ? -1 : 1;
                    updateQuantity(item.dataset.id, parseInt(quantityInput.value) + change);
                });
            });
        });
    });

    function updateQuantity(bookId, newQuantity) {
        const cartItem = document.querySelector(`.cart-item[data-id="${bookId}"]`);
        const input = cartItem.querySelector('input[name="quantity"]');
        const maxStock = parseInt(input.getAttribute('max'));
        
        newQuantity = Math.max(1, Math.min(newQuantity, maxStock));
        cartItem.classList.add('updating');
        
        fetch('update_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=update&book_id=${bookId}&quantity=${newQuantity}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = newQuantity;
                cartItem.querySelector('.subtotal').textContent = `$${data.formattedItemSubtotal}`;
                updateCartSummary(data.formattedTotal);
            } else {
                throw new Error(data.error || 'Failed to update cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'An error occurred while updating the cart');
        })
        .finally(() => {
            cartItem.classList.remove('updating');
        });
    }

    function removeFromCart(bookId) {
        const cartItem = document.querySelector(`.cart-item[data-id="${bookId}"]`);
        cartItem.classList.add('removing');
        
        fetch('update_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=remove&book_id=${bookId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cartItem.addEventListener('transitionend', () => {
                    cartItem.remove();
                    if (document.querySelectorAll('.cart-item').length === 0) {
                        location.reload();
                    }
                });
                cartItem.style.height = '0';
                cartItem.style.opacity = '0';
                updateCartSummary(data.formattedTotal);
            } else {
                throw new Error(data.error || 'Failed to remove item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'An error occurred while removing the item');
            cartItem.classList.remove('removing');
        });
    }

    function updateCartSummary(formattedTotal) {
        document.querySelectorAll('.summary-row span:last-child, .summary-total span:last-child')
            .forEach(el => el.textContent = `$${formattedTotal}`);
    }
    </script>
</body>
</html>