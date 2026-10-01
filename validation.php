<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
  header("Location: index.php");
  exit();
}

$email = $_SESSION['email'];
$q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$user = mysqli_fetch_assoc($q);

$firstName = $user['firstName'];
$lastName = $user['lastName'];
$profile = !empty($user['profile']) ? $user['profile'] : 'imgs/default.png';
$success = false;
$error = '';

$posted = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
  $birthdate = mysqli_real_escape_string($conn, $_POST['birthdate']);
  $gender = mysqli_real_escape_string($conn, $_POST['gender']);
  $idtype = mysqli_real_escape_string($conn, $_POST['idtype']);
  $idnumber = mysqli_real_escape_string($conn, $_POST['idnumber']);
  $address = mysqli_real_escape_string($conn, $_POST['address']);
  $age = (int) $_POST['age'];
  $occupation = mysqli_real_escape_string($conn, $_POST['occupation']);
  $monthly_salary = mysqli_real_escape_string($conn, $_POST['monthly_salary']);

  // 🧩 Check missing fields
  if (
    empty($fullname) || empty($birthdate) || empty($gender) || empty($idtype) ||
    empty($idnumber) || empty($address) || empty($occupation) || empty($monthly_salary)
  ) {
    $error = "Please fill in all required fields.";
  } elseif ($age < 18) {
    $error = "Cannot be validated due to underage (must be 18 or above).";
  } else {
    $checkQuery = "SELECT * FROM verifications WHERE user_id = '{$user['id']}' AND (status = 'pending' OR status = 'approved')";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
      $error = "You already have a pending or approved validation request.";
    } else {
      $folder = "uploads/verifications/";
      if (!file_exists($folder)) mkdir($folder, 0777, true);

      $upload_ok = true;
      $requiredFiles = ['id_img', 'selfie_img', 'work_evidence', 'bill_meralco', 'bill_water'];

      foreach ($requiredFiles as $file) {
        if (empty($_FILES[$file]['name'])) {
          $upload_ok = false;
          break;
        }
      }

      if (!$upload_ok) {
        $error = "Please upload all required images.";
      } else {
        // move files
        foreach ($requiredFiles as $file) {
          $path[$file] = $folder . basename($_FILES[$file]['name']);
          move_uploaded_file($_FILES[$file]['tmp_name'], $path[$file]);
        }

        $sql = "INSERT INTO verifications 
        (user_id, full_name, birthdate, age, gender, address, id_type, id_number, 
         id_image, selfie_image, occupation, monthly_salary, work_evidence, bill_meralco, bill_water, status)
        VALUES (
          '{$user['id']}', '$fullname', '$birthdate', '$age', '$gender', '$address',
          '$idtype', '$idnumber', '{$path['id_img']}', '{$path['selfie_img']}',
          '$occupation', '$monthly_salary', '{$path['work_evidence']}', '{$path['bill_meralco']}', '{$path['bill_water']}', 'pending'
        )";

       if (mysqli_query($conn, $sql)) {
        $success = true;
      } else {
        $error = 'Something went wrong! Please try again.';
      }
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MemenLoan - Get the Funds You Need</title>
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="system8.css">
  <link rel="icon" href="imgs/logo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body { background: #f8f9fa; }
    .card { border-radius: 12px; margin-top: 80px; }
    .card-ofBlack { margin-top: -50px; }
    .required { color: red; }
    .preview-img { max-width: 100%; border-radius: 8px; margin-top: 8px; border: 1px solid #ddd; }
    .form-label { font-weight: 500; }
    .form-control.same-size, .form-select.same-size { height: 42px; }
    .input-group-text { height: 42px; font-weight: 500; }

    .btn-outline {
      border-radius: 6px;
      font-weight: 500;
      background-color: rgb(255, 0, 0);
      color: white;
      border: none;
    }

    .btn-outline:hover {
      background-color: rgba(186, 18, 18, 1);
      color: white;
    }
  </style>
</head>
<body>
  <header id="blur">         
    <div class="lm">
      <a href="homepage.php"><img class="logo" src="imgs/loggo.png" alt=""> MemenLoan</a>
    </div>
    <nav>
      <button class="notif-btn"><i class="fa-regular fa-lg fa-bell text-white"></i></button> 
      <input type="checkbox" id="sidebar-active">
      <label for="sidebar-active" class="open-sidebar-button">
        <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32" fill="#1f1f1f"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg>
      </label>
      <label id="overlay" for="sidebar-active"></label>
      <div class="links-container">       
        <label for="sidebar-active" class="close-sidebar-button">
          <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32" fill="#1f1f1f"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
        </label> 
        <a class="home-link" href="index.php">Home</a>
        <a href="loggedin-about.php">About us</a>
        <a href="loggedin-services.php">Services</a>
        <a href="loggedin-contact.php">Contact us</a>
        <a href="my-account.php">My account</a>
      </div>
    </nav>
  </header>

  <div class="bod py-4">
  <div class="card card1 p-4 mb-3">
    <div class="d-flex align-items-center gap-3">
      <img src="<?php echo $profile; ?>" style="width:70px;height:70px;border-radius:50%;object-fit:cover;border:2px solid #007bff;">
      <div>
        <h5 class="mb-0"><?php echo $firstName . ' ' . $lastName; ?></h5>
        <small class="text-muted"><?php echo $email; ?></small>
      </div>
    </div>
  </div>

  <section class="card-ofBlack">
    <div class="card card2 p-4">
      <h4>Account Validation</h4>
      <p class="text-muted">Please fill out all required information. <span class="required">*</span> means required.</p>

      <form method="POST" enctype="multipart/form-data">
        <div class="row g-3">

          <!-- Personal Info -->
          <div class="col-md-6">
            <label class="form-label">Full Name <span class="required">*</span></label>
            <input type="text" name="fullname" class="form-control same-size" 
                   required value="<?php echo htmlspecialchars($posted['fullname'] ?? $firstName . ' ' . $lastName); ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label">Birthdate <span class="required">*</span></label>
            <input type="date" name="birthdate" id="birthdate" class="form-control same-size"
                   value="<?php echo htmlspecialchars($posted['birthdate'] ?? ''); ?>" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Age</label>
            <input type="text" name="age" id="age" class="form-control same-size" 
                   value="<?php echo htmlspecialchars($posted['age'] ?? ''); ?>" readonly>
          </div>

          <div class="col-md-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select same-size">
              <option <?php if (($posted['gender'] ?? '') === 'Male') echo 'selected'; ?>>Male</option>
              <option <?php if (($posted['gender'] ?? '') === 'Female') echo 'selected'; ?>>Female</option>
              <option <?php if (($posted['gender'] ?? '') === 'Other') echo 'selected'; ?>>Other</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">ID Type <span class="required">*</span></label>
            <input list="idtypes" name="idtype" class="form-control same-size" placeholder="Select or type your ID type"
                   value="<?php echo htmlspecialchars($posted['idtype'] ?? ''); ?>" required>
            <datalist id="idtypes">
              <option value="National ID">
              <option value="PhilSys/PhilID">
              <option value="UMID">
              <option value="Passport">
              <option value="Driver's License">
              <option value="Voter's ID">
            </datalist>
          </div>

          <div class="col-md-6">
            <label class="form-label">ID Number <span class="required">*</span></label>
            <input type="text" name="idnumber" class="form-control same-size" 
                   value="<?php echo htmlspecialchars($posted['idnumber'] ?? ''); ?>" required>
          </div>

          <div class="col-12">
            <label class="form-label">Full Address <span class="required">*</span></label>
            <input type="text" name="address" class="form-control same-size"
                   placeholder="House No., Street, City, Province"
                   value="<?php echo htmlspecialchars($posted['address'] ?? ''); ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Occupation <span class="required">*</span></label>
            <input type="text" name="occupation" class="form-control same-size"
                   placeholder="Your job or business"
                   value="<?php echo htmlspecialchars($posted['occupation'] ?? ''); ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Monthly Salary <span class="required">*</span></label>
            <div class="input-group">
              <span class="input-group-text">₱</span>
              <input type="number" name="monthly_salary" class="form-control same-size"
                     placeholder="e.g. 25000"
                     value="<?php echo htmlspecialchars($posted['monthly_salary'] ?? ''); ?>"
                     required min="0" step="0.01">
            </div>
          </div>

          <!-- File Uploads -->
          <div class="col-md-6">
            <label class="form-label">Evidence of Work/Business <span class="required">*</span></label>
            <input type="file" name="work_evidence" id="work_evidence" accept="image/*" class="form-control same-size" required>
            <div id="work_evidencePreview"></div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Current Meralco Bill <span class="required">*</span></label>
            <input type="file" name="bill_meralco" id="bill_meralco" accept="image/*" class="form-control same-size" required>
            <div id="bill_meralcoPreview"></div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Valid ID Image <span class="required">*</span></label>
            <input type="file" name="id_img" id="id_img" accept="image/*" class="form-control same-size" required>
            <div id="id_imgPreview"></div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Current Water Bill <span class="required">*</span></label>
            <input type="file" name="bill_water" id="bill_water" accept="image/*" class="form-control same-size" required>
            <div id="bill_waterPreview"></div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Selfie Holding ID <span class="required">*</span></label>
            <input type="file" name="selfie_img" id="selfie_img" accept="image/*" class="form-control same-size" required>
            <div id="selfie_imgPreview"></div>
          </div>

          <div class="col-12 mt-4 d-flex justify-content-between align-items-center">
            <a href="my-account.php" class="btn btn-outline px-2"><i class="fa-solid fa-arrow-left"></i> Go Back</a>             
            <div class="text-end">
              <button type="submit" class="val-btn">Submit</button>
              <button type="reset" class="btn btn-outline-secondary px-4" onclick="clearPreviews()">Reset</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>

  <script>
  // ✅ Auto-calculate age
  document.getElementById('birthdate').addEventListener('change', function() {
    const birthDate = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) age--;
    document.getElementById('age').value = age > 0 ? age : '';
  });

  // ✅ Preview uploaded images
  function previewImage(input, target) {
    const preview = document.getElementById(target);
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = e => {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'preview-img';
        preview.appendChild(img);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  ['id_img', 'selfie_img', 'work_evidence', 'bill_meralco', 'bill_water'].forEach(id => {
    document.getElementById(id).addEventListener('change', e => previewImage(e.target, id + 'Preview'));
  });

  // ✅ Clear image previews + age when Reset is clicked
  function clearPreviews() {
    ['id_imgPreview','selfie_imgPreview','work_evidencePreview','bill_meralcoPreview','bill_waterPreview']
      .forEach(id => document.getElementById(id).innerHTML = '');
    document.getElementById('age').value = '';
  }

 document.querySelector('form').addEventListener('submit', function(e) {
  e.preventDefault(); // stop automatic submission

  Swal.fire({
    title: 'Confirm Submission',
    text: 'Are you sure you want to submit your validation form?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, submit it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      // only submit if confirmed
      e.target.submit();
    } else {
      Swal.close(); // just close the alert
    }
  });
});
</script>

  <?php if ($success): ?>
<script>
Swal.fire({
  icon: 'success',
  title: 'Success!',
  text: 'Your validation form has been successfully submitted!',
  confirmButtonColor: '#38a169'
}).then(() => {
  window.location.replace('my-account.php');
});
</script>
<?php endif; ?>

</body>
</html>
