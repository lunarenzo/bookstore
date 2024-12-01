<?php
// Include session check
require_once 'check_user_session.php';

require_once 'db.php';
require_once 'includes/PaypalCheckout.php';
require_once 'includes/OrderManager.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: userAuth.php");
    exit();
}

// Get user details
$user_id = $_SESSION["id"];
$user_details = [];
$sql = "SELECT * FROM user_details WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_details = $result->fetch_assoc();
    $stmt->close();
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
}

// Generate a unique transaction ID if not exists
if (!isset($_SESSION['transaction_id'])) {
    $_SESSION['transaction_id'] = 'TXN' . time() . rand(1000, 9999);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Bookverse</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="css\shop.css">
    <link rel="stylesheet" href="css\cart.css">
    <link rel="stylesheet" href="css\checkout.css">
</head>
<body>
    <nav>
        <div class="navbar">
            <div class="nav-left">
                <h1><a href="index.php" style="text-decoration: none; color: #1a1a1a;"><i class="fa-solid fa-book-open-cover"></i> Bookverse</a></h1>
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
                <a href="index.php" class="btn-primary">Browse Books</a>
            </div>
        <?php else: ?>
            <div class="checkout-container">
                <div class="user-info-section">
                    <h3>Contact Information</h3>
                    <div class="user-info-item">
                        <label>Transaction ID:</label>
                        <span class="transaction-id"><?php echo htmlspecialchars($_SESSION['transaction_id']); ?></span>
                    </div>
                    <div class="user-info-item">
                        <label>Full Name:</label>
                        <span><?php echo isset($user_details['full_name']) ? htmlspecialchars($user_details['full_name']) : 'Not set'; ?></span>
                    </div>
                    <div class="user-info-item">
                        <label>Email:</label>
                        <span><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Not set'; ?></span>
                    </div>
                    <div class="user-info-item">
                        <label>Phone Number:</label>
                        <span><?php echo isset($user_details['phone']) ? htmlspecialchars($user_details['phone']) : 'Not set'; ?></span>
                    </div>
                    <div class="user-info-item">
                        <label>Address:</label>
                        <span><?php echo isset($user_details['address']) ? htmlspecialchars($user_details['address']) : 'Not set'; ?></span>
                    </div>
                    <?php if (!$user_details): ?>
                        <div class="warning-message">
                            Please <a href="profile.php">update your profile</a> to complete your purchase.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="checkout-content">
                    <div class="order-summary">
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
                    
                    <div class="payment-summary">
                        <div class="checkout-section">
                            <h3>Payment Summary</h3>
                            <div class="summary-content">
                                <div class="summary-row">
                                    <span>Subtotal</span>
                                    <span>$<?php echo number_format($total, 2); ?></span>
                                </div>
                                <div class="summary-total">
                                    <span>Total</span>
                                    <span>$<?php echo number_format($total, 2); ?></span>
                                </div>
                                
                                <?php if ($user_details): ?>
                                    <form action="process_payment.php" method="post">
                                        <input type="hidden" name="transaction_id" value="<?php echo $_SESSION['transaction_id']; ?>">
                                        <button type="submit" class="checkout-button">Proceed to Payment</button>
                                    </form>
                                <?php else: ?>
                                    <div class="warning-message">Please update your profile before proceeding to payment.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>