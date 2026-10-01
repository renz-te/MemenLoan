<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
  header("Location: index.php");
  exit();
}

$email = $_SESSION['email'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$row = mysqli_fetch_assoc($query);

$firstName = $row['firstName'];
$lastName = $row['lastName'];
$profile = $row['profile'] ?: 'default-avatar.png'; // Default avatar if none
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Poppins', sans-serif;
    }
    .profile-card {
      max-width: 600px;
      margin: 60px auto;
      padding: 30px;
      border-radius: 20px;
      background: #fff;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      text-align: center;
    }
    .profile-card img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 20px;
    }
    .profile-card h3 {
      font-weight: 600;
      margin-bottom: 5px;
    }
    .profile-card p {
      color: #777;
      margin-bottom: 15px;
    }
    .btn-edit {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 30px;
      transition: 0.3s;
    }
    .btn-edit:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

  <div class="profile-card">
    <img src="<?php echo htmlspecialchars($profile); ?>" alt="Profile Picture">
    <h3><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></h3>
    <p><?php echo htmlspecialchars($email); ?></p>
    <button id="editBtn" class="btn-edit">Edit Profile</button>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const editBtn = document.getElementById("editBtn");

    editBtn.addEventListener("click", async () => {
      const { value: formValues } = await Swal.fire({
        title: "Edit Personal Information",
        html: `
          <form id="editProfileForm" enctype="multipart/form-data">
            <label>First Name</label>
            <input id="swalFirstName" name="firstName" class="swal2-input" value="<?php echo htmlspecialchars($firstName); ?>" required>

            <label>Last Name</label>
            <input id="swalLastName" name="lastName" class="swal2-input" value="<?php echo htmlspecialchars($lastName); ?>" required>

            <label>Profile Picture</label>
            <input type="file" id="swalProfile" name="profile" class="swal2-file" accept="image/*">

            <label>New Password (optional)</label>
            <input type="password" id="swalPassword" name="password" class="swal2-input" placeholder="Leave blank to keep current password">

            <img id="swalPreview" src="<?php echo htmlspecialchars($profile); ?>" alt="Preview" style="max-width:100px; border-radius:50%; margin-top:10px;">
          </form>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: "Save Changes",
        cancelButtonText: "Cancel",
        preConfirm: () => {
          return {
            firstName: document.getElementById("swalFirstName").value,
            lastName: document.getElementById("swalLastName").value,
            password: document.getElementById("swalPassword").value,
            profile: document.getElementById("swalProfile").files[0] || null
          };
        }
      });

      if (formValues) {
        const fd = new FormData();
        fd.append("firstName", formValues.firstName);
        fd.append("lastName", formValues.lastName);
        if (formValues.password) fd.append("password", formValues.password);
        if (formValues.profile) fd.append("profile", formValues.profile);

        try {
          const response = await fetch("update_profile.php", {
            method: "POST",
            body: fd
          });
          const result = await response.json();

          if (result.success) {
            Swal.fire({
              icon: "success",
              title: "Profile Updated!",
              text: "Your information has been successfully updated.",
              confirmButtonColor: "#3085d6"
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Update Failed",
              text: result.message || "Something went wrong."
            });
          }
        } catch (error) {
          Swal.fire({
            icon: "error",
            title: "Network Error",
            text: "Unable to connect to the server."
          });
        }
      }
    });

    // Image preview live inside SweetAlert
    document.addEventListener("change", e => {
      if (e.target.id === "swalProfile") {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = ev => {
            document.getElementById("swalPreview").src = ev.target.result;
          };
          reader.readAsDataURL(file);
        }
      }
    });
  });
  </script>

</body>
</html>
