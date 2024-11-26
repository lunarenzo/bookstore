<?php
// Include the database connection file
include('db.php');

// Start session to track user login status
session_start();

// Initialize variables for form input and errors
$email = $password = "";
$email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check for errors before authenticating
    if (empty($email_err) && empty($password_err)) {
        // Prepare SQL query to check if the email exists in the database
        $sql = "SELECT id, email, password FROM shopuser WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            // Check if email exists
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $email_db, $hashed_password);
                if ($stmt->fetch()) {
                    // Verify password
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a new session
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["email"] = $email_db;

                        // Redirect to the user dashboard or another protected page
                        header("location: shop.php");
                        exit;
                    } else {
                        $password_err = "The password you entered was not correct.";
                    }
                }
            } else {
                $email_err = "No account found with that email.";
            }

            $stmt->close();
        }
    }

    // Close the connection
    $conn->close();
}
?>
