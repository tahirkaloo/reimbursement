<?php
session_start();
require_once 'db_connect.php';

// Do reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.html"); // Redirect to the home page or any other desired page
    exit;
}

$error = false;
$errorMessage = '';

// Check if the login form is submitted
if (isset($_POST['login'])) {
    // Get the form inputs
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validate the form inputs
    if (empty($email) || empty($password)) {
        $error = true;
        $errorMessage = "Email and password are required.";
    } else {
        // Prepare and execute the SQL statement
        $stmt = mysqli_prepare($conn, "SELECT user_id, email, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $userId, $fetchedEmail, $hashedPassword);
            mysqli_stmt_fetch($stmt);

            // Verify the password
            if (md5($password) === $hashedPassword) {
                $_SESSION['user_id'] = $userId;
                header("Location: index.html"); // Redirect to the home page or any other desired page
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
    <title>Login Test</title>
</head>
<body>
    <h2>Login Test</h2>

    <?php if ($error): ?>
        <p><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit" name="login">Login</button>
    </form>
</body>
</html>

