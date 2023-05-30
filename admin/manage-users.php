<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection file
require_once "../db_connect.php";

// Check if the user is not logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Check if the user is an admin
// Replace the condition below with your own logic to determine if the user is an admin
$isAdmin = ($_SESSION['role'] === 'admin');

// Check if the form is submitted for managing user actions
if (isset($_POST['manageUser'])) {
    $userId = $_POST['userId'];
    $action = $_POST['manageUserAction'];

    if ($action === 'deleteUser') {
        // Perform the necessary database operations to delete the user's account
        // Replace the following code with your own logic
        $query = "DELETE FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    } elseif ($action === 'resetPassword') {
        // Generate a new password
        $newPassword = generateRandomPassword(); // Implement your own logic to generate a new password

        // Perform the necessary database operations to update the user's password
        // Replace the following code with your own logic
        $query = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $hashedPassword = md5($newPassword); // Hash the new password using md5
        $stmt->bind_param("si", $hashedPassword, $userId);
        $stmt->execute();

        // Send the new password to the user via email or any other method
        // Replace the following code with your own logic
        $email = getUserEmail($userId); // Implement your own logic to retrieve the user's email
        if ($email) {
            $subject = "Password Reset";
            $message = "Your new password: $newPassword";
            // Send the email using your preferred email sending method
            // Replace the following code with your own logic
            mail($email, $subject, $message);
        }
    } elseif (in_array($action, ['admin', 'manager', 'finance'])) {
        // Perform the necessary database operations to update the user's role
        // Replace the following code with your own logic
        $query = "UPDATE users SET role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $action, $userId);
        $stmt->execute();
    }
}

// Get all users from the database
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" type="text/css" href="../styles.css">
    <style>
        /* Additional CSS styles for manage-users page */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            display: inline-block;
        }

        .btn-make-admin,
        .btn-make-manager,
        .btn-make-finance,
        .btn-reset-password,
        .btn-delete-account {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-remove-manager,
        .btn-remove-finance {
            background-color: #f44336;
        }

        .btn-make-admin:hover,
        .btn-make-manager:hover,
        .btn-make-finance:hover,
        .btn-reset-password:hover,
        .btn-delete-account:hover,
        .btn-remove-manager:hover,
        .btn-remove-finance:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <?php include 'admin-navbar.php'; ?>

    <div class="container">
        <h1>Manage Users</h1>
        <?php if (!$isAdmin) : ?>
            <h2>Access Denied</h2>
            <p>You do not have permission to access this page.</p>
        <?php else : ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php echo $user['user_id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="userId" value="<?php echo $user['user_id']; ?>">
                                <select name="manageUserAction">
                                    <option value="admin">Admin</option>
                                    <option value="manager">Manager</option>
                                    <option value="finance">Finance</option>
                                </select>
                                <button class="btn-make-admin" type="submit" name="manageUser">Make Role</button>
                                <button class="btn-reset-password" type="submit" name="manageUserAction" value="resetPassword">Reset Password</button>
                                <button class="btn-delete-account" type="submit" name="manageUserAction" value="deleteUser">Delete Account</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>

