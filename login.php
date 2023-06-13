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
$errorMessage = '';

// Check if a valid and unused token ID is present in the URL
if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);

    // Verify the token in the database
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE reset_token = ? AND is_verified = 0 AND reset_token_expiration > NOW()");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Mark the user as verified
        $stmt = mysqli_prepare($conn, "UPDATE users SET is_verified = 1, reset_token = NULL, reset_token_expiration = NULL WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user['user_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect to the home page or any other desired page
        header("Location: index.html");
        exit;
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}

// Check if the login form is submitted
if (isset($_POST['login'])) {
    // Get the form inputs
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validate the form inputs
    if (empty($email) || empty($password)) {
        $error = true;
        $errorMessage = "All fields are required.";
    } else {
        // Check if the user exists in the database
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Check if the user is verified
            if ($user['is_verified']) {
                // Verify the password
                if (md5($password) == $user['password']) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    // Redirect to the home page or any other desired page
                    header("Location: index.html");
                    exit;
                } else {
                    $error = true;
                    $errorMessage = "Invalid email or password.";
                }
            } else {
                $error = true;
                $errorMessage = "Your email is not verified. Please check your email for verification instructions.";
            }
        } else {
            $error = true;
            $errorMessage = "Invalid email or password.";
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    }
}

// Resend verification email
if (isset($_POST['resend'])) {
    // Get the form input
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Generate a new verification token
    $verificationToken = bin2hex(openssl_random_pseudo_bytes(16));

    // Set the expiration time for the verification token (e.g., 24 hours from now)
    $verificationTokenExpiration = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Update the verification token in the database
    $stmt = mysqli_prepare($conn, "UPDATE users SET reset_token = ?, reset_token_expiration = ? WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "sss", $verificationToken, $verificationTokenExpiration, $email);
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);

    // Send the verification email
    $subject = "Account Verification";
    $verificationLink = "http://tahirkaloo.tk/login.php?token=" . urlencode($verificationToken);
    $message = "Please click the following link to verify your email: " . $verificationLink;
    $command = 'aws ses send-email --region us-east-1 --from admin@tahirkaloo.tk --to ' . $email . ' --subject "' . $subject . '" --text "' . $message . '"';
    exec($command);

    // Display success message
    $errorMessage = "Verification email has been resent. Please check your email.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        /* Additional CSS styles for login page */
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

        .resend-button {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #6c63ff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.html">Home</a>
        <a href="register.html">Register</a>
    </div>
    <div class="container">
        <h2>Login</h2>
        <?php
        if ($error) {
            echo '<div class="error-message">' . $errorMessage . '</div>';
        }
        ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <input type="submit" name="login" value="Login">
            </div>
        </form>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <input type="submit" name="resend" value="Resend Verification Email" class="resend-button">
            </div>
        </form>
    </div>
</body>
</html>

