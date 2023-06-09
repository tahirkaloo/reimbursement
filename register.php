<?php
session_start();

// AWS SES configuration
$awsRegion = 'us-east-1'; // Replace with your AWS region
$senderEmail = 'admin@tahirkaloo.tk'; // Replace with your sender email

// Include the database connection
require_once 'db_connect.php';

// Error reporting and timezone settings
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/New_York'); // Replace 'America/New_York' with your desired timezone

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.html"); // Redirect to the home page or any other desired page
    exit;
}

$error = false;
$successMessage = '';
$errorMessage = '';

// Check if the registration form is submitted
if (isset($_POST['register'])) {
    // Get the form inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Validate the form inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = true;
        $errorMessage = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $errorMessage = "Invalid email format.";
    } elseif ($password != $confirmPassword) {
        $error = true;
        $errorMessage = "Passwords do not match.";
    } else {
        // Check if the user already exists in the database
        $checkStmt = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($checkStmt, "s", $email);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);

        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            $error = true;
            $errorMessage = "User with this email already exists.";
        } else {
            // Generate a unique verification token
            $verificationToken = bin2hex(openssl_random_pseudo_bytes(16));

            // Set the expiration time for the verification token (e.g., 1 hour from now)
            $verificationTokenExpiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Hash the password using md5()
            $hashedPassword = md5($password);

            // Prepare the SQL statement
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, verification_token, verification_token_expiration) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                // Bind parameters and execute the statement
                mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $hashedPassword, $verificationToken, $verificationTokenExpiration);
                $result = mysqli_stmt_execute($stmt);

                if ($result) {
                    $successMessage = "Registration successful. Please check your email to verify your account.";

                    // Send verification email
                    $subject = "Account Verification";
                    $message = "Thank you for registering. Please click the link below to verify your account:\n\n";
                    $message .= "http://tahirkaloo.tk/login.php?email=" . urlencode($email) . "&token=" . urlencode($verificationToken);

                    // Use AWS CLI to send the email
                    $awsCommand = 'aws ses send-email --region ' . escapeshellarg($awsRegion) . ' --from ' . escapeshellarg($senderEmail) . ' --to ' . escapeshellarg($email) . ' --subject ' . escapeshellarg($subject) . ' --text ' . escapeshellarg($message);
                    exec($awsCommand, $output, $returnVar);

                    if ($returnVar !== 0) {
                        $error = true;
                        $errorMessage = "Registration failed. Please try again.";
                    }
                } else {
                    $error = true;
                    $errorMessage = "Registration failed. Please try again.";
                }
            } else {
                $error = true;
                $errorMessage = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        /* Additional CSS styles for registration page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            color: #fff;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-left: 10px;
        }

        .navbar a:first-child {
            margin-left: 0;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #6c63ff;
            box-shadow: 0 0 10px rgba(108, 99, 255, 0.2);
        }

        .error-message {
            color: red;
            margin-top: 5px;
        }

        .success-message {
            color: green;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<!-- Login Navbar -->
<div class="navbar">
    <div>
        <a href="index.html">Home</a>
    </div>
    <div>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
    </div>
</div>

<div class="container">
    <h2>Registration</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" />
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" />
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" />
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" />
        </div>
        <div class="form-group">
            <button type="submit" name="register">Register</button>
        </div>
        <?php if ($error): ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <?php if ($successMessage): ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php endif; ?>
    </form>
</div>

</body>
</html>

