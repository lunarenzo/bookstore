<?php
include 'db.php';
session_start();

header('Content-Type: application/json');
$cart_items = array();
$total = 0;

if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $book_id => $quantity) {
        $cart_query = "SELECT id, title, price, book_cover FROM bookstore WHERE id = ?";
        $stmt = $conn->prepare($cart_query);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $cart_result = $stmt->get_result();
        if($book = $cart_result->fetch_assoc()) {
            $item = array(
                'id' => $book['id'],
                'title' => $book['title'],
                'price' => $book['price'],
                'book_cover' => $book['book_cover'],
                'quantity' => $quantity,
                'subtotal' => $book['price'] * $quantity
            );
            $cart_items[] = $item;
            $total += $item['subtotal'];
        }
    }
}

echo json_encode([
    'success' => true,
    'items' => $cart_items,
    'total' => $total,
    'empty' => empty($cart_items)
]);
?>
