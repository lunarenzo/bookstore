<?php
session_start();

$servername = "localhost";
$username = "root";        
$password = "";            
$dbname = "bookstore";     


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for form inputs
$email = $password = "";
$email_err = $password_err = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check for errors before querying the database
    if (empty($email_err) && empty($password_err)) {
        // Prepare SQL query to check if the email exists
        $stmt = $conn->prepare("SELECT id, full_name, password FROM adminuser WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Check if password matches the hashed password
            if (password_verify($password, $user['password'])) {
                // Start a session and store user information
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_email'] = $email;

                // Redirect to the dashboard or another page after successful login
                header("Location: index.php"); // Change this to the actual page for the logged-in user
                exit();
            } else {
                $login_error = "Invalid email or password!";
            }
        } else {
            $login_error = "Invalid email or password!";
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In</title>
  <link rel="stylesheet" href="adminAuth.css">
</head>
<body>

  <form class="form" method="POST">
    <div class="title-section">
      <h1 class="site-title"><i class="fas fa-book logo-icon"></i> Bookverse Admin <i class="fas fa-book logo-icon"></i></h1>
    </div>

    <div class="flex-column">
      <label>Email</label>
    </div>
    <div class="form-input">
      <i class="fa-solid fa-at"></i>
      <input type="text" name="email" class="input" placeholder="Enter your Email" required value="<?php echo htmlspecialchars($email); ?>">
    </div>

    <div class="flex-column">
      <label>Password</label>
    </div>
    <div class="form-input">
      <i class="fa-solid fa-lock"></i>
      <input type="password" name="password" class="input" id="password" placeholder="Enter your Password" required>
      <i class="fa-solid fa-eye" id="togglePassword"></i>
    </div>

    <?php 
    if (isset($login_error)) {
        echo '<p style="color: red; font-size: 14px;">' . $login_error . '</p>';
    }
    ?>
    <button type="submit" class="button-submit">Sign In</button>
    <p class="p">
      Don't have an account?
      <a href="adminRegister.php">
        <span class="span">Sign Up</span>
      </a>
    </p>
  </form>
</body>
<script src="main.js"></script>
</html>
