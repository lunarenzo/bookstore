<?php
require_once 'check_user_session.php';
require_once 'db.php';
require_once 'includes/PaypalCheckout.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: userAuth.php");
    exit();
}

// Verify this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit();
}

// Get cart items and calculate total
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $ids_str = implode(',', array_fill(0, count($ids), '?'));
    
    $query = "SELECT * FROM bookstore WHERE id IN ($ids_str)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($book = $result->fetch_assoc()) {
        $book['quantity'] = $_SESSION['cart'][$book['id']];
        $book['subtotal'] = $book['price'] * $book['quantity'];
        $cart_items[] = $book;
        $total += $book['subtotal'];
    }
    
    // Store cart data in session for later use after payment
    $_SESSION['pending_order'] = [
        'cart_items' => $cart_items,
        'total' => $total,
        'transaction_id' => $_SESSION['transaction_id']
    ];
}

// Generate PayPal form and redirect
$paypal = new PaypalCheckout($conn);
echo $paypal->generatePaymentForm($cart_items, $total);
echo "<script>document.getElementById('paypal_form').submit();</script>";
?>
