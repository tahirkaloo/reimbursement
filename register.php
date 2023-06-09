<?php
session_start();
require_once 'db_connect.php';

// Set the timezone
date_default_timezone_set('UTC');

// Do reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Validate the form inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
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
            // Generate a unique verification code
            $verificationCode = bin2hex(openssl_random_pseudo_bytes(16));

            // Hash the password using md5()
            $hashedPassword = md5($password);

            // Prepare the SQL statement
	    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, reset_token) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                // Bind parameters and execute the statement
                mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashedPassword, $verificationCode);
                $result = mysqli_stmt_execute($stmt);

                if ($result) {
                    $successMessage = "Registration successful. Please check your email to verify your account.";

                    // Send the verification email
                    $subject = "Account Verification";
                    $message = "Dear $username,\n\nThank you for registering an account. To verify your account, please click the following link:\n\n";
                    $message .= "http://tahirkaloo.tk/login.php?email=" . urlencode($email) . "&code=" . urlencode($verificationCode);

                    // Use AWS CLI to send the email
                    $command = 'echo "' . addslashes($message) . '" | /usr/bin/aws ses send-email --region us-east-1 --from admin@tahirkaloo.tk --to ' . addslashes($email) . ' --subject "' . addslashes($subject) . '" --text "' . addslashes($message) . '"';
                    $output = shell_exec($command);
                    error_log("Command: $command");
                    error_log("Output: $output");
                } else {
                    $errorMessage = "Something went wrong. Please try again later.";
                    error_log("Error executing prepared statement: " . mysqli_error($conn));
                }

                // Close the statement
                mysqli_stmt_close($stmt);
            } else {
                $errorMessage = "Something went wrong. Please try again later.";
                error_log("Error preparing statement: " . mysqli_error($conn));
            }
        }

        // Close the check statement
        mysqli_stmt_close($checkStmt);
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
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <?php if (!empty($successMessage)): ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <div class="form-group">
                <button type="submit" name="register">Register</button>
            </div>
        </form>
    </div>

</body>
</html>

