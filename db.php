<?php
$servername = "localhost";  // Change this if you are using a different host
$username = "root";         // Your MySQL username
$password = "";             // Your MySQL password (empty for local setup)
$dbname = "bookstore";      // The name of the database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>