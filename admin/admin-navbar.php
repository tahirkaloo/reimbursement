<nav class="navbar navbar-expand-md navbar-dark bg-dark">
  <img src="https://reimbursement-instance-bucket.s3.amazonaws.com/hanover.jpg" alt="Logo" height="100">
  <a class="navbar-brand" href="#">Hanover County</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <?php
      session_start();

      // Check if the user is logged in
      if (isset($_SESSION['user_id'])) {
          // User is logged in
          ?>
          <li class="nav-item">
            <a class="nav-link" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="mileage_responses.php">Mileage Responses</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="contact_responses.php">Contact Form Responses</a>
          </li>
          <!-- Add more nav items for other pages in the admin panel -->
          <li class="nav-item">
            <a class="nav-link" href="../logout.php">Logout</a>
          </li>
          <?php
      } else {
          // User is not logged in
          header("Location: ../login.php"); // Redirect to the login page
          exit;
      }
      ?>
    </ul>
  </div>
</nav>

