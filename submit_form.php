<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Determine which form was submitted based on the presence of specific form fields
  if (isset($_POST["mileage"])) {
    handleMileageReimbursementForm();
  } elseif (isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["message"])) {
    handleContactForm();
  } else {
    echo "Error: Unknown form submitted.";
  }
} else {
  // If the form is not submitted, return an error
  echo "Error: Form not submitted.";
}

// Handle local mileage reimbursement form
function handleMileageReimbursementForm() {
  // Get the form data
  $name = $_POST["name"];
  $mileages = $_POST["mileage"];
  $dates = $_POST["date"];
  $tollsParkings = $_POST["tolls-parking"];
  $origins = $_POST["origin"];
  $destinations = $_POST["destination"];
  $multipleStops = $_POST["multiple-stops"];
  $purposes = $_POST["purpose"];

  // Store the form data in the mileage_reimbursement database
  $servername = "localhost";
  $dbname = "mileage_reimbursement";
  $username = "tahir";
  $password = "11559933tk";

  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Loop through the form data and insert each response into the database
    for ($i = 0; $i < count($mileages); $i++) {
      $mileage = $mileages[$i];
      $date = $dates[$i];
      $tollsParking = $tollsParkings[$i];
      $origin = $origins[$i];
      $destination = $destinations[$i];
      $multipleStop = isset($multipleStops[$i]) ? $multipleStops[$i] : "";
      $purpose = $purposes[$i];

      // Insert the form data into the database
      $stmt = $conn->prepare("INSERT INTO reimbursement_responses (name, mileage, date, tolls_parking, origin, destination, multiple_stops, purpose) VALUES (:name, :mileage, :date, :tolls_parking, :origin, :destination, :multiple_stops, :purpose)");
      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':mileage', $mileage);
      $stmt->bindParam(':date', $date);
      $stmt->bindParam(':tolls_parking', $tollsParking);
      $stmt->bindParam(':origin', $origin);
      $stmt->bindParam(':destination', $destination);
      $stmt->bindParam(':multiple_stops', $multipleStop);
      $stmt->bindParam(':purpose', $purpose);
      $stmt->execute();
    }

    // Return a success message or redirect to a thank you page
    echo "Mileage reimbursement form submitted successfully!";
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}

// Handle contact form
function handleContactForm() {
  // Get the form data
  $name = $_POST["name"];
  $email = $_POST["email"];
  $message = $_POST["message"];
  // ... Get other form fields

  // Perform any additional processing or validation of the form data
  // ...

  // Store the form data in a database (contact_form_db)
  $servername = "localhost";
  $dbname = "contact_form_db";
  $username = "tahir";
  $password = "11559933tk";

  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert the form data into the database
    $stmt = $conn->prepare("INSERT INTO contact_responses (name, email, message) VALUES (:name, :email, :message)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':message', $message);
    $stmt->execute();

    // Return a success message or redirect to a thank you page
    echo "Contact form submitted successfully!";
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}
?>

