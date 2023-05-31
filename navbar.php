<?php
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
  <img src="https://reimbursement-instance-bucket.s3.amazonaws.com/hanover.jpg" alt="Logo" height="100">
  <a class="navbar-brand" href="#">Hanover County</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item <?php if(!$isLoggedIn) echo 'active'; ?>">
        <a class="nav-link" href="index.html">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="local-mileage-reimbursement.php">Local Mileage Reimbursement</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="about.html">About</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="contact.html">Contact</a>
      </li>
      <?php if ($isLoggedIn) { ?>
        <li class="nav-item">
          <a class="nav-link" href="admin">Admin</a>
	</li>
	<li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
      <?php } else { ?>
        <li class="nav-item">
          <a class="nav-link" href="login.php">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="register.php">Register</a>
        </li>
      <?php } ?>
    </ul>
  </div>
</nav>

