<?php
session_start();
include("connect.php");
if (isset($_SESSION['email'])) {
  header("Location: homepage.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Form</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="icon" href="imgs/logo.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="endex1.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <form method="post" action="register.php" onsubmit="return validateForm();">
    <div class="login-container">
      <h1>Register</h1>
      <?php
      if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
        $firstName = $_POST['firstName'];
        $lastName  = $_POST['lastName'];
        $email     = $_POST['email'];
        $phone     = $_POST['phone'];
        $password  = $_POST['password'];
        $confirm   = $_POST['confirm'];

        // 1️⃣ Check if passwords match
        if ($password !== $confirm) {
          echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Passwords do not match!',
                    confirmButtonColor: '#3085d6'
                });
            });
        </script>";
        } else {
          // 2️⃣ Check if email already exists
          $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
          $checkEmail->bind_param("s", $email);
          $checkEmail->execute();
          $checkEmail->store_result();

          if ($checkEmail->num_rows > 0) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Email already used',
                        text: 'That email is already registered. Please use another one.',
                        confirmButtonColor: '#3085d6'
                    });
                });
            </script>";
          } else {
            // 3️⃣ Check if phone number already exists
            $checkPhone = $conn->prepare("SELECT id FROM users WHERE phone = ? LIMIT 1");
            $checkPhone->bind_param("s", $phone);
            $checkPhone->execute();
            $checkPhone->store_result();

            if ($checkPhone->num_rows > 0) {
              echo "<script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Phone number already used',
                            text: 'That phone number is already registered. Please try another one.',
                            confirmButtonColor: '#3085d6'
                        });
                    });
                </script>";
            } else {
              // 4️⃣ Hash the password before inserting
              $hashed_password = password_hash($password, PASSWORD_DEFAULT);

              // 5️⃣ Insert into database with the hashed password
              $insert = $conn->prepare("INSERT INTO users (firstName, lastName, email, phone, password) VALUES (?, ?, ?, ?, ?)");
              $insert->bind_param("sssss", $firstName, $lastName, $email, $phone, $hashed_password);

              if ($insert->execute()) {
                echo "<script>
                        document.addEventListener('DOMContentLoaded', () => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Registered successfully!',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                window.location.href = 'login.php';
                            });
                        });
                    </script>";
              } else {
                echo "<script>
                        document.addEventListener('DOMContentLoaded', () => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong. Please try again.',
                                confirmButtonColor: '#3085d6'
                            });
                        });
                    </script>";
              }
              $insert->close();
            }
            $checkPhone->close();
          }
          $checkEmail->close();
        }
      }
      ?>

      <div class="input-group inp-flex">
        <div class="name-input">
          <input type="text" name="firstName" placeholder="First Name" required>
        </div>
        <div class="name-input">
          <input type="text" name="lastName" placeholder="Last Name" required>
        </div>
      </div>
      <div class="input-group">
        <input type="email" name="email" placeholder="Email" required>
        <i class="fa-regular fa-envelope icon"></i>
      </div>
      <!-- Phone -->
      <div class="input-group inp-flex">
        <select id="countryCode" class="code-select">
          <option value="+63">🇵🇭 +63</option>
          <option value="+1">🇺🇸 +1</option>
          <option value="+44">🇬🇧 +44</option>
          <option value="+91">🇮🇳 +91</option>
          <option value="+81">🇯🇵 +81</option>
          <option value="+61">🇦🇺 +61</option>
          <option value="+49">🇩🇪 +49</option>
          <option value="+33">🇫🇷 +33</option>
          <option value="+971">🇦🇪 +971</option>
          <option value="+86">🇨🇳 +86</option>
        </select>
        <input type="tel" id="phoneNumber" placeholder="Phone number" required>
        <input type="hidden" name="phone" id="fullPhone">
        <i class="fa-solid fa-phone icon"></i>
      </div>
      <div class="input-group">
        <div class="name-input">
          <input type="password" name="password" id="password" placeholder="Password" required minlength="8" oninput="checkStrength(); checkConfirm();">
          <i class="fa-solid fa-lock icon"></i>
          <i class="fa-solid fa-eye icon" id="togglePassword" onclick="togglePass('password','togglePassword')" style="position:absolute; right:40px; top:50%; transform:translateY(-50%); cursor:pointer;"></i>
          <small id="strengthMessage" style="display:block; margin-top:5px; font-size:12px;"></small>
        </div>
      </div>

      <div class="input-group">
        <div class="name-input">
          <input type="password" name="confirm" id="confirm" placeholder="Confirm Password" required oninput="checkConfirm()">
          <i class="fa-solid fa-lock icon"></i>
          <i class="fa-solid fa-eye icon" id="toggleConfirm" onclick="togglePass('confirm','toggleConfirm')" style="position:absolute; right:40px; top:50%; transform:translateY(-50%); cursor:pointer;"></i>
          <small id="confirmMessage" style="display:block; margin-top:5px; font-size:12px;"></small>
        </div>
      </div>
      <input type="submit" class="btn-login" value="Register" name="register" id="registerBtn">
  </form>
  <div class="register-link">
    Already have an account? <a href="login.php">Login</a>
  </div>
  </div>
  <script>
    // Phone Data
    const phoneInput = document.getElementById("phoneNumber");
    const countryCode = document.getElementById("countryCode");
    const fullPhone = document.getElementById("fullPhone");
    // Phone formats by country
    const formats = {
      "+63": [3, 3, 4], // 🇵🇭 PH: 923 456 7890
      "+1": [3, 3, 4], // 🇺🇸 US: 123 456 7890
      "+44": [4, 3, 4], // 🇬🇧 UK: 1234 567 8901
      "+91": [5, 5], // 🇮🇳 IN: 12345 67890
      "+81": [3, 4, 4], // 🇯🇵 JP: 090 1234 5678
      "+61": [4, 3, 3], // 🇦🇺 AU: 0412 345 678
      "+49": [3, 3, 4], // 🇩🇪 DE: 151 234 5678
      "+33": [2, 2, 2, 2, 2], // 🇫🇷 FR: 01 23 45 67 89
      "+971": [3, 3, 4], // 🇦🇪 AE: 050 123 4567
      "+86": [3, 4, 4], // 🇨🇳 CN: 138 1234 5678
    };
    // Function to format based on country
    function formatPhone(value, code) {
      let nums = value.replace(/\D/g, ""); // only numbers
      let groups = formats[code] || [3, 3, 4]; // fallback format
      let formatted = "";
      let i = 0;
      for (let g of groups) {
        if (nums.length > i) {
          if (formatted !== "") formatted += " ";
          formatted += nums.substring(i, i + g);
        }
        i += g;
      }
      return formatted;
    }
    // Format while typing
    phoneInput.addEventListener("input", function() {
      let code = countryCode.value;
      phoneInput.value = formatPhone(phoneInput.value, code);
    });
    // Combine with country code on submit
    function combinePhone() {
      fullPhone.value = countryCode.value + phoneInput.value.replace(/\s+/g, "");
    }
    // Show/Hide Password
    function togglePass(id, iconId) {
      let input = document.getElementById(id);
      let icon = document.getElementById(iconId);
      if (input.type === "password") {
        input.type = "text";
        icon.classList.add("fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
      }
    }
    // Password Strength

    // Confirm Password

    function validateForm() {
      let pass = document.getElementById("password").value;
      let confirm = document.getElementById("confirm").value;
      // Define strong password rule
      let strongPass = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
      // 1️⃣ Check password strength
      if (!strongPass.test(pass)) {
        Swal.fire({
          icon: 'warning',
          title: 'Weak Password',
          text: 'Your password must include uppercase, lowercase, number, and special character.',
          confirmButtonColor: '#3085d6'
        });
        return false; // stop submission
      }
      // 2️⃣ Check if passwords match
      if (pass !== confirm) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Passwords do not match!',
          confirmButtonColor: '#3085d6'
        });
        return false; // stop submission
      }
      // Combine phone before submit
      combinePhone();
      // ✅ Everything is fine — allow form to submit
      return true;
    }
  </script>
</body>

</html>