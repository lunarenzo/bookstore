<?php
session_start();
require_once 'db.php';
require_once 'includes/PaypalCheckout.php';
require_once 'includes/OrderManager.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: userAuth.php");
    exit();
}

// Initialize PayPal checkout handler and Order Manager
$paypal = new PaypalCheckout($conn);
$orderManager = new OrderManager($conn);

// Get cart items and calculate total
$cart_items = [];
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

    // Create order in database
    try {
        $orderId = $orderManager->createOrder($_SESSION['id'], $cart_items, $total);
        $_SESSION['current_order_id'] = $orderId;
    } catch (Exception $e) {
        die("Error creating order: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Bookverse</title>
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
            <h2>Checkout</h2>
            <a href="cart.php" class="continue-shopping">
                <i class="fa-solid fa-arrow-left"></i> Back to Cart
            </a>
        </div>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <i class="fa-solid fa-shopping-cart empty-cart-icon"></i>
                <p>Your cart is empty</p>
                <p class="empty-cart-subtext">Add some books to your cart first.</p>
                <a href="shop.php" class="btn-primary">Browse Books</a>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <div class="checkout-items">
                    <div class="checkout-section">
                        <h3>Order Summary</h3>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="checkout-item">
                                <div class="item-info">
                                    <img src="uploads/<?php echo htmlspecialchars($item['book_cover']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>">
                                    <div class="item-details">
                                        <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                        <p class="author">by <?php echo htmlspecialchars($item['author']); ?></p>
                                        <p class="quantity">Quantity: <?php echo $item['quantity']; ?></p>
                                        <p class="price">$<?php echo number_format($item['price'], 2); ?> each</p>
                                    </div>
                                </div>
                                <div class="item-subtotal">
                                    <p>$<?php echo number_format($item['subtotal'], 2); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="cart-summary">
                    <div class="summary-content">
                        <h3>Payment Summary</h3>
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
                        <div class="payment-section">
                            <?php echo $paypal->generatePaymentForm($cart_items, $total); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <style>
    .checkout-section {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 30px;
    }

    .checkout-section h3 {
        font-size: 18px;
        margin: 0 0 20px 0;
        color: #1a1a1a;
    }

    .checkout-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 0;
        border-bottom: 1px solid #eee;
    }

    .checkout-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .item-info {
        display: flex;
        gap: 20px;
        flex: 1;
    }

    .item-info img {
        width: 80px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
    }

    .item-details h4 {
        font-size: 16px;
        margin: 0 0 8px 0;
        color: #1a1a1a;
    }

    .item-details .author {
        font-size: 14px;
        color: #666;
        margin-bottom: 8px;
    }

    .item-details .quantity {
        font-size: 14px;
        color: #666;
        margin-bottom: 8px;
    }

    .item-details .price {
        font-size: 14px;
        color: #1a1a1a;
        margin: 0;
    }

    .item-subtotal {
        font-size: 16px;
        font-weight: 500;
        color: #1a1a1a;
        min-width: 100px;
        text-align: right;
    }

    .item-subtotal p {
        margin: 0;
    }

    .cart-summary h3 {
        font-size: 18px;
        margin: 0 0 20px 0;
        color: #1a1a1a;
    }

    .payment-section {
        margin-top: 30px;
    }

    .btn-primary, .btn-secondary {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
        border: 1px solid #000;
    }

    .btn-primary {
        background-color: #000;
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #333;
        border-color: #333;
    }

    .btn-secondary {
        background-color: #fff;
        color: #000;
    }

    .btn-secondary:hover {
        background-color: #f5f5f5;
    }

    .paypal-button {
        width: 100%;
        padding: 10px;
        background-color: #000;
        color: #fff;
        border: 1px solid #000;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 20px;
    }

    .paypal-button:hover {
        background-color: #333;
        border-color: #333;
    }

    .continue-shopping {
        color: #000;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        padding: 6px 12px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .continue-shopping:hover {
        background-color: #f5f5f5;
    }

    @media (max-width: 600px) {
        .checkout-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .item-info {
            width: 100%;
        }

        .item-subtotal {
            width: 100%;
            text-align: left;
        }
    }
    </style>
</body>
</html>