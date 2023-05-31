<?php
// Retrieve the mileage responses from the database
$servername = "localhost";
$dbname = "mileage_reimbursement";
$username = "tahir";
$password = "11559933tk";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Retrieve the mileage responses and finance responses from the database
  $stmt = $conn->query("SELECT id, name, mileage, date, tolls_parking, origin, destination, multiple_stops, purpose, status FROM reimbursement_responses
                        UNION
                        SELECT id, name, mileage, date, tolls_parking, origin, destination, multiple_stops, purpose, 'Approved' AS status FROM finance_responses");
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
            <th>Status</th>
            <th>Action</th>
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
              <td><?php echo $response['status']; ?></td>
              <td>
                <?php if ($_SESSION['user_level'] === 'manager' && $response['status'] === 'Approved') : ?>
                  <!-- Show Finance button -->
                  <form method="POST" action="finance.php">
                    <input type="hidden" name="response_id" value="<?php echo $response['id']; ?>">
                    <button type="submit" class="btn btn-primary">Show Finance</button>
                  </form>
                <?php elseif ($_SESSION['user_level'] === 'manager' && $response['status'] === 'Manager Rejected') : ?>
                  <!-- Show Rejected for User button -->
                  <form method="POST" action="rejected_user.php">
                    <input type="hidden" name="response_id" value="<?php echo $response['id']; ?>">
                    <button type="submit" class="btn btn-secondary">Show Rejected for User</button>
                  </form>
                <?php endif; ?>
              </td>
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

