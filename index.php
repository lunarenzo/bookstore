<?php
// Start session but don't require login
session_start();

// Database connection
include 'db.php';

// Handle logout if requested
if(isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header("Location: userAuth.php");
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
    <link rel="stylesheet" href="css/shop.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<nav>
    <div class="navbar">
        <div class="nav-left">
            <h1><i class="fa-solid fa-book-open-cover"></i> Bookverse</h1>
        </div>
        <div class="nav-center">
            <form action="index.php" method="GET" class="search-form">
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
                    <span>Cart <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>(<?php echo array_sum($_SESSION['cart']); ?>)<?php endif; ?></span>
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
                                    echo "<button class='remove-item' onclick='removeFromCart(" . $book['id'] . ", event)'>";
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
                            <button class="btn btn-clear-cart" onclick="clearCart(event)">Clear Cart</button>
                            <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                                <a href="cart.php" class="btn btn-view-cart">View Cart</a>
                            <?php else: ?>
                                <a href="userAuth.php" class="btn btn-view-cart">Sign in to Checkout</a>
                            <?php endif; ?>
                        <?php endif; ?>
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
                        <a href="orders.php"><i class="fa-solid fa-box"></i> My Orders</a>
                        <a href="index.php?logout=true">Log Out</a>
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
        <a href="index.php" <?php echo !$genreFilter ? 'class="active"' : ''; ?>>All</a>
        <?php
        $genres = ['Fantasy', 'Contemporary', 'Sci-Fi', 'Dystopian', 'Adventure', 'Romance', 'Mystery', 'Horror', 'Thriller', 'Historical'];
        foreach ($genres as $genre) {
            $activeClass = ($genreFilter === $genre) ? 'class="active"' : '';
            echo "<a href=\"index.php?genre=$genre\" $activeClass>$genre</a>";
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

    <script src="js/shop.js"></script>
</body>
</html>
