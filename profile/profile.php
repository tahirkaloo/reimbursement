<?php
// Include the database connection file
require_once('../db_connect.php');

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  // Redirect to login page if not logged in
  header("Location: login.php");
  exit;
}

// Fetch user profile details from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  $userData = mysqli_fetch_assoc($result);
  $user = [
    'name' => $userData['username'],
    'email' => $userData['email']
  ];
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Update profile details
  if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Update the profile details in the database
    $sql = "UPDATE users SET username = '$name', email = '$email' WHERE user_id = $user_id";

    if (mysqli_query($conn, $sql)) {
      // Update the user data
      $user['name'] = $name;
      $user['email'] = $email;

      // Show success message
      $message = "Profile updated successfully!";
    } else {
      echo "Profile update failed: " . mysqli_error($conn);
    }
  }

  // Change password
  if (isset($_POST['change_password'])) {
    $newPassword = $_POST['new_password'];

    // Update the password in the database
    $sql = "UPDATE users SET password = '$newPassword' WHERE user_id = $user_id";

    if (mysqli_query($conn, $sql)) {
      // Show success message
      $message = "Password changed successfully!";
    } else {
      echo "Password change failed: " . mysqli_error($conn);
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Profile</title>
  <link rel="stylesheet" type="text/css" href="../styles.css">
  <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <?php include '../navbar.html'; ?>
  <div class="container">
    <h1>Profile</h1>
    
    <?php if (isset($message)) : ?>
      <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <ul class="nav nav-tabs">
      <li class="nav-item">
        <a class="nav-link active" href="#profile" data-toggle="tab">Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#change_password" data-toggle="tab">Change Password</a>
      </li>
    </ul>

    <div class="tab-content mt-3">
      <div id="profile" class="tab-pane active">
        <form method="POST" action="#">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
          </div>
          <button type="submit" class="btn btn-primary" name="update_profile">Update Profile</button>
        </form>
      </div>

      <div id="change_password" class="tab-pane">
        <form method="POST" action="#">
          <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
          </div>
          <button type="submit" class="btn btn-primary" name="change_password">Change Password</button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

