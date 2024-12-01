<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: adminLogin.php");
    exit();
}

// Include database connection
include 'db.php';

// Initialize messages
$success_message = $error_message = '';

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete from user_details first (due to foreign key)
        $delete_details = "DELETE FROM user_details WHERE user_id = ?";
        $stmt = $conn->prepare($delete_details);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Then delete from shopuser
        $delete_user = "DELETE FROM shopuser WHERE id = ?";
        $stmt = $conn->prepare($delete_user);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success_message'] = "User deleted successfully.";
        header("Location: adminAccounts.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error_message'] = "Error deleting user. Please try again.";
        header("Location: adminAccounts.php");
        exit();
    }
}

// Handle user update
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update email in shopuser table
        $update_user = "UPDATE shopuser SET email = ? WHERE id = ?";
        $stmt = $conn->prepare($update_user);
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();

        // Check if user_details exists
        $check_details = "SELECT id FROM user_details WHERE user_id = ?";
        $stmt = $conn->prepare($check_details);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing user_details
            $update_details = "UPDATE user_details SET full_name = ?, phone = ?, address = ? WHERE user_id = ?";
            $stmt = $conn->prepare($update_details);
            $stmt->bind_param("sssi", $full_name, $phone, $address, $user_id);
        } else {
            // Insert new user_details
            $insert_details = "INSERT INTO user_details (user_id, full_name, phone, address) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_details);
            $stmt->bind_param("isss", $user_id, $full_name, $phone, $address);
        }
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        $_SESSION['success_message'] = "User updated successfully!";
        header("Location: adminAccounts.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error_message'] = "Error updating user!";
        header("Location: adminAccounts.php");
        exit();
    }
}

// Get total number of registered users
$count_query = "SELECT COUNT(*) as total FROM shopuser";
$count_result = mysqli_query($conn, $count_query);
$total_users = mysqli_fetch_assoc($count_result)['total'];

// Check for messages
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
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
    <link rel="stylesheet" href="css\adminAccounts.css">
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
    <main class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h1>Customer Accounts</h1>
                <p class="subtitle">Manage registered customers</p>
            </div>
        </div>

        <?php if ($success_message): ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="stats-container">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-value"><?php echo number_format($total_users); ?></div>
                <div class="stats-label">Registered Customers</div>
            </div>
        </div>

        <?php
        // Fetch all users with their details
        $query = "SELECT s.id, s.email, ud.full_name, ud.phone, ud.address 
                 FROM shopuser s 
                 LEFT JOIN user_details ud ON s.id = ud.user_id 
                 ORDER BY s.id DESC";
        $result = mysqli_query($conn, $query);
        ?>

        <div class="content-wrapper">
            <div class="table-container customer-table">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Full Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name'] ?? 'Not set'); ?></td>
                                <td><?php echo htmlspecialchars($user['phone'] ?? 'Not set'); ?></td>
                                <td><?php echo htmlspecialchars($user['address'] ?? 'Not set'); ?></td>
                                <td class="btn-container">
                                    <button class="btn btn-primary" onclick="openEditModal(<?php 
                                        echo htmlspecialchars(json_encode($user)); 
                                    ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger" onclick="confirmDelete(<?php echo $user['id']; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div id="editUserModal" class="modal">
            <div class="modal-container">
                <div class="modal-header">
                    <h2 class="modal-title">Edit Customer</h2>
                    <button class="modal-close" onclick="closeModal('editUserModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="editUserForm" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="edit_email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="edit_full_name" name="full_name">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="edit_phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="edit_address" name="address" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Cancel</button>
                        <button type="submit" name="update_user" class="btn btn-primary">Update Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

<script src="js\adminAccounts.js"></script>
</body>
</html>