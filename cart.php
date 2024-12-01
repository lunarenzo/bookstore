<?php
session_start();
include 'db.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle quantity updates
if (isset($_POST['update_quantity'])) {
    $book_id = $_POST['book_id'];
    $new_quantity = (int)$_POST['quantity'];
    
    // Get book stock from database
    $stock_query = "SELECT stock_available FROM bookstore WHERE id = ?";
    $stmt = $conn->prepare($stock_query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    
    // Ensure quantity doesn't exceed stock
    if ($new_quantity > 0 && $new_quantity <= $book['stock_available']) {
        $_SESSION['cart'][$book_id] = $new_quantity;
    }
    
    header("Location: cart.php");
    exit();
}

// Handle item removal
if (isset($_POST['remove_item'])) {
    $book_id = $_POST['book_id'];
    unset($_SESSION['cart'][$book_id]);
    header("Location: cart.php");
    exit();
}

// Get cart items from database
$cart_items = array();
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $ids_str = implode(',', array_fill(0, count($ids), '?'));
    
    $query = "SELECT * FROM bookstore WHERE id IN ($ids_str)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($book = $result->fetch_assoc()) {
        $book['quantity'] = $_SESSION['cart'][$book['id']];
        $book['subtotal'] = $book['price'] * $book['quantity'];
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
    <link rel="stylesheet" href="shop.css">
    <link rel="stylesheet" href="cart.css">
</head>
<body>
    <nav>
        <div class="navbar">
            <div class="nav-left">
                <h1><i class="fa-solid fa-book-open-cover"></i> Bookverse</h1>
            </div>
            <div class="nav-center">
                <form action="shop.php" method="GET" class="search-form">
                    <input type="text" name="search" class="search-bar" 
                           placeholder="Search for books...">
                    <button type="submit" class="search-button">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </form>
            </div>
            <div class="nav-right">
                <div class="dropdown cart-dropdown">
                    <button class="dropbtn">
                        <i class="fa-solid fa-shopping-cart"></i>
                        <span class="cart-count"><?php echo array_sum($_SESSION['cart'] ?? []); ?></span>
                    </button>
                </div>
                <div class="dropdown user-dropdown">
                    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <button class="dropbtn">
                            <i class="fa-solid fa-user"></i>
                            <span><?php echo isset($_SESSION['fullname']) ? htmlspecialchars($_SESSION['fullname']) : 'Account'; ?></span>
                        </button>
                        <div class="dropdown-content">
                            <a href="settings.php">Profile</a>
                            <a href="shop.php?logout=true">Log Out</a>
                        </div>
                    <?php else: ?>
                        <a href="userAuth.php" class="dropbtn">
                            <i class="fa-solid fa-user"></i>
                            <span>Sign In</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="cart-container">
        <div class="cart-header">
            <h2>Your Shopping Cart</h2>
            <a href="shop.php" class="continue-shopping">
                <i class="fa-solid fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <i class="fa-solid fa-shopping-cart empty-cart-icon"></i>
                <p>Your cart is empty</p>
                <p class="empty-cart-subtext">Looks like you haven't added any books to your cart yet.</p>
                <a href="shop.php" class="btn-primary">Browse Books</a>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item" data-id="<?php echo $item['id']; ?>">
                            <div class="item-image">
                                <img src="uploads/<?php echo htmlspecialchars($item['book_cover']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="item-details">
                                <div class="item-info">
                                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <p class="author">by <?php echo htmlspecialchars($item['author']); ?></p>
                                    <p class="price">$<?php echo number_format($item['price'], 2); ?></p>
                                </div>
                                <div class="item-actions">
                                    <div class="quantity-controls">
                                        <button class="quantity-btn minus" onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="<?php echo $item['stock_available']; ?>"
                                               onchange="updateQuantity(<?php echo $item['id']; ?>, this.value)">
                                        <button class="quantity-btn plus" onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                    <button class="btn-remove" onclick="removeItem(<?php echo $item['id']; ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                                <p class="subtotal">Subtotal: $<?php echo number_format($item['subtotal'], 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <div class="summary-content">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <div class="summary-total">
                            <span>Total</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <a href="checkout.php" class="btn-checkout">
                            Proceed to Checkout <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
    function updateQuantity(bookId, change) {
        const input = document.querySelector(`.cart-item[data-id="${bookId}"] input[name="quantity"]`);
        let newQuantity;
        
        if (typeof change === 'number') {
            newQuantity = parseInt(input.value) + change;
        } else {
            newQuantity = parseInt(change);
        }

        if (newQuantity < 1 || newQuantity > parseInt(input.max)) return;

        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `book_id=${bookId}&quantity=${newQuantity}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = newQuantity;
                updateCartDisplay(data);
            }
        });
    }

    function removeItem(bookId) {
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `book_id=${bookId}&remove=1`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`.cart-item[data-id="${bookId}"]`);
                item.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => {
                    item.remove();
                    updateCartDisplay(data);
                    if (document.querySelectorAll('.cart-item').length === 0) {
                        location.reload();
                    }
                }, 300);
            }
        });
    }

    function updateCartDisplay(data) {
        document.querySelector('.cart-count').textContent = data.cartCount;
        const summaryTotal = document.querySelector('.summary-total span:last-child');
        if (summaryTotal) {
            summaryTotal.textContent = `$${data.total.toFixed(2)}`;
        }
    }
    </script>
</body>
</html>