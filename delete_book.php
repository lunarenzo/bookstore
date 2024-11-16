<?php
// Include database connection
include 'db.php';

// Handle the deletion of a book
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM bookstore WHERE id = $id";
    
    if (mysqli_query($conn, $sql)) {
        // Redirect to inventory.php with a success message
        header('Location: inventory.php?deleted=true');
    } else {
        // Redirect to inventory.php with an error message
        header('Location: inventory.php?error=' . urlencode('Error: ' . mysqli_error($conn)));
    }
}

// Close the database connection
mysqli_close($conn);
?>
