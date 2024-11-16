<?php
// Include database connection
include 'db.php';

// Fetch book details based on ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM bookstore WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    $book = mysqli_fetch_assoc($result);
}

// Handle form submission for updating book
if (isset($_POST['update_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $date_published = $_POST['date_published'];

    // Update the book in the database
    $sql = "UPDATE bookstore SET title = '$title', author = '$author', price = '$price', date_published = '$date_published' WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header('Location: inventory.php');
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
</head>
<body>
    <h1>Edit Book</h1>
    <form method="POST" action="edit_book.php?id=<?= $book['id'] ?>">
        <label for="title">Book Title</label>
        <input type="text" id="title" name="title" value="<?= $book['title'] ?>" required>

        <label for="author">Author</label>
        <input type="text" id="author" name="author" value="<?= $book['author'] ?>" required>

        <label for="price">Price</label>
        <input type="number" id="price" name="price" value="<?= $book['price'] ?>" required step="0.01">

        <label for="date_published">Date Published</label>
        <input type="date" id="date_published" name="date_published" value="<?= $book['date_published'] ?>" required>

        <button type="submit" name="update_book">Update Book</button>
    </form>
</body>
</html>