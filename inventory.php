<?php
// Include database connection
include 'db.php';

// Initialize messages
$success_message = '';
$error_message = '';
$deleted_message = '';

// Handle form submission for adding a new book
if (isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $date_published = $_POST['date_published'];

    // Insert into the database
    $sql = "INSERT INTO bookstore (title, author, price, date_published) VALUES ('$title', '$author', '$price', '$date_published')";
    
    if (mysqli_query($conn, $sql)) {
        $success_message = 'New book added successfully!';
    } else {
        $error_message = 'Error: ' . mysqli_error($conn);
    }
}

// Fetch all books from the database
$sql = "SELECT * FROM bookstore";
$result = mysqli_query($conn, $sql);

// Check for 'deleted' query parameter
if (isset($_GET['deleted']) && $_GET['deleted'] == 'true') {
    $deleted_message = 'Book deleted successfully!';
}

// Check for 'error' query parameter
if (isset($_GET['error'])) {
    $error_message = $_GET['error'];
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en" data-theme="Light">
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
                        <a href="index.html" class="nav-link active">
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
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-star"></i>
                            <span class="nav-text">Reviews</span>
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
                        <a href="#" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span class="nav-text">Store Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-question-circle"></i>
                            <span class="nav-text">Help</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="theme-toggle-wrapper">
            <button class="theme-toggle" aria-label="Toggle theme">
                <i class="fas fa-moon"></i>
                <span class="theme-toggle-text">Switch to Light Mode</span>
            </button>
        </div>
    </aside>
    <main class="content">
        <h1>Book Inventory</h1>

         <!-- Show success or error message if available -->
        <?php if ($success_message): ?>
            <div class="alert-success show"><?= $success_message ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert-error show"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- Show deleted message if a book was deleted -->
        <?php if ($deleted_message): ?>
            <div class="alert-deleted show"><?= $deleted_message ?></div>
        <?php endif; ?>


        <!-- Button to open the modal -->
        <button id="openModalBtn" class="btn btn-primary">Add New Book</button>

        <!-- Table displaying the list of books -->
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Date Published</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- PHP loop for displaying books -->
                <?php while ($book = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $book['id']; ?></td>
                        <td><?php echo $book['title']; ?></td>
                        <td><?php echo $book['author']; ?></td>
                        <td>$<?php echo $book['price']; ?></td>
                        <td><?php echo $book['date_published']; ?></td>
                        <td>
                            <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-edit">Edit</a>
                            <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn btn-delete">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Add New Book Modal -->
        <div id="addBookModal" class="modal">
            <div class="modal-content">
                <span id="closeModalBtn" class="close-btn">&times;</span>
                <h2 class="modal-title">Add New Book</h2>
                <form action="inventory.php" method="POST">
                    <label for="title">Book Title</label>
                    <input type="text" id="title" name="title" placeholder="Enter book title" required>

                    <label for="author">Author</label>
                    <input type="text" id="author" name="author" placeholder="Enter author name" required>

                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" placeholder="Enter price" required step="0.01">

                    <label for="date_published">Date Published</label>
                    <input type="date" id="date_published" name="date_published" required>

                    <button type="submit" name="add_book">Add Book</button>
                </form>
            </div>
        </div>

    </main>

<script src="main.js"></script>
</body>
</html>