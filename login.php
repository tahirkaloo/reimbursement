<?php
session_start();
require_once 'db_connect.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

$error = false;
$errorMessage = '';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (empty($email) || empty($password)) {
        $error = true;
        $errorMessage = "Email and password are required.";
    } elseif (isset($_POST['resetPassword'])) {
        // Handle password reset request
        $query = "SELECT user_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            // Generate a new password
            $newPassword = generateRandomPassword(); // Implement your own logic to generate a new password

            // Update the user's password in the database
            $hashedPassword = md5($newPassword); // Hash the new password using md5()
            $stmt->bind_result($userId);
            $stmt->fetch();
            $stmt->close();

            $updateQuery = "UPDATE users SET password = ? WHERE user_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("si", $hashedPassword, $userId);
            $updateStmt->execute();
            $updateStmt->close();

            // Send the new password to the user via email or any other method
            $subject = "Password Reset";
            $message = "Your new password: $newPassword";
            // Send the email using your preferred email sending method
            // Replace the following code with your own logic
            mail($email, $subject, $message);

            $successMessage = "Your password has been reset. Please check your email for the new password.";
        } else {
            $error = true;
            $errorMessage = "Invalid email. Please try again.";
        }
    } else {
        // Authenticate the user
        $stmt = mysqli_prepare($conn, "SELECT user_id, email, password, role FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $userId, $fetchedEmail, $hashedPassword, $role);
            mysqli_stmt_fetch($stmt);

            if (md5($password) === $hashedPassword) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['role'] = $role;
                header("Location: index.html");
                exit;
            } else {
                $error = true;
                $errorMessage = "Invalid email or password.";
            }
        } else {
            $error = true;
            $errorMessage = "Invalid email or password.";
        }

        mysqli_stmt_close($stmt);
    }
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
            margin-right: 20px;
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
    <h2>Login</h2>
    <?php if ($error): ?>
        <p class="error-message"><?php echo $errorMessage; ?></p>
    <?php elseif (isset($successMessage)): ?>
        <p class="success-message"><?php echo $successMessage; ?></p>
    <?php endif; ?>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
            <button type="submit" name="login">Login</button>
        </div>
        <div class="form-group">
            <button type="submit" name="resetPassword">Reset Password</button>
        </div>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>
</body>
</html>

