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
$fullName = $email = $password = $confirmPassword = "";
$fullName_err = $email_err = $password_err = $confirmPassword_err = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form inputs and clean them up to prevent SQL injection and XSS attacks
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);


    // Validation for the form fields
    if (empty($fullName)) {
        $fullName_err = "Full name is required.";
    }

    if (empty($email)) {
        $email_err = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    }

    if (empty($password)) {
        $password_err = "Password is required.";
    } elseif (strlen($password) < 6) {
        $password_err = "Password must be at least 6 characters.";
    }

    if (empty($confirmPassword)) {
        $confirmPassword_err = "Please confirm your password.";
    } elseif ($password !== $confirmPassword) {
        $confirmPassword_err = "Passwords do not match.";
    }

    // If there are no errors, proceed with registration
    if (empty($fullName_err) && empty($email_err) && empty($password_err) && empty($confirmPassword_err) && empty($terms_err)) {
        // Hash password using password_hash() (bcrypt)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists in the database
        $stmt = $conn->prepare("SELECT id FROM adminuser WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $email_err = "Email is already taken.";
        } else {
            // Prepare SQL query to insert user data into the database
            $stmt = $conn->prepare("INSERT INTO adminuser (full_name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullName, $email, $hashedPassword);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect to login page after successful registration
                header("Location: adminLogin.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        }

        // Close the connection
        $conn->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="css\adminAuth.css">
</head>

<body>
  <form class="form" method="POST">
    <div class="title-section">
      <h1 class="site-title"><i class="fas fa-book logo-icon"></i> Bookverse Admin <i class="fas fa-book logo-icon"></i></h1>
    </div>
    <div class="flex-column">
      <label>Full Name</label>
    </div>
    <div class="form-input">
      <i class="fa-solid fa-user"></i>
      <input type="text" name="fullName" class="input" placeholder="Enter your Full Name" required value="<?php echo htmlspecialchars($fullName); ?>">
      <?php if ($fullName_err) echo '<span style="color: red;">' . $fullName_err . '</span>'; ?>
    </div>

    <div class="flex-column">
      <label>Email</label>
    </div>
    <div class="form-input">
      <i class="fa-solid fa-at"></i>
      <input type="email" name="email" class="input" placeholder="Enter your Email" required value="<?php echo htmlspecialchars($email); ?>">
      <?php if ($email_err) echo '<span style="color: red;">' . $email_err . '</span>'; ?>
    </div>

    <div class="flex-column">
      <label>Password</label>
    </div>
    <div class="form-input">
      <i class="fa-solid fa-lock"></i>
      <input type="password" name="password" class="input" id="password" placeholder="Enter your Password" required>
      <?php if ($password_err) echo '<span style="color: red;">' . $password_err . '</span>'; ?>
      <i class="fa-solid fa-eye" id="togglePassword"></i>
    </div>

    <div class="flex-column">
      <label>Confirm Password</label>
    </div>
    <div class="form-input">
      <i class="fa-solid fa-lock"></i>
      <input type="password" name="confirmPassword" class="input" id="confirmPassword" placeholder="Confirm your Password" required>
      <?php if ($confirmPassword_err) echo '<span style="color: red;">' . $confirmPassword_err . '</span>'; ?>
      <i class="fa-solid fa-eye" id="toggleConfirmPassword"></i>
    </div>


    <button type="submit" class="button-submit">Sign Up</button>
    <p class="p">
      Already have an account?
      <a href="adminLogin.php">
        <span class="span">Sign In</span>
      </a>
    </p>

  </form>
</body>
<script src="main.js"></script>
</html>
