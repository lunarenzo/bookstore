<?php
include 'db.php';
session_start();

if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $book_id => $quantity) {
        $cart_query = "SELECT id, title, price, book_cover FROM bookstore WHERE id = ?";
        $stmt = $conn->prepare($cart_query);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $cart_result = $stmt->get_result();
        if($book = $cart_result->fetch_assoc()) {
            echo "<div class='cart-item'>";
            echo "<img src='uploads/" . htmlspecialchars($book['book_cover']) . "' alt='" . htmlspecialchars($book['title']) . "'>";
            echo "<div class='cart-item-details'>";
            echo "<h4>" . htmlspecialchars($book['title']) . "</h4>";
            echo "<p>Quantity: " . $quantity . "</p>";
            echo "<p class='cart-price'>$" . number_format($book['price'] * $quantity, 2) . "</p>";
            echo "</div>";
            echo "</div>";
        }
    }
} else {
    echo "<p class='empty-cart-message'>Your cart is empty</p>";
}
?>
