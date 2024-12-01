<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: userAuth.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    
    // Check if book exists and has stock
    $query = "SELECT stock_available FROM bookstore WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($book = $result->fetch_assoc()) {
        // Initialize cart if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        
        // Add or update quantity in cart
        if (isset($_SESSION['cart'][$book_id])) {
            // Don't exceed available stock
            if ($_SESSION['cart'][$book_id] < $book['stock_available']) {
                $_SESSION['cart'][$book_id]++;
            }
        } else {
            $_SESSION['cart'][$book_id] = 1;
        }
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Book not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>