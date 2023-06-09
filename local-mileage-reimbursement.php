<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to the login page
    exit;
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $name = $_POST['name'];
    $date = $_POST['date'];
    $tollsParking = $_POST['tolls-parking'];
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $multipleStops = isset($_POST['multiple-stops']) ? $_POST['multiple-stops'] : [];
    $mileage = $_POST['mileage'];
    $purpose = $_POST['purpose'];

    // Connect to the database
    require_once('db_connect.php');

    // Prepare the SQL statement
    $stmt = $mysqli->prepare("INSERT INTO mileage_reimbursement (user_id, name, date, tolls_parking, origin, destination, multiple_stops, mileage, purpose, manager_approval, manager_id, finance_approval, finance_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NULL, 'Pending', NULL)");

    // Bind parameters and execute the statement for each row
    foreach ($date as $index => $value) {
        $stmt->bind_param("sssssssss", $_SESSION['user_id'], $name, $value, $tollsParking[$index], $origin[$index], $destination[$index], $multipleStops[$index], $mileage[$index], $purpose[$index]);
        $stmt->execute();
    }

    // Close the statement and database connection
    $stmt->close();
    $mysqli->close();

    // Redirect to the confirmation page
    header("Location: confirmation.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Local Mileage Reimbursement</title>
  <link rel="stylesheet" type="text/css" href="reimbursement.css">
  <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="reimbursement.js"></script>
</head>
<body>
  <!-- Include the navbar -->
  <?php include 'navbar.php'; ?>
  <style>
    .navbar-nav.ml-auto {
      justify-content: flex-end;
    }
  </style>

  <div class="container">
    <div class="form-header">
      <!-- Add any necessary header content here -->
    </div>

    <form id="reimbursement-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
      <!-- Form fields and table code here -->
      <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" id="name" name="name" required>
      </div>

      <table id="mileageTable" class="table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Tolls/Parking</th>
            <th>Origin</th>
            <th>Destination</th>
            <th>Multiple Stops</th>
            <th>Mileage</th>
            <th>Purpose</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input type="date" name="date[]" value="mm-dd-yyyy" required=""></td>
            <td><input type="number" name="tolls-parking[]" min="0" step="any"></td>
            <td><input type="text" name="origin[]"></td>
            <td><input type="text" name="destination[]"></td>
            <td><input type="checkbox" name="multiple-stops[]" value="yes"></td>
            <td><input type="number" name="mileage[]" min="0" step="any" required=""></td>
            <td><input type="text" name="purpose[]"></td>
            <td><button type="button" class="btn btn-primary remove-row">Remove</button></td>
          </tr>
        </tbody>
        <tfoot>
          <!-- Footer rows here -->
          <tr>
            <th colspan="5">Total mileage:</th>
            <th id="total-mileage">0.00</th>
            <th colspan="2"></th>
          </tr>
          <tr>
            <th colspan="5">IRS reimbursement rate:</th>
            <th>0.655</th>
            <th colspan="2">IRS Standard Mileage Rates</th>
          </tr>
          <tr>
            <th colspan="5">Mileage reimbursement:</th>
            <th id="mileage-reimbursement">0.00</th>
            <th colspan="2"></th>
          </tr>
          <tr>
            <th colspan="5">Supporting documentation:</th>
            <th colspan="3"><input type="file" name="supporting-docs"></th>
          </tr>
          <tr>
            <th colspan="5">Total reimbursement:</th>
            <th id="total-reimbursement">0.00</th>
            <th colspan="2"></th>
          </tr>
        </tfoot>
      </table>

      <button id="add-row" type="button" class="btn btn-success">Add Row</button>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>
</body>
</html>

