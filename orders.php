<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: userAuth.php");
    exit();
}

// Database connection
include 'db.php';

// Get user's orders
$user_id = $_SESSION["id"];
$query = "SELECT o.*, 
          GROUP_CONCAT(b.title SEPARATOR '|||') as titles,
          GROUP_CONCAT(b.author SEPARATOR '|||') as authors,
          GROUP_CONCAT(b.book_cover SEPARATOR '|||') as book_covers,
          GROUP_CONCAT(oi.quantity SEPARATOR '|||') as quantities,
          GROUP_CONCAT(oi.price_per_unit SEPARATOR '|||') as prices
          FROM orders o
          JOIN order_items oi ON o.id = oi.order_id
          JOIN bookstore b ON oi.book_id = b.id
          WHERE o.user_id = ?
          GROUP BY o.id
          ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Bookverse</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="css/shop.css">
    <link rel="stylesheet" href="css/orders.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="navbar">
            <div class="nav-left">
                <a href="index.php"><h1><i class="fa-solid fa-book-open-cover"></i> Bookverse</h1></a>
            </div>
            <div class="nav-right">
                <a href="cart.php" class="cart-link">
                    <i class="fa-light fa-cart-shopping"></i>
                    <span>Cart <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>(<?php echo array_sum($_SESSION['cart']); ?>)<?php endif; ?></span>
                </a>
                <div class="dropdown">
                    <button class="dropdown-toggle">
                        <i class="fa-solid fa-user"></i>
                        <span><?php echo htmlspecialchars($_SESSION["full_name"]); ?></span>
                    </button>
                    <div class="dropdown-menu user-dropdown">
                        <a href="settings.php">Profile</a>
                        <a href="orders.php" class="active"><i class="fa-solid fa-box"></i> My Orders</a>
                        <a href="index.php?logout=true">Log Out</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="orders-container">
        <h2>My Orders</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): 
                $titles = explode('|||', $row['titles']);
                $authors = explode('|||', $row['authors']);
                $book_covers = explode('|||', $row['book_covers']);
                $quantities = explode('|||', $row['quantities']);
                $prices = explode('|||', $row['prices']);
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <span class="order-id">Order #<?php echo $row['id']; ?></span>
                            <span class="order-date"><?php echo date('M j, Y', strtotime($row['created_at'])); ?></span>
                            <span class="order-status status-<?php echo strtolower($row['status']); ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </div>
                    </div>
                    <div class="order-items">
                        <?php foreach ($book_covers as $index => $cover): ?>
                            <div class="order-item">
                                <img src="uploads/<?php echo $cover; ?>" alt="Book cover">
                                <div class="book-details">
                                    <h4><?php echo $titles[$index]; ?></h4>
                                    <p class="author">by <?php echo $authors[$index]; ?></p>
                                    <div class="quantity-price">
                                        <span class="quantity">Qty: <?php echo $quantities[$index]; ?></span>
                                    </div>
                                </div>
                                <div class="item-total">
                                    $<?php echo number_format($quantities[$index] * $prices[$index], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="total-amount">
                        Total: $<?php echo number_format($row['total_amount'], 2); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You haven't placed any orders yet.</p>
        <?php endif; ?>
    </div>

    <script src="js/orders.js"></script>
</body>
</html>