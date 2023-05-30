<?php
// Connect to the database and retrieve the contact responses
$servername = "localhost";
$username = "tahir";
$password = "11559933tk";
$dbname = "contact_form_db";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $conn->prepare("SELECT * FROM contact_responses");
  $stmt->execute();
  $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Contact Responses</title>
  <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
  <?php include 'admin-navbar.php'; ?>
  <div class="admin-container">
    <div class="admin-header">
      <h1>Contact Responses</h1>
    </div>
    <div class="admin-content">
      <?php foreach ($responses as $response): ?>
        <div class="response-card">
          <div class="response-details">
            <p><strong>Name:</strong> <?php echo $response['name']; ?></p>
            <p><strong>Email:</strong> <?php echo $response['email']; ?></p>
            <p><strong>Message:</strong> <?php echo $response['message']; ?></p>
            <!-- Add more fields as necessary -->
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>

