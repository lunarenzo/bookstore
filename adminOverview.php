<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: adminLogin.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'bookstore');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to ensure sales_history exists and is populated
function ensureSalesHistoryExists($conn) {
    // Check if sales_history table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'sales_history'");
    if ($tableCheck->num_rows == 0) {
        // Read and execute the SQL file
        $sql = file_get_contents('add_sales_history.sql');
        $conn->multi_query($sql);
        
        // Clear out the results
        while ($conn->more_results() && $conn->next_result()) {
            if ($res = $conn->store_result()) {
                $res->free();
            }
        }
    }
}

// Ensure sales_history table exists
ensureSalesHistoryExists($conn);

// Get total all-time sales from sales_history
$totalSalesQuery = "SELECT COALESCE(SUM(total_amount), 0) as total_sales FROM sales_history";
$totalSalesResult = $conn->query($totalSalesQuery);
$totalSales = $totalSalesResult->fetch_assoc()['total_sales'];

// Get total number of all-time orders from sales_history
$totalOrdersQuery = "SELECT COUNT(*) as total_orders FROM sales_history";
$totalOrdersResult = $conn->query($totalOrdersQuery);
$totalOrders = $totalOrdersResult->fetch_assoc()['total_orders'];

$avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

// Get current active orders status distribution
$orderStatusQuery = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
$orderStatusResult = $conn->query($orderStatusQuery);
$orderStatus = [];
while ($row = $orderStatusResult->fetch_assoc()) {
    $orderStatus[$row['status']] = $row['count'];
}

// Get monthly sales trend from sales_history
$monthlySalesQuery = "SELECT 
    DATE_FORMAT(transaction_date, '%Y-%m') as month,
    SUM(total_amount) as monthly_total,
    COUNT(*) as order_count
    FROM sales_history
    GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6";
$monthlySalesResult = $conn->query($monthlySalesQuery);
$monthlySales = [];
while ($row = $monthlySalesResult->fetch_assoc()) {
    $monthlySales[] = $row;
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
                        <a href="adminOrders.php" class="nav-link">
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
        <div class="page-header">
            <h1>Dashboard Overview</h1>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-details">
                    <h3>Total Sales</h3>
                    <p>$<?php echo number_format($totalSales, 2); ?></p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-details">
                    <h3>Total Orders</h3>
                    <p><?php echo $totalOrders; ?></p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-details">
                    <h3>Average Order Value</h3>
                    <p>$<?php echo number_format($avgOrderValue, 2); ?></p>
                </div>
            </div>
        </div>

        <div class="order-status-section">
            <h2>Order Status Distribution</h2>
            <div class="status-grid">
                <div class="status-card">
                    <h4>Pending</h4>
                    <p><?php echo isset($orderStatus['Pending']) ? $orderStatus['Pending'] : 0; ?></p>
                </div>
                <div class="status-card">
                    <h4>Processing</h4>
                    <p><?php echo isset($orderStatus['Processing']) ? $orderStatus['Processing'] : 0; ?></p>
                </div>
                <div class="status-card">
                    <h4>Shipped</h4>
                    <p><?php echo isset($orderStatus['Shipped']) ? $orderStatus['Shipped'] : 0; ?></p>
                </div>
                <div class="status-card">
                    <h4>Delivered</h4>
                    <p><?php echo isset($orderStatus['Delivered']) ? $orderStatus['Delivered'] : 0; ?></p>
                </div>
            </div>
        </div>

        <div class="order-status-section">
            <h2>Monthly Sales Trend</h2>
            <div class="monthly-sales-grid">
                <?php foreach ($monthlySales as $month): ?>
                <div class="monthly-card">
                    <h4><?php echo date('F Y', strtotime($month['month'] . '-01')); ?></h4>
                    <p class="amount">$<?php echo number_format($month['monthly_total'], 2); ?></p>
                    <p class="orders"><?php echo $month['order_count']; ?> orders</p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

<script src="main.js"></script>
</body>
</html>