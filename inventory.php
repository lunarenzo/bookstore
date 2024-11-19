<?php

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.html");
    exit();
}

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
    $isbn = $_POST['isbn']; // Get ISBN from the form
    $genre = $_POST['genre'];
    

    // Check if the ISBN already exists in the database
    $isbn_check_query = "SELECT * FROM bookstore WHERE isbn = ?";
    $stmt = mysqli_prepare($conn, $isbn_check_query);
    mysqli_stmt_bind_param($stmt, 's', $isbn); // 's' means string type
    mysqli_stmt_execute($stmt);
    $isbn_check_result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($isbn_check_result) > 0) {
        // ISBN already exists
        $error_message = 'Error: This ISBN already exists in the database.';
    } else {
        // Handle file upload
        if (isset($_FILES['book_cover']) && $_FILES['book_cover']['error'] == 0) {
            $book_cover = $_FILES['book_cover'];

            // Check file type and size (optional)
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $file_extension = pathinfo($book_cover['name'], PATHINFO_EXTENSION);
            if (in_array(strtolower($file_extension), $allowed_extensions) && $book_cover['size'] <= 2000000) { // 2MB limit
                $upload_dir = 'uploads/';
                $file_name = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;

                // Move the file to the uploads folder
                if (move_uploaded_file($book_cover['tmp_name'], $upload_path)) {
                    // Insert into the database using a prepared statement
                    $sql = "INSERT INTO bookstore (title, author, price, date_published, genre, book_cover, isbn) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)"; 

                    $stmt = mysqli_prepare($conn, $sql);
                    // Bind parameters: 'ssdsdss' corresponds to title, author, price, date_published, genre, file_name, isbn
                    mysqli_stmt_bind_param($stmt, 'ssdssss', $title, $author, $price, $date_published, $genre, $file_name, $isbn);

                    if (mysqli_stmt_execute($stmt)) {
                        $success_message = 'New book added successfully!';
                    } else {
                        $error_message = 'Error: ' . mysqli_error($conn);
                    }
                } else {
                    $error_message = 'Failed to upload the file.';
                }
            } else {
                $error_message = 'Invalid file type or size.';
            }
        } else {
            $error_message = 'No file uploaded or an error occurred during upload.';
        }
    }
}


// Handle form submission for editing an existing book
if (isset($_POST['edit_book'])) {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $date_published = $_POST['date_published'];
    $isbn = $_POST['isbn'];
    $genre = $_POST['genre'];

    // Check if the ISBN already exists (if it's being changed)
    $isbn_check_query = "SELECT * FROM bookstore WHERE isbn = ? AND id != ?";
    $stmt = mysqli_prepare($conn, $isbn_check_query);
    mysqli_stmt_bind_param($stmt, 'si', $isbn, $book_id);
    mysqli_stmt_execute($stmt);
    $isbn_check_result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($isbn_check_result) > 0) {
        // ISBN already exists
        $error_message = 'Error: This ISBN already exists in the database.';
    } else {
        // Update the book record in the database
        $sql = "UPDATE bookstore SET title = ?, author = ?, price = ?, date_published = ?, genre = ?, isbn = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssdssss', $title, $author, $price, $date_published, $genre, $isbn, $book_id);

        if (mysqli_stmt_execute($stmt)) {
            $success_message = 'Book updated successfully!';
        } else {
            $error_message = 'Error: ' . mysqli_error($conn);
        }
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
                        <a href="index.php" class="nav-link active">
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
                    <th>ISBN</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Price</th>
                    <th>Date Published</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- PHP loop for displaying books -->
                <?php while ($book = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $book['isbn']; ?></td>
                        <td><?php echo $book['title']; ?></td>
                        <td><?php echo $book['author']; ?></td>
                        <td><?php echo $book['genre']; ?></td>
                        <td>$<?php echo $book['price']; ?></td>
                        <td><?php echo $book['date_published']; ?></td>
                        <td>
                        <button class="btn btn-edit" onclick="openEditModal(<?php echo $book['id']; ?>)">Edit</button>
                            <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn btn-delete">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>


<!-- Add New Book Modal -->
<div id="addBookModal" class="modal">
    <div class="modal-content">
        <h2 class="modal-title">Add New Book</h2>
        <form action="inventory.php" method="POST" enctype="multipart/form-data">
            <label for="title">Book Title</label>
            <input type="text" id="title" name="title" placeholder="Enter book title" required>

            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" placeholder="Enter ISBN" required>

            <label for="author">Author</label>
            <input type="text" id="author" name="author" placeholder="Enter author name" required>

            <label for="price">Price</label>
            <input type="number" id="price" name="price" placeholder="Enter price" required step="0.01">

            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre" placeholder="Enter genre" required>

            <label for="date_published">Date Published</label>
            <input type="date" id="date_published" name="date_published" required>

            <label for="book_cover">Book Cover</label>
            <input type="file" id="book_cover" name="book_cover" accept="image/*" required>

            <div class="modal-footer">
                <button type="submit" name="add_book">Add Book</button>
                <button type="button" class="btn btn-close" onclick="closeModal('addBookModal')">Close</button>
            </div>
        </form>
    </div>
</div>

                <!-- Edit Book Modal -->
        <div id="editBookModal" class="modal">
            <div class="modal-content">
                <h2 class="modal-title">Edit Book</h2>
                <form action="inventory.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="book_id" name="book_id">
                    <label for="title">Book Title</label>
                    <input type="text" id="edit_title" name="title" required>

                    <label for="isbn">ISBN</label>
                    <input type="text" id="edit_isbn" name="isbn" required>

                    <label for="author">Author</label>
                    <input type="text" id="edit_author" name="author" required>

                    <label for="price">Price</label>
                    <input type="number" id="edit_price" name="price" required step="0.01">

                    <label for="genre">Genre</label>
                    <input type="text" id="edit_genre" name="genre" required>

                    <label for="date_published">Date Published</label>
                    <input type="date" id="edit_date_published" name="date_published" required>
                    <div class="modal-footer">
                        <button type="submit" name="edit_book">Update Book</button>
                        <button type="button" class="btn btn-close" onclick="closeModal('editBookModal')">Close</button>
                    </div>
                </form>
            </div>
        </div>

<br>
<br>

<!-- Recently Added Books Section -->
<h2>Recently Added Books</h2>
<div class="recent-books">
    <!-- PHP loop to fetch the 3 most recent books -->
    <?php
    $recent_books_query = "SELECT * FROM bookstore ORDER BY id DESC LIMIT 5";
    $recent_books_result = mysqli_query($conn, $recent_books_query);

    while ($recent_book = mysqli_fetch_assoc($recent_books_result)): ?>
        <div class="book-card">
            <img src="uploads/<?php echo $recent_book['book_cover']; ?>" alt="<?php echo $recent_book['title']; ?>" class="book-cover">
            <div class="book-details">
                <h3><?php echo $recent_book['title']; ?></h3>
                <p><strong>Author:</strong> <?php echo $recent_book['author']; ?></p>
                <p><strong>Genre:</strong> <?php echo $recent_book['genre']; ?></p>
                <p><strong>Price:</strong> $<?php echo $recent_book['price']; ?></p>
                <p><strong>Date:</strong> <?php echo $recent_book['date_published']; ?></p>
            </div>
        </div>
    <?php endwhile; ?>
</div>
</main>

    <!-- Close the database connection -->
<?php
// Ensure all database operations are done before closing the connection
if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>
<script src="main.js"></script>
</body>
</html>