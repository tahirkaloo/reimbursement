<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit;
}

$error = false;

// Check if the password update form is submitted
if (isset($_POST['update_password'])) {
    // Get the form inputs
    $currentPassword = mysqli_real_escape_string($conn, $_POST['current_password']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Validate the form inputs
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = true;
        $errorMessage = "All fields are required.";
    } elseif ($newPassword != $confirmPassword) {
        $error = true;
        $errorMessage = "New password and confirm password must match.";
    } else {
        $userId = $_SESSION['user_id'];

        // Retrieve the current hashed password from the database
        $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $hashedPassword);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Verify the current password
        if (password_verify($currentPassword, $hashedPassword)) {
            // Password is correct, proceed with updating the password
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the hashed password in the database
            $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt, "si", $newHashedPassword, $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $successMessage = "Password updated successfully.";
        } else {
            $error = true;
            $errorMessage = "Incorrect current password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Password</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Additional CSS styles for update password page */
        /* ... */

    </style>
</head>
<body>
    <!-- Update Password HTML code -->
    <!-- ... -->

    <div class="container">
        <h2>Update Password</h2>

        <?php if (isset($errorMessage)): ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <?php if (isset($successMessage)): ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" id="current_password" required>
            </div>

            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>

            <div class="form-group">
                <button type="submit" name="update_password" class="btn">Update Password</button>
            </div>
        </form>
    </div>
</body>
</html>

