<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection if not already included
if (!function_exists('mysqli_init') || !isset($conn)) {
    include 'db.php';
}

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: userauth.php");
    exit;
}

// Verify user still exists in database
$check_sql = "SELECT id FROM shopuser WHERE id = ? AND email = ?";
if ($check_stmt = $conn->prepare($check_sql)) {
    $check_stmt->bind_param("is", $_SESSION["id"], $_SESSION["email"]);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows == 0) {
        // User no longer exists in database
        session_destroy();
        header("location: userauth.php");
        exit;
    }
    $check_stmt->close();
}
?>
