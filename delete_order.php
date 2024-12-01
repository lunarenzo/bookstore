<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    die('Unauthorized access');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete from order_items first (due to foreign key constraint)
        $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        // Then delete from orders
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ? AND status = 'Delivered'");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        // If we got here, commit the transaction
        $conn->commit();
        echo "success";
    } catch (Exception $e) {
        // If there was an error, rollback the transaction
        $conn->rollback();
        echo "error";
    }
    
    $stmt->close();
}
?>
