<?php
require_once 'check_user_session.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: userAuth.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation | Bookverse</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="css/shop.css">
    <style>
        .confirmation-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 40px;
            text-align: center;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .confirmation-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .success-icon { color: #28a745; }
        .error-icon { color: #dc3545; }

        .confirmation-title {
            font-size: 24px;
            color: #1a1a1a;
            margin-bottom: 16px;
        }

        .confirmation-message {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }

        .btn-primary {
            display: inline-block;
            padding: 12px 24px;
            background: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: #333;
        }
    </style>
</head>
<body>
    <nav>
        <div class="navbar">
            <div class="nav-left">
                <h1><a href="index.php" style="text-decoration: none; color: #1a1a1a;"><i class="fa-solid fa-book-open-cover"></i> Bookverse</a></h1>
            </div>
        </div>
    </nav>

    <div class="confirmation-container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <i class="fa-solid fa-circle-check confirmation-icon success-icon"></i>
            <h1 class="confirmation-title">Order Confirmed!</h1>
            <p class="confirmation-message"><?php echo htmlspecialchars($_SESSION['success_message']); ?></p>
            <?php unset($_SESSION['success_message']); ?>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            <i class="fa-solid fa-circle-xmark confirmation-icon error-icon"></i>
            <h1 class="confirmation-title">Oops! Something went wrong</h1>
            <p class="confirmation-message"><?php echo htmlspecialchars($_SESSION['error_message']); ?></p>
            <?php unset($_SESSION['error_message']); ?>
        <?php else: ?>
            <i class="fa-solid fa-circle-question confirmation-icon"></i>
            <h1 class="confirmation-title">No Order Information</h1>
            <p class="confirmation-message">No order information was found. Please try placing your order again.</p>
        <?php endif; ?>
        
        <a href="index.php" class="btn-primary">Continue Shopping</a>
    </div>
</body>
</html>
