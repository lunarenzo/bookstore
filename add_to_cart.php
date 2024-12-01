<?php
// Include session check
require_once 'check_user_session.php';

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    
    // Check if user is logged in
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        echo json_encode(['success' => false, 'message' => 'login_required']);
        exit();
    }
    
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
        
        // Calculate total cart quantity
        $cartCount = array_sum($_SESSION['cart']);
        
        echo json_encode([
            'success' => true,
            'cartCount' => $cartCount,
            'message' => 'Book added to cart successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Book not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>