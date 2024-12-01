<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: adminLogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookverse Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="css\style.css">
    <link rel="stylesheet" href="css\adminOrders.css">
</head>
<body>
    <aside class="sidebar">
        <div class="logo-container">
            <div class="logo-wrapper">
                <i class="fa-solid fa-book-open-cover"></i>
                <span class="logo-text">Bookverse</span>
            </div>
        </div>

        <nav>
            <div class="nav-section">
                <h2 class="nav-title">Store Management</h2>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="adminOverview.php" class="nav-link active">
                            <i class="fas fa-chart-line"></i>
                            <span class="nav-text">Overview</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="adminInventory.php" class="nav-link">
                            <i class="fas fa-cube"></i>
                            <span class="nav-text">Inventory</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="nav-text">Orders</span>
                        </a>
                    </li>
                </ul>
            </div>


            <div class="nav-section">
                <h2 class="nav-title">Settings</h2>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="adminAccounts.php" class="nav-link">
                            <i class="fas fa-user-circle"></i>
                            <span class="nav-text">Account</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="adminLogout.php" class="nav-link">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="nav-text">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </aside>
    <main class="content">
        <div class="main-content">
            <?php
            // Include database connection
            include 'db.php';

            // Fetch all orders with user and book details
            $query = "SELECT o.*, 
                      ud.full_name as customer_name,
                      ud.phone as customer_phone,
                      ud.address as customer_address,
                      GROUP_CONCAT(b.title SEPARATOR '|||') as titles,
                      GROUP_CONCAT(b.author SEPARATOR '|||') as authors,
                      GROUP_CONCAT(oi.quantity SEPARATOR '|||') as quantities,
                      GROUP_CONCAT(oi.price_per_unit SEPARATOR '|||') as prices
                      FROM orders o
                      JOIN user_details ud ON o.user_id = ud.user_id
                      JOIN order_items oi ON o.id = oi.order_id
                      JOIN bookstore b ON oi.book_id = b.id
                      GROUP BY o.id
                      ORDER BY o.created_at DESC";

            $result = $conn->query($query);
            ?>
            <div class="content-header">
                <h1>Order Management</h1>
            </div>
            
            <div class="orders-container">
                <?php if ($result && $result->num_rows > 0): ?>
                    <div class="orders-grid">
                    <?php while($row = $result->fetch_assoc()): 
                        $titles = explode('|||', $row['titles']);
                        $authors = explode('|||', $row['authors']);
                        $quantities = explode('|||', $row['quantities']);
                        $prices = explode('|||', $row['prices']);
                        $total_items = array_sum($quantities);
                    ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <div class="order-id">Order #<?php echo $row['id']; ?></div>
                                    <div class="order-date"><?php echo date('F j, Y \a\t g:i A', strtotime($row['created_at'])); ?></div>
                                </div>
                                <span class="status-badge <?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span>
                            </div>
                            <div class="order-content">
                                <div class="customer-info">
                                    <div class="customer-name"><?php echo htmlspecialchars($row['customer_name']); ?></div>
                                    <div class="customer-details">
                                        <?php if ($row['customer_phone']): ?>
                                        <div class="detail">
                                            <i class="fas fa-phone"></i>
                                            <?php echo htmlspecialchars($row['customer_phone']); ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($row['customer_address']): ?>
                                        <div class="detail">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?php echo htmlspecialchars($row['customer_address']); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="order-items">
                                    <?php foreach($titles as $index => $title): ?>
                                        <div class="item">
                                            <div class="item-details">
                                                <div class="item-title"><?php echo htmlspecialchars($title); ?></div>
                                                <div class="item-author">by <?php echo htmlspecialchars($authors[$index]); ?></div>
                                            </div>
                                            <div class="item-price">
                                                <?php echo $quantities[$index]; ?> × $<?php echo number_format($prices[$index], 2); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="order-total">
                                    <span>Total Amount</span>
                                    <span>$<?php echo number_format($row['total_amount'], 2); ?></span>
                                </div>
                            </div>
                            <div class="order-actions">
                                <select class="status-select" data-order-id="<?php echo $row['id']; ?>">
                                    <option value="">Update Status</option>
                                    <option value="Pending" <?php echo $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Processing" <?php echo $row['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="Shipped" <?php echo $row['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="Delivered" <?php echo $row['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                                <button class="confirm-status" onclick="confirmStatusUpdate(this)">Confirm</button>
                                <button class="delete-order" onclick="deleteOrder(this)" data-order-id="<?php echo $row['id']; ?>" <?php echo $row['status'] == 'Delivered' ? 'style="display: block;"' : ''; ?>>Delete Order</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="no-orders">
                        <p>No orders found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <script src="js/main.js"></script>
        <script src="js/adminOrders.js"></script>
    </main>
</body>
</html>