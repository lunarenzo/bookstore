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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <aside class="sidebar">
        <div class="logo-container">
            <div class="logo-wrapper">
                <i class="fas fa-book logo-icon"></i>
                <span class="logo-text">Bookverse</span>
            </div>
        </div>

        <nav>
            <div class="nav-section">
                <h2 class="nav-title">Store Management</h2>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="fas fa-chart-line"></i>
                            <span class="nav-text">Overview</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="inventory.php" class="nav-link">
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
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span class="nav-text">Customers</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="nav-section">
                <h2 class="nav-title">Catalog</h2>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-book"></i>
                            <span class="nav-text">Books</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-tags"></i>
                            <span class="nav-text">Categories</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="nav-section">
                <h2 class="nav-title">Settings</h2>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-user-circle"></i>
                            <span class="nav-text">Account</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="nav-text">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </aside>
    <main class="content">
        <!-- Content area -->
    </main>

<script src="main.js"></script>
</body>
</html>