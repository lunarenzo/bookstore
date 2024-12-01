<?php
session_start();
require_once 'db.php';
require_once 'includes/OrderManager.php';

if (isset($_SESSION['current_order_id'])) {
    $orderManager = new OrderManager($conn);
    $orderManager->updateOrderStatus($_SESSION['current_order_id'], 'completed');
    unset($_SESSION['current_order_id']);
}

// Clear the cart after successful payment
unset($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success | Bookverse</title>
    <link rel="stylesheet" href="cart.css">
</head>
<body>
    <div class="cart-container">
        <div class="success-message">
            <h2>Payment Successful!</h2>
            <p>Thank you for your purchase. Your order has been processed successfully.</p>
            <a href="shop.php" class="continue-shopping">Continue Shopping</a>
        </div>
    </div>
</body>
</html>