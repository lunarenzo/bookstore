<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled | Bookverse</title>
    <link rel="stylesheet" href="cart.css">
</head>
<body>
    <div class="cart-container">
        <div class="cancel-message">
            <h2>Payment Cancelled</h2>
            <p>Your payment has been cancelled. Your cart items are still saved.</p>
            <a href="cart.php" class="continue-shopping">Return to Cart</a>
        </div>
    </div>
</body>
</html>