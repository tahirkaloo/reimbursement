<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to the login page
    exit;
}

// Simulated total response counts for demonstration
$totalMileageResponses = 10;
$totalContactResponses = 5;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Panel</title>
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="styles.css"> <!-- Additional admin panel styles -->
  <style>
    /* Customize admin panel styles */
    .admin-container {
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
      text-align: center;
    }

    .admin-header {
      margin-bottom: 30px;
    }

    .admin-title {
      font-size: 24px;
      font-weight: bold;
    }

    .admin-content {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
    }

    .admin-card {
      width: 200px;
      height: 200px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 20px;
      background-color: #f0f0f0;
      border-radius: 8px;
      cursor: pointer;
    }

    .admin-card:hover {
      background-color: #e0e0e0;
    }

    .card-icon img {
      max-width: 100px;
      max-height: 100px;
    }

    .card-title {
      margin-top: 10px;
      font-size: 16px;
      font-weight: bold;
    }

    .admin-metrics {
      margin-top: 30px;
      display: flex;
      justify-content: center;
      gap: 30px;
    }

    .admin-metric {
      padding: 10px 20px;
      background-color: #f0f0f0;
      border-radius: 8px;
    }

    .admin-metric-label {
      font-size: 14px;
      font-weight: bold;
    }

    .admin-metric-value {
      font-size: 24px;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <?php include 'admin-navbar.php'; ?>

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
    </div>
    <div class="admin-metrics">
      <div class="admin-metric">
        <p class="admin-metric-label">Mileage Responses</p>
        <p class="admin-metric-value"><?php echo $totalMileageResponses; ?></p>
      </div>
      <div class="admin-metric">
        <p class="admin-metric-label">Contact Form Responses</p>
        <p class="admin-metric-value"><?php echo $totalContactResponses; ?></p>
      </div>
      <!-- Add more metrics for other response pages -->
    </div>
  </div>
</body>
</html>

