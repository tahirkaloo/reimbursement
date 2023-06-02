<?php
session_start();
require_once '../db_connect.php';

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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        /* Additional CSS styles for profile page */
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

        .profile-info {
            margin-bottom: 20px;
        }

        .profile-info label {
            font-weight: bold;
        }

        .profile-info span {
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Profile</h2>
    <div class="profile-info">
        <label>Username:</label>
        <span><?php echo $username; ?></span>
    </div>
    <div class="profile-info">
        <label>Email:</label>
        <span><?php echo $email; ?></span>
    </div>
    <div class="profile-info">
        <label>Role:</label>
        <span><?php echo $role; ?></span>
    </div>
</div>
</body>
</html>

