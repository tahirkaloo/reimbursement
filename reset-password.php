<?php
require_once 'db_connect.php';

$error = false;
$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        // Validate the form data
        if (empty($password) || empty($confirmPassword)) {
            $error = true;
            $errorMessage = "Please enter both password fields.";
        } elseif ($password !== $confirmPassword) {
            $error = true;
            $errorMessage = "Passwords do not match.";
        } else {
            // Check if the reset token exists in the database and is not expired
            $query = "SELECT email, reset_token_expiration FROM users WHERE reset_token = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->bind_result($email, $resetTokenExpiration);
            $stmt->fetch();
            $stmt->close();

            if ($email) {
                // Check if the reset token is not expired
                if (strtotime($resetTokenExpiration) >= time()) {
                    // Hash the new password using MD5
                    $hashedPassword = md5($password);

                    // Update the user's password in the database
                    $updateQuery = "UPDATE users SET password = ? WHERE email = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bind_param("ss", $hashedPassword, $email);
                    $updateStmt->execute();
                    $updateStmt->close();

                    $successMessage = "Your password has been successfully updated.";
                } else {
                    $error = true;
                    $errorMessage = "Invalid or expired reset token.";
                }
            } else {
                $error = true;
                $errorMessage = "Invalid reset token.";
            }
        }
    } else {
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        // Validate the form data
        if (empty($email)) {
            $error = true;
            $errorMessage = "Please enter your email address.";
        } else {
            // Check if the email exists in the database
            $query = "SELECT email FROM users WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // Generate a unique reset token
                $resetToken = bin2hex(openssl_random_pseudo_bytes(16));

                // Set the expiration time for the reset token (e.g., 1 hour from now)
                $resetTokenExpiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Update the user's reset token and expiration time in the database
                $updateQuery = "UPDATE users SET reset_token = ?, reset_token_expiration = ? WHERE email = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("sss", $resetToken, $resetTokenExpiration, $email);
                $updateStmt->execute();
                $updateStmt->close();

                // Send the password reset email to the user
                $resetLink = "http://tahirkaloo.tk/reset-password.php?token=" . $resetToken; // Update with your actual reset-password.php URL
                $emailSubject = "Password Reset";
                $emailBody = "Hi,\n\nYou have requested to reset your password. Click the link below to reset your password:\n\n$resetLink\n\nIf you did not request a password reset, please ignore this email.\n\nBest regards,\nThe Website Team";

                // Send the email using AWS CLI
                $awsCliCommand = "aws ses send-email --from 'admin@tahirkaloo.tk' --to '$email' --subject '$emailSubject' --text '$emailBody'";
                exec($awsCliCommand, $output, $returnValue);

                if ($returnValue === 0) {
                    $successMessage = "An email with instructions to reset your password has been sent to your email address.";
                } else {
                    $error = true;
                    $errorMessage = "Failed to send the password reset email. Please try again later.";
                }
            } else {
                // Email does not exist in the database
                $error = true;
                $errorMessage = "Email address not found. Please enter a valid email address.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        /* Additional CSS styles for reset password page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
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
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-group .error-message {
            color: #ff0000;
        }

        .form-group .success-message {
            color: #008000;
        }

        .form-group .button {
            display: inline-block;
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if ($successMessage !== ''): ?>
            <div class="form-group">
                <p class="success-message"><?php echo $successMessage; ?></p>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="form-group">
                    <p class="error-message"><?php echo $errorMessage; ?></p>
                </div>
            <?php endif; ?>
            <form method="post" action="">
                <?php if (isset($_GET['token'])): ?>
                    <div class="form-group">
                        <label for="password">New Password:</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" name="confirm_password" id="confirm_password" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="button">Reset Password</button>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="button">Send Reset Link</button>
                    </div>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

