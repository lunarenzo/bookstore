<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['action'])) {
        throw new Exception('Action not specified');
    }

    $action = $_POST['action'];
    
    if ($action === 'clear') {
        // Clear the entire cart
        $_SESSION['cart'] = array();
        echo json_encode([
            'success' => true,
            'cartCount' => 0,
            'total' => 0,
            'formattedTotal' => '0.00'
        ]);
        exit;
    }

    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;

    if ($action === 'update') {
        $new_quantity = (int)$_POST['quantity'];
        
        // Verify stock availability
        $stock_query = "SELECT stock_available, price FROM bookstore WHERE id = ?";
        $stmt = $conn->prepare($stock_query);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
        
        if (!$book) {
            throw new Exception('Book not found');
        }
        
        if ($new_quantity <= 0) {
            // Remove item if quantity is 0 or negative
            unset($_SESSION['cart'][$book_id]);
        } elseif ($new_quantity <= $book['stock_available']) {
            // Update quantity if within stock limits
            $_SESSION['cart'][$book_id] = $new_quantity;
        } else {
            throw new Exception('Requested quantity exceeds available stock');
        }
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$book_id]);
    } else {
        throw new Exception('Invalid action');
    }

    // Calculate new totals
    $total = 0;
    $cart_count = 0;
    $item_subtotal = 0;

    if (!empty($_SESSION['cart'])) {
        $ids = array_keys($_SESSION['cart']);
        $ids_str = implode(',', array_fill(0, count($ids), '?'));
        
        $query = "SELECT id, price FROM bookstore WHERE id IN ($ids_str)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($book = $result->fetch_assoc()) {
            $quantity = $_SESSION['cart'][$book['id']];
            $subtotal = $book['price'] * $quantity;
            if ($book['id'] == $book_id) {
                $item_subtotal = $subtotal;
            }
            $total += $subtotal;
            $cart_count += $quantity;
        }
    }

    echo json_encode([
        'success' => true,
        'total' => $total,
        'formattedTotal' => number_format($total, 2),
        'cartCount' => $cart_count,
        'itemSubtotal' => $item_subtotal,
        'formattedItemSubtotal' => number_format($item_subtotal, 2)
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
