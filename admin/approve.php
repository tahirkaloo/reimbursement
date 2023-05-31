<?php
// Retrieve the response ID from the form submission
$responseId = $_POST['response_id'];

// Update the status of the response in the database to "Approved"
$servername = "localhost";
$dbname = "mileage_reimbursement";
$username = "tahir";
$password = "11559933tk";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Retrieve the response details
  $stmt = $conn->prepare("SELECT * FROM reimbursement_responses WHERE id = :response_id");
  $stmt->bindParam(':response_id', $responseId);
  $stmt->execute();
  $response = $stmt->fetch(PDO::FETCH_ASSOC);

  // Insert the response into the finance_responses table
  $stmt = $conn->prepare("INSERT INTO finance_responses (name, mileage, date, tolls_parking, origin, destination, multiple_stops, purpose) 
                          VALUES (:name, :mileage, :date, :tolls_parking, :origin, :destination, :multiple_stops, :purpose)");
  $stmt->bindParam(':name', $response['name']);
  $stmt->bindParam(':mileage', $response['mileage']);
  $stmt->bindParam(':date', $response['date']);
  $stmt->bindParam(':tolls_parking', $response['tolls_parking']);
  $stmt->bindParam(':origin', $response['origin']);
  $stmt->bindParam(':destination', $response['destination']);
  $stmt->bindParam(':multiple_stops', $response['multiple_stops']);
  $stmt->bindParam(':purpose', $response['purpose']);
  $stmt->execute();

  // Update the status to "Approved"
  $stmt = $conn->prepare("UPDATE reimbursement_responses SET status = 'Approved' WHERE id = :response_id");
  $stmt->bindParam(':response_id', $responseId);
  $stmt->execute();

  // Redirect back to the mileage responses page after approval
  header("Location: mileage_responses.php");
  exit;
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>

