document.addEventListener("DOMContentLoaded", function () {
  var viewButtons = document.querySelectorAll(".view-button");
  viewButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      var userId = this.getAttribute("data-user-id");
      console.log(userId);
    });
  });
});

function displayUserDetails(userDetails) {
  // Create the modal HTML structure
  var modal = document.createElement("div");
  modal.classList.add("modal");

  var modalContent = document.createElement("div");
  modalContent.classList.add("modal-content");

  var closeButton = document.createElement("span");
  closeButton.classList.add("close");
  closeButton.innerHTML = "&times;";
  closeButton.addEventListener("click", function () {
    modal.style.display = "none";
  });

  var userDetailsElement = document.createElement("div");
  userDetailsElement.innerHTML =
    "<h2>User Details</h2>" +
    "<p><strong>ID:</strong> " +
    userDetails.ID +
    "</p>" +
    "<p><strong>Username:</strong> " +
    userDetails.username +
    "</p>" +
    "<p><strong>Email:</strong> " +
    userDetails.email +
    "</p>";

  modalContent.appendChild(closeButton);
  modalContent.appendChild(userDetailsElement);
  modal.appendChild(modalContent);
  document.body.appendChild(modal);

  // Display the modal
  modal.style.display = "block";
}

// Delete form function
jQuery(document).ready(function ($) {
  // Delete button click event handler
  $(".delete-button").on("click", function () {
    var pageId = $(this).data("page-id");

    // Confirm the deletion
    var confirmDelete = confirm("Are you sure you want to delete this page?");

    if (confirmDelete) {
      // Perform AJAX request to delete the page
      $.ajax({
        url: ajaxurl, // ajaxurl is automatically defined by WordPress
        type: "POST",
        data: {
          action: "delete_page",
          page_id: pageId,
        },
        success: function (response) {
          console.log(response);
          // On success, remove the deleted page row from the table
          if (response.success) {
            $('tr[data-page-id="' + pageId + '"]').remove();
            alert("Page deleted successfully!");
          } else {
            alert("Error deleting page.");
          }
        },
        error: function () {
          alert("Error deleting page.");
        },
      });
    }
  });
});
