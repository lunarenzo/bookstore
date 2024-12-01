<?php
// Include session check
require_once 'check_user_session.php';

require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: userAuth.php");
    exit();
}

$success_message = $error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST["full_name"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $user_id = $_SESSION["id"];

    // Update or insert user details
    $sql = "INSERT INTO user_details (user_id, full_name, phone, address) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            full_name = VALUES(full_name), 
            phone = VALUES(phone), 
            address = VALUES(address)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isss", $user_id, $full_name, $phone, $address);
        
        if ($stmt->execute()) {
            $_SESSION["full_name"] = $full_name; // Store full name in session
            $success_message = "Profile updated successfully!";
        } else {
            $error_message = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
}

// Get existing user details
$user_id = $_SESSION["id"];
$user_details = [];
$sql = "SELECT * FROM user_details WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_details = $result->fetch_assoc();
    if ($user_details) {
        $_SESSION["full_name"] = $user_details['full_name']; // Store full name in session
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | Bookverse</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="css/shop.css">
    <link rel="stylesheet" href="css/settings.css">
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
                <div class="dropdown">
                    <button class="dropdown-toggle">
                        <i class="fa-light fa-cart-shopping"></i>
                        <span>Cart</span>
                        <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span class="cart-count"><?php echo array_sum($_SESSION['cart']); ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu cart-dropdown">
                        <div class="cart-items">
                            <?php
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
                        </div>
                        <div class="cart-actions">
                            <a href="cart.php" class="btn btn-view-cart">View Cart</a>
                        </div>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="dropdown-toggle">
                        <i class="fa-solid fa-user"></i>
                        <span><?php echo isset($_SESSION["full_name"]) ? htmlspecialchars($_SESSION["full_name"]) : 'Account'; ?></span>
                    </button>
                    <div class="dropdown-menu user-dropdown">
                        <a href="settings.php" class="active">Profile</a>
                        <a href="orders.php"><i class="fa-solid fa-box"></i> My Orders</a>
                        <a href="index.php?logout=true">Log Out</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="settings-container">
        <div class="settings-card">
            <h2>Profile Settings</h2>
            
            <?php if ($success_message): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" 
                           value="<?php echo htmlspecialchars($user_details['full_name'] ?? ''); ?>" 
                           placeholder="Enter your full name"
                           required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($user_details['phone'] ?? ''); ?>" 
                           placeholder="Enter your phone number"
                           required>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" 
                              placeholder="Enter your shipping address"
                              required><?php echo htmlspecialchars($user_details['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="save-btn">Save Changes</button>
                    <a href="index.php" class="back-btn">Back to Shop</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dropdown functionality
        const dropdowns = document.querySelectorAll('.dropdown');
        let activeDropdown = null;
        
        dropdowns.forEach(dropdown => {
            const button = dropdown.querySelector('.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');
            
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // If this dropdown is already active, close it
                if (activeDropdown === dropdown) {
                    menu.classList.remove('show');
                    activeDropdown = null;
                    return;
                }
                
                // Close any other open dropdown
                if (activeDropdown) {
                    activeDropdown.querySelector('.dropdown-menu').classList.remove('show');
                }
                
                // Open this dropdown
                menu.classList.add('show');
                activeDropdown = dropdown;
            });
            
            // Prevent dropdown from closing when clicking inside the menu
            menu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (activeDropdown && !activeDropdown.contains(e.target)) {
                activeDropdown.querySelector('.dropdown-menu').classList.remove('show');
                activeDropdown = null;
            }
        });
    });
    </script>
</body>
</html>