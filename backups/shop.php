<?php
// Include session check
require_once 'check_user_session.php';

// Database connection
include 'db.php';

// Handle logout if requested
if(isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header("Location: userauth.php");
    exit();
}

$genreFilter = isset($_GET['genre']) ? $_GET['genre'] : '';

// Get search query if exists
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT * FROM bookstore";
$params = array();
$types = "";

if ($searchQuery) {
    $query .= " WHERE (title LIKE ? OR author LIKE ? OR genre LIKE ?)";
    $searchTerm = "%{$searchQuery}%";
    $params = array($searchTerm, $searchTerm, $searchTerm);
    $types = "sss";
}

if ($genreFilter) {
    $query .= ($searchQuery ? " AND" : " WHERE") . " genre = ?";
    $params[] = $genreFilter;
    $types .= "s";
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookverse</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="shop.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<!-- Replace the existing nav section in shop.php -->
<nav>
    <div class="navbar">
        <div class="nav-left">
            <h1><i class="fa-solid fa-book-open-cover"></i> Bookverse</h1>
        </div>
        <div class="nav-center">
            <form action="shop.php" method="GET" class="search-form">
                <?php if ($genreFilter): ?>
                    <input type="hidden" name="genre" value="<?php echo htmlspecialchars($genreFilter); ?>">
                <?php endif; ?>
                <input type="text" name="search" class="search-bar" 
                       placeholder="Search for books..." 
                       value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit" class="search-button">
                    <i class="fa-solid fa-search"></i>
                </button>
            </form>
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
                            $cart_items = array();
                            foreach($_SESSION['cart'] as $book_id => $quantity) {
                                $cart_query = "SELECT id, title, price, book_cover FROM bookstore WHERE id = ?";
                                $stmt = $conn->prepare($cart_query);
                                $stmt->bind_param("i", $book_id);
                                $stmt->execute();
                                $cart_result = $stmt->get_result();
                                if($book = $cart_result->fetch_assoc()) {
                                    echo "<div class='cart-item' data-id='" . $book['id'] . "'>";
                                    echo "<img src='uploads/" . htmlspecialchars($book['book_cover']) . "' alt='" . htmlspecialchars($book['title']) . "'>";
                                    echo "<div class='cart-item-details'>";
                                    echo "<h4>" . htmlspecialchars($book['title']) . "</h4>";
                                    echo "<p>Quantity: " . $quantity . "</p>";
                                    echo "<p class='cart-price'>$" . number_format($book['price'] * $quantity, 2) . "</p>";
                                    echo "</div>";
                                    echo "<button class='remove-item' onclick='removeFromCart(" . $book['id'] . ")'>";
                                    echo "<i class='fa-solid fa-times'></i>";
                                    echo "</button>";
                                    echo "</div>";
                                }
                            }
                        } else {
                            echo "<p class='empty-cart-message'>Your cart is empty</p>";
                        }
                        ?>
                    </div>
                    <div class="cart-actions">
                        <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                            <button class="btn btn-clear-cart" onclick="clearCart()">Clear Cart</button>
                        <?php endif; ?>
                        <a href="cart.php" class="btn btn-view-cart">View Cart</a>
                    </div>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropdown-toggle">
                    <i class="fa-solid fa-user"></i>
                    <span>
                        <?php echo isset($_SESSION["full_name"]) ? htmlspecialchars($_SESSION["full_name"]) : (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true ? 'Account' : 'Sign In'); ?>
                    </span>
                </button>
                <div class="dropdown-menu user-dropdown">
                    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <a href="settings.php">Profile</a>
                        <a href="shop.php?logout=true">Log Out</a>
                    <?php else: ?>
                        <a href="userAuth.php">Sign In</a>
                        <a href="userAuth.php?action=register">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</nav>

    <!-- Genre Filters -->
    <div class="genre-filters">
        <a href="shop.php" <?php echo !$genreFilter ? 'class="active"' : ''; ?>>All</a>
        <?php
        $genres = ['Fantasy', 'Contemporary', 'Sci-Fi', 'Dystopian', 'Adventure', 'Romance', 'Mystery', 'Horror', 'Thriller', 'Historical'];
        foreach ($genres as $genre) {
            $activeClass = ($genreFilter === $genre) ? 'class="active"' : '';
            echo "<a href=\"shop.php?genre=$genre\" $activeClass>$genre</a>";
        }
        ?>
    </div>

    <!-- Book Display -->
    <div class="book-container">
    <?php while ($book = $result->fetch_assoc()): ?>
        <div class="book-item">
            <img src="uploads/<?php echo htmlspecialchars($book['book_cover']); ?>" 
                 alt="<?php echo htmlspecialchars($book['title']); ?> cover" 
                 class="book-cover">
            
            <span class="stock-badge <?php echo $book['stock_available'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                <?php echo $book['stock_available'] > 0 ? $book['stock_available'] . ' in stock' : 'Out of stock'; ?>
            </span>

            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
            <p class="author"><?php echo htmlspecialchars($book['author']); ?></p>
            <p class="price">$<?php echo number_format($book['price'], 2); ?></p>

            <?php if ($book['stock_available'] > 0): ?>
                <button class="add-to-cart" data-book-id="<?php echo $book['id']; ?>">
                    Add to Cart
                </button>
            <?php else: ?>
                <button class="add-to-cart" disabled>Out of Stock</button>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const addToCartButtons = document.querySelectorAll('.add-to-cart:not([disabled])');
        
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const bookId = this.dataset.bookId;
                
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `book_id=${bookId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update cart dropdown content
                        updateCartDropdown();
                        // Refresh the page to update stock display
                        location.reload();
                    } else {
                        alert(data.message || 'Error adding item to cart');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding item to cart');
                });
            });
        });

        // Function to update cart dropdown content
        function updateCartDropdown() {
            fetch('get_cart_items.php')
            .then(response => response.text())
            .then(html => {
                const cartItems = document.querySelector('.cart-items');
                if (cartItems) {
                    cartItems.innerHTML = html;
                }
            })
            .catch(error => console.error('Error updating cart:', error));
        }

        // Optional: Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(event.target)) {
                    dropdown.querySelector('.dropdown-menu').style.display = 'none';
                }
            });
        });

        // Toggle dropdown menus
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                const dropdownMenu = this.nextElementSibling;
                dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
            });
        });
    });

    function removeFromCart(bookId) {
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove&book_id=${bookId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Simply remove the item from the dropdown
                const cartItem = document.querySelector(`.cart-item[data-id="${bookId}"]`);
                cartItem.remove();
                updateCartDisplay(data);
            } else {
                alert(data.error || 'An error occurred while removing the item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the item');
        });
    }

    function clearCart() {
        if (!confirm('Are you sure you want to clear your cart?')) return;

        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=clear'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to reflect the empty cart
                location.reload();
            } else {
                alert(data.error || 'An error occurred while clearing the cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while clearing the cart');
        });
    }

    function updateCartDisplay(data) {
        // Update cart count
        const cartCount = document.querySelector('.cart-count');
        if (data.cartCount > 0) {
            cartCount.textContent = data.cartCount;
            cartCount.style.display = 'inline';
        } else {
            cartCount.style.display = 'none';
            // Show empty cart message
            const cartItems = document.querySelector('.cart-items');
            cartItems.innerHTML = '<p class="empty-cart-message">Your cart is empty</p>';
            // Hide clear cart button
            document.querySelector('.btn-clear-cart').style.display = 'none';
        }
    }
    </script>
</body>
</html>