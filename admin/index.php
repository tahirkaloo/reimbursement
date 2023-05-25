<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to the login page
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Panel</title>
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="styles.css"> <!-- Additional admin panel styles -->
</head>
<body>
  <?php include 'admin_navbar.html'; ?>

  <div class="admin-container">
    <div class="admin-header">
      <h1 class="admin-title">Admin Panel</h1>
    </div>
    <div class="admin-content">
      <div class="admin-card" onclick="window.location.href='mileage_responses.php';">
        <div class="card-icon">
          <img src="https://reimbursement-instance-bucket.s3.amazonaws.com/hanover-banner-fuel" alt="Mileage Icon">
        </div>
        <div class="card-title">Mileage Responses</div>
      </div>
      <div class="admin-card" onclick="window.location.href='contact_responses.php';">
        <div class="card-icon">
          <img src="https://reimbursement-instance-bucket.s3.amazonaws.com/Admin+Contact+Us+index.png" alt="Contact Icon">
        </div>
        <div class="card-title">Contact Form Responses</div>
      </div>
      <!-- Add more cards for other response pages -->

      <div class="admin-metrics">
        <div class="admin-metric">
          <p class="admin-metric-label">Contact Form</p>
          <p class="admin-metric-value"><?php echo $totalContactResponses; ?></p>
        </div>
        <!-- Add more metrics for other response pages -->
      </div>
    </div>
  </div>
</body>
</html>

