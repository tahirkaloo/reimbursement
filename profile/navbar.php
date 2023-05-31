<?php
// Simulating user data
$user = [
  'name' => 'John Doe',
  'email' => 'johndoe@example.com'
];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Update profile details
  if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Validate and update the profile details in the database
    // Your implementation here

    // Simulating profile update success
    $user['name'] = $name;
    $user['email'] = $email;
  }

  // Change password
  if (isset($_POST['change_password'])) {
    $newPassword = $_POST['new_password'];

    // Validate and update the password in the database
    // Your implementation here

    // Simulating password change success
    $message = "Password changed successfully!";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Profile Page</title>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .profile-page {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #f4f4f4;
    }

    .profile-card {
      background-color: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      padding: 40px;
      width: 600px;
      max-width: 90%;
    }

    .profile-tabs {
      display: flex;
      margin-bottom: 20px;
    }

    .profile-tab {
      flex-grow: 1;
      text-align: center;
      padding: 10px;
      background-color: #f4f4f4;
      border-radius: 8px 8px 0 0;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .profile-tab.active {
      background-color: #fff;
    }

    .profile-content {
      display: none;
      padding: 20px;
      background-color: #fff;
      border-radius: 0 8px 8px 8px;
    }

    .profile-content.active {
      display: block;
    }
  </style>
</head>
<body>
  <div class="profile-page">
    <div class="profile-card">
      <h1>Profile</h1>

      <div class="profile-tabs">
        <div class="profile-tab active" data-tab="profile">Profile</div>
        <div class="profile-tab" data-tab="password">Change Password</div>
      </div>

      <div class="profile-content active" id="profile">
        <h2>Profile Details</h2>
        <form method="POST" action="">
          <label for="name">Name:</label>
          <input type="text" name="name" id="name" value="<?php echo $user['name']; ?>"><br><br>
          <label for="email">Email:</label>
          <input type="email" name="email" id="email" value="<?php echo $user['email']; ?>"><br><br>
          <button type="submit" name="update_profile">Update Profile</button>
        </form>
      </div>

      <div class="profile-content" id="password">
        <h2>Change Password</h2>
        <form method="POST" action="">
          <label for="new_password">New Password:</label>
          <input type="password" name="new_password" id="new_password"><br><br>
          <button type="submit" name="change_password">Change Password</button>
        </form>
        <?php if (isset($message)) : ?>
          <p><?php echo $message; ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    const tabs = document.querySelectorAll('.profile-tab');
    const contents = document.querySelectorAll('.profile-content');

    tabs.forEach(tab => {
      tab.addEventListener('click', (e) => {
        e.preventDefault();

        const target = tab.getAttribute('data-tab');
        showContent(target);
        activateTab(tab);
      });
    });

    function showContent(target) {
      contents.forEach(content => {
        if (content.id === target) {
          content.classList.add('active');
        } else {
          content.classList.remove('active');
        }
      });
    }

    function activateTab(tab) {
      tabs.forEach(t => {
        if (t === tab) {
          t.classList.add('active');
        } else {
          t.classList.remove('active');
        }
      });
    }
  </script>
</body>
</html>

