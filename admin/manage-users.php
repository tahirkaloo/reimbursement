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

// Check if the form is submitted for making a user admin or removing admin access
if (isset($_POST['makeAdmin']) || isset($_POST['removeAdmin'])) {
    $userId = $_POST['userId'];
    $action = isset($_POST['makeAdmin']) ? 'makeAdmin' : 'removeAdmin';

    // Perform the necessary database operations to update the user's admin status
    // Replace the following code with your own logic
    $query = "UPDATE users SET role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    if ($action === 'makeAdmin') {
        $role = 'admin';
    } else {
        $role = 'user';
    }
    $stmt->bind_param("si", $role, $userId);
    $stmt->execute();
}

// Check if the form is submitted for resetting the user's password
if (isset($_POST['resetPassword'])) {
    $userId = $_POST['userId'];

    // Generate a new password
    $newPassword = generateRandomPassword(); // Implement your own logic to generate a new password

    // Perform the necessary database operations to update the user's password
    // Replace the following code with your own logic
    $query = "UPDATE users SET password = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
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
}

// Check if the form is submitted for deleting the user's account
if (isset($_POST['deleteAccount'])) {
    $userId = $_POST['userId'];

    // Perform the necessary database operations to delete the user's account
    // Replace the following code with your own logic
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

// Get all users from the database
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Helper function to generate a random password
function generateRandomPassword() {
    // Implement your own logic to generate a random password
    // This is just a simple example
    $length = 8;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

// Helper function to retrieve the user's email
function getUserEmail($userId) {
    // Implement your own logic to retrieve the user's email
    // Replace the following code with your own logic
    global $conn; // Added to access the global connection variable
    $query = "SELECT email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['email'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
</head>
<body>
    <?php if (!$isAdmin) : ?>
        <h1>Access Denied</h1>
        <p>You do not have permission to access this page.</p>
    <?php else : ?>
        <h1>Manage Users</h1>
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
                            <?php if ($user['role'] === 'user') : ?>
                                <button type="submit" name="makeAdmin">Make Admin</button>
                            <?php else : ?>
                                <button type="submit" name="removeAdmin">Remove Admin</button>
                            <?php endif; ?>
                            <button type="submit" name="resetPassword">Reset Password</button>
                            <button type="submit" name="deleteAccount">Delete Account</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>

