<?php
include 'db.php';

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // Fetch book details from the database
    $query = "SELECT * FROM bookstore WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $book_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($book = mysqli_fetch_assoc($result)) {
        echo json_encode($book);
    } else {
        echo json_encode(['error' => 'Book not found']);
    }
}

mysqli_close($conn);
?>
