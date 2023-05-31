document.addEventListener("DOMContentLoaded", function() {
  var tabs = document.querySelectorAll(".tabs li");
  var tabContents = document.querySelectorAll(".tab-content");

  // Add click event listeners to tabs
  tabs.forEach(function(tab) {
    tab.addEventListener("click", function() {
      var activeTab = this.dataset.tab;

      // Remove "active" class from all tabs and tab contents
      tabs.forEach(function(tab) {
        tab.classList.remove("active");
      });

      tabContents.forEach(function(content) {
        content.classList.remove("active");
      });

      // Add "active" class to the clicked tab and corresponding tab content
      this.classList.add("active");
      document.getElementById(activeTab).classList.add("active");
    });
  });

  // Get profile details from the server and populate the fields
  var profileDetails = {
    name: "John Doe",
    email: "johndoe@example.com"
  };

  document.getElementById("name").textContent = profileDetails.name;
  document.getElementById("email").textContent = profileDetails.email;

  // Handle form submission for changing password
  var changePasswordForm = document.getElementById("change-password-form");
  changePasswordForm.addEventListener("submit", function(event) {
    event.preventDefault();
    var currentPassword = document.getElementById("current-password").value;
    var newPassword = document.getElementById("new-password").value;

    // Send a request to the server to change the password
    // You can use AJAX or fetch API for this

    // Clear the input fields
    changePasswordForm.reset();
  });

  // Handle form submission for updating name
  var updateNameForm = document.getElementById("update-name-form");
  updateNameForm.addEventListener("submit", function(event) {
    event.preventDefault();
    var newName = document.getElementById("name-input").value;

    // Send a request to the server to update the name
    // You can use AJAX or fetch API for this

    // Update the name in the profile details section
    document.getElementById("name").textContent = newName;

    // Clear the input field
    updateNameForm.reset();
  });

  // Handle form submission for updating email
  var updateEmailForm = document.getElementById("update-email-form");
  updateEmailForm.addEventListener("submit", function(event) {
    event.preventDefault();
    var newEmail = document.getElementById("email-input").value;

    // Send a request to the server to update the email
    // You can use AJAX or fetch API for this

    // Update the email in the profile details section
    document.getElementById("email").textContent = newEmail;

    // Clear the input field
    updateEmailForm.reset();
  });
});

