<?php
// Retrieve the response ID from the form submission
$responseId = $_POST['response_id'];

// Update the status of the response in the database to "Manager Rejected"
$servername = "localhost";
$dbname = "mileage_reimbursement";
$username = "tahir";
$password = "11559933tk";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Update the status to "Manager Rejected"
  $stmt = $conn->prepare("UPDATE reimbursement_responses SET status = 'Manager Rejected' WHERE id = :response_id");
  $stmt->bindParam(':response_id', $responseId);
  $stmt->execute();

  // Redirect back to the mileage responses page after rejection
  header("Location: mileage_responses.php");
  exit;
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>

