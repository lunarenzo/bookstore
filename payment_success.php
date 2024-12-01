<?php
require_once 'check_user_session.php';
require_once 'db.php';
require_once 'includes/OrderManager.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: userAuth.php");
    exit();
}

// Verify we have pending order data
if (!isset($_SESSION['pending_order'])) {
    header("Location: cart.php");
    exit();
}

$orderManager = new OrderManager($conn);

// Create and complete order
try {
    // Create the order
    $orderId = $orderManager->createOrder(
        $_SESSION['id'], 
        $_SESSION['pending_order']['cart_items'], 
        $_SESSION['pending_order']['total']
    );
    
    // Update order status to completed
    $orderManager->updateOrderStatus($orderId, 'completed');
    
    // Clear cart and order session data
    unset($_SESSION['cart']);
    unset($_SESSION['pending_order']);
    unset($_SESSION['transaction_id']);
    
    // Store success message
    $_SESSION['success_message'] = "Thank you for your purchase! Your order has been completed successfully.";
} catch (Exception $e) {
    $_SESSION['error_message'] = "There was an error processing your order: " . $e->getMessage();
}

// Redirect to order confirmation page
header("Location: order_confirmation.php");
exit();
?>