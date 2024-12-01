<?php
require_once 'db.php';

// Initialize variables
$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
        // Check if email exists in the database
        $sql = "SELECT id FROM shopuser WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $email_err = "This email is already taken.";
            }
            $stmt->close();
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must be at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm-password"]))) {
        $confirm_password_err = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST["confirm-password"]);
        if ($password !== $confirm_password) {
            $confirm_password_err = "Passwords do not match.";
        }
    }

    // Check for errors before inserting into the database
    if (empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert the user into the database
        $sql = "INSERT INTO shopuser (email, password) VALUES (?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $email, $password_hash);
            if ($stmt->execute()) {
                // Redirect to login page after successful registration
                header("Location: userAuth.php");
                exit; // Make sure the script exits after header redirection
            } else {
                echo "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    } else {
        // Display error messages if there are any
        echo "Error: " . $email_err . " " . $password_err . " " . $confirm_password_err;
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
    <title>Sign Up | Bookverse</title>
    <link rel="stylesheet" href="css/userauthStyle.css">
</head>
<body>
  <main>
    <div class="left-side"></div>
    <div class="right-side">
      <form method="POST">
        <div class="title-section">
          <h1 class="site-title">Sign Up</h1>
        </div>
  
        <label for="email">Email</label>
        <input type="text" placeholder="Enter Email" name="email" required />
  
        <label for="password">Password</label>
        <input type="password" placeholder="Enter Password" name="password" required />
  
        <label for="confirm-password">Confirm Password</label>
        <input type="password" placeholder="Confirm Password" name="confirm-password" required />
  
        <button type="submit" class="login-btn">Register</button>
        <div class="links">
          <a>or</a>
          <a href="userAuth.php">Sign In</a>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
