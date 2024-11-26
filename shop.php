<?php
// Database connection
include 'db.php'; // Ensure this file has the database credentials.

$genreFilter = isset($_GET['genre']) ? $_GET['genre'] : '';

$query = "SELECT * FROM bookstore";
if ($genreFilter) {
    $query .= " WHERE genre = '$genreFilter'";
}
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookverse</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="shop.css"> <!-- Add your CSS file -->
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="navbar">
            <div class="nav-left">
                <h1>Bookverse</h1>
            </div>
            <div class="nav-center">
                <input type="text" class="search-bar" placeholder="Search for books...">
            </div>
            <div class="nav-right">
                <a href="cart.php"><i class="fa-light fa-cart-shopping"></i> Cart</a>
                <a href="userauth.html"><i class="fa-solid fa-user"></i> Sign In</a>
            </div>
        </div>
    </nav>

    <!-- Genre Filters -->
    <div class="genre-filters">
        <a href="shop.php">All</a>
        <a href="shop.php?genre=Fantasy">Fantasy</a>
        <a href="shop.php?genre=Contemporary">Contemporary</a>
        <a href="shop.php?genre=Sci-Fi">Sci-Fi</a>
        <a href="shop.php?genre=Dystopian">Dystopian</a>
        <a href="shop.php?genre=Adventure">Adventure</a>
        <a href="shop.php?genre=Romance">Romance</a>
        <a href="shop.php?genre=Mystery">Mystery</a>
        <a href="shop.php?genre=Horror">Horror</a>
        <a href="shop.php?genre=Thriller">Thriller</a>
        <a href="shop.php?genre=Historical">Historical</a>
    </div>

    <!-- Book Display -->
    <div class="book-container">
    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='book-item'>";
            echo "<img src='uploads/" . $row['book_cover'] . "' alt='" . $row['title'] . " cover' class='book-cover'>";
            echo "<h3>" . $row['title'] . "</h3>";
            echo "<p class='author'>" . $row['author'] . "</p>";
            echo "<p class='price'>$" . $row['price'] . "</p>";
            echo "<button class='add-to-cart'>Add to Cart</button>";
            echo "</div>";
        }
    } else {
        echo "<p>No books found.</p>";
    }
    ?>
</div>

</body>
</html>
