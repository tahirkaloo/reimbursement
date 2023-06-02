<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the user ID from the session
$userID = $_SESSION['user_id'];

// Retrieve the user's details from the database
$query = "SELECT username, email, role FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $email = $row['email'];
    $role = $row['role'];
} else {
    // Handle error if user data cannot be retrieved
    // Redirect or display an error message
    header("Location: error.php");
    exit;
}

$stmt->close();

// Password change form handling
if (isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];

    // Verify current password
    $verifyQuery = "SELECT password FROM users WHERE user_id = ?";
    $verifyStmt = $conn->prepare($verifyQuery);
    $verifyStmt->bind_param("i", $userID);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();

    if ($verifyResult->num_rows === 1) {
        $row = $verifyResult->fetch_assoc();
        $hashedPassword = $row['password'];

        // Verify the current password
        if (md5($currentPassword) === $hashedPassword) {
            // Update the password with the new one
            $newHashedPassword = md5($newPassword);
            $updateQuery = "UPDATE users SET password = ? WHERE user_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("si", $newHashedPassword, $userID);
            $updateStmt->execute();

            // Redirect to the profile page with a success message
            header("Location: index.php?password_changed=true");
            exit;
        } else {
            // Display an error message if the current password is incorrect
            $errorMessage = "Current password is incorrect.";
        }
    } else {
        // Handle error if user data cannot be retrieved
        // Redirect or display an error message
        header("Location: error.php");
        exit;
    }

    $verifyStmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <link rel="stylesheet" type="text/css" href="../styles.css">
    <style>
        /* Additional CSS styles for profile page */
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
            max-width: 600px;
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
<!-- Navigation -->
<?php include "navbar.php"; ?>

<div class="container">
    <h2>User Profile</h2>
    <p>Welcome, <?php echo $username; ?>!</p>

    <?php if (isset($_GET['password_changed']) && $_GET['password_changed'] === "true"): ?>
        <p class="success-message">Password changed successfully!</p>
    <?php endif; ?>

    <h3>Change Password</h3>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="form-group">
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" id="current_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" required>
        </div>
        <?php if (isset($errorMessage)): ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <div class="form-group">
            <button type="submit" name="change_password">Change Password</button>
        </div>
    </form>
</div>
</body>
</html>

