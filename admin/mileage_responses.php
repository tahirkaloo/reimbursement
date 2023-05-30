<?php
// Retrieve the mileage responses from the database
$servername = "localhost";
$dbname = "mileage_reimbursement";
$username = "tahir";
$password = "11559933tk";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Retrieve the mileage responses from the database
  $stmt = $conn->query("SELECT * FROM reimbursement_responses");
  $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Mileage Responses</title>
  <link rel="stylesheet" type="text/css" href="styles.css">
  <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <?php include 'admin-navbar.php'; ?>
  <div class="container">
    <h1>Mileage Responses</h1>

    <?php if (isset($responses) && !empty($responses)) : ?>
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Mileage</th>
            <th>Date</th>
            <th>Tolls/Parking</th>
            <th>Origin</th>
            <th>Destination</th>
            <th>Multiple Stops</th>
            <th>Purpose</th>
            <?php if ($_SESSION['user_level'] === 'manager') : ?>
              <th>Action</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($responses as $response) : ?>
            <tr>
              <td><?php echo $response['name']; ?></td>
              <td><?php echo $response['mileage']; ?></td>
              <td><?php echo $response['date']; ?></td>
              <td><?php echo $response['tolls_parking']; ?></td>
              <td><?php echo $response['origin']; ?></td>
              <td><?php echo $response['destination']; ?></td>
              <td><?php echo $response['multiple_stops']; ?></td>
              <td><?php echo $response['purpose']; ?></td>
              <?php if ($_SESSION['user_level'] === 'manager') : ?>
                <td>
                  <!-- Approve button -->
                  <form method="POST" action="approve.php">
                    <input type="hidden" name="response_id" value="<?php echo $response['id']; ?>">
                    <button type="submit" class="btn btn-success">Approve</button>
                  </form>
                  <!-- Reject button -->
                  <form method="POST" action="reject.php">
                    <input type="hidden" name="response_id" value="<?php echo $response['id']; ?>">
                    <button type="submit" class="btn btn-danger">Reject</button>
                  </form>
                </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else : ?>
      <p>No mileage responses found.</p>
    <?php endif; ?>
  </div>
</body>
</html>

