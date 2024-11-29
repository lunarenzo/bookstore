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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Bookverse</title>
    <link rel="stylesheet" href="userauthStyle.css">
</head>
<body>
  <main>
    <div class="left-side"></div>
    <div class="right-side">
      <form method="POST">
        <div class="title-section">
          <h1 class="site-title">Sign In</h1>
        </div>
  
        <label for="email">Email</label>
        <input type="text" placeholder="Enter Email" name="email" required />
  
        <label for="password">Password</label>
        <input type="password" placeholder="Enter Password" name="password" required />
  
        <button type="submit" class="login-btn">Sign In</button>
        <div class="links">
          <a href="#">Forgot password?</a>
          <a href="userReg.php">Sign Up</a>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
