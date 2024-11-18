<?php
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
$fullName = $email = $password = $confirmPassword = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $agreeToTerms = isset($_POST['agreeToTerms']) ? true : false;

    // Check if password and confirm password match
    if ($password !== $confirmPassword) {
        echo "Passwords do not match!";
    } else if (!$agreeToTerms) {
        echo "You must agree to the terms and conditions!";
    } else {
        // Hash password using MD5
        $hashedPassword = md5($password);

        // Prepare SQL query to insert user data into the database
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fullName, $email, $hashedPassword);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            header("Location: login.html");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>
