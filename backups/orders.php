<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: userAuth.php");
    exit();
}

// Fetch user's orders
$query = "SELECT o.*, COUNT(oi.id) as item_count 
          FROM orders o 
          LEFT JOIN order_items oi ON o.id = oi.order_id 
          WHERE o.user_id = ? 
          GROUP BY o.id 
          ORDER BY o.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Bookverse</title>
    <link rel="stylesheet" href="cart.css">
    <style>
        .orders-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .order-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            padding: 1rem;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .order-items {
            margin: 1rem 0;
        }
        .order-total {
            font-weight: bold;
            text-align: right;
        }
        .status-pending {
            color: #f59e0b;
        }
        .status-completed {
            color: #10b981;
        }
        .status-cancelled {
            color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="orders-container">
        <h1>My Orders</h1>
        
        <?php if ($orders->num_rows > 0): ?>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <p>Placed on: <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div>
                            <span class="status-<?php echo strtolower($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="order-items">
                        <p><?php echo $order['item_count']; ?> item(s)</p>
                        <?php
                        // Fetch order items
                        $items_query = "SELECT oi.*, b.title 
                                      FROM order_items oi 
                                      JOIN bookstore b ON oi.book_id = b.id 
                                      WHERE oi.order_id = ?";
                        $items_stmt = $conn->prepare($items_query);
                        $items_stmt->bind_param("i", $order['id']);
                        $items_stmt->execute();
                        $items = $items_stmt->get_result();
                        
                        while ($item = $items->fetch_assoc()):
                        ?>
                            <div class="order-item">
                                <p><?php echo htmlspecialchars($item['title']); ?> x <?php echo $item['quantity']; ?></p>
                                <p>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="order-total">
                        Total: $<?php echo number_format($order['total_amount'], 2); ?>
                    </div>
                    
                    <?php if ($order['transaction_id']): ?>
                        <div class="order-transaction">
                            Transaction ID: <?php echo htmlspecialchars($order['transaction_id']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-orders">
                <p>You haven't placed any orders yet.</p>
                <a href="shop.php" class="continue-shopping">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>