<?php
session_start();

// Database connection
$servername = "localhost"; // Change this to your server
$username = "root";        // Change this to your database username
$password = "";            // Change this to your database password
$dbname = "bookstore"; // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for form inputs
$email = $password = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password using MD5
    $hashedPassword = md5($password);

    // Prepare SQL query to check if the email and password exist
    $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $hashedPassword);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        // Start a session and store user information
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_email'] = $email;

        // Redirect to index.html after successful login
        header("Location: index.php");
        exit();
    } else {
        echo "Invalid email or password!";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
