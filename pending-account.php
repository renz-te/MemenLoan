<?php
  session_start();
  include("connect.php");
  $email = $_SESSION['email']; 

// Kunin data ng user
  $sql = "SELECT * FROM users WHERE email='$email'";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();

  $firstName = $row['firstName'];
  $lastName = $row['lastName'];
  $email = $row['email'];
  $phone = $row['phone'];
  $profile = $row['profile'] ? $row['profile'] : 'imgs/default.png';
    if (!isset($_SESSION['email'])) {
        header("Location: index.php");
        exit();
    }
 

if (!isset($_SESSION['address'])) {
  header("Location: my-account.php");
  exit();
}
    
    $user_query = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $user_query->bind_param("s", $email);
    $user_query->execute();
    $user_result = $user_query->get_result();
    $user = $user_result->fetch_assoc();
    $user_id = $user['id'];

    // Kunin lahat ng loans ng user
    $loans_query = $conn->prepare("SELECT * FROM loans WHERE user_id = ? ORDER BY date_applied DESC");
    $loans_query->bind_param("i", $user_id);
    $loans_query->execute();
    $loans_result = $loans_query->get_result();

  if (isset($_SESSION['email'])) {
      $user_email = $_SESSION['email'];
      $query = mysqli_query($conn, "SELECT firstName, lastName, phone, email FROM users WHERE email = '$user_email'");
      if ($row = mysqli_fetch_assoc($query)) {
          $firstName = $row['firstName'];
          $lastName = $row['lastName'];
          $phone = $row['phone'];
          $email = $row['email'];
      }
  }

// ✅ Fetch logged-in user data
$email = $_SESSION['email'];
$query = $conn->prepare("SELECT firstName, lastName, email, phone, profile, email_verified, id_verified, address_verified, account_secured FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// ✅ Assign safe defaults
$firstName = $user['firstName'] ?? '';
$lastName = $user['lastName'] ?? '';
$phone = $user['phone'] ?? '';
$profilePic = !empty($user['profile']) ? $user['profile'] : 'default-profile.png';

// ✅ Check if user already has a validation record
$user_id = $user['id'] ?? null;
$has_validation = false;

if (!empty($user_id)) {
    $check_validation = mysqli_query($conn, "SELECT id FROM verifications WHERE user_id = '" . intval($user_id) . "' LIMIT 1");
    if ($check_validation && mysqli_num_rows($check_validation) > 0) {
        $has_validation = true;
    }
}

// ✅ Account status flags (use from DB if available)
$emailVerified = !empty($user['email_verified']) ? (bool)$user['email_verified'] : false;
$idVerified = !empty($user['id_verified']) ? (bool)$user['id_verified'] : false;
$addressVerified = !empty($user['address_verified']) ? (bool)$user['address_verified'] : false;
$accountSecured = !empty($user['account_secured']) ? (bool)$user['account_secured'] : false;

// ✅ Derived validation status
$accountValidated = $emailVerified && $idVerified && $addressVerified;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MemenLoan</title>
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="rename-for-myaccount8.css">
  <link rel="icon" href="imgs/logo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <!-- Header -->
  <header id="blur">         
      <div class="lm">
        <a href="homepage.php"><img class="logo" src="imgs/logo.png" alt=""> MemenLoan</a>
      </div>
      <nav>
          <div class="notif-div mt-5" style="display: none;" id="notif">
            <div>
              <h1 class="text-light">pogi ko</h1>
            </div>     
                           
          </div>
          <button class="notif-btn" id="notifBtn"><i class="fa-regular fa-lg fa-bell text-white"></i></button> 
          <input type="checkbox" id="sidebar-active">
          <label for="sidebar-active" class="open-sidebar-button">
              <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32" fill="#1f1f1f"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg>
          </label>
          <label id="overlay" for="sidebar-active"></label>
          
          <div class="links-container">       
              <label for="sidebar-active" class="close-sidebar-button">
                  <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32" fill="#1f1f1f"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
              </label> 
              <a class="home-link " href="homepage.php">Home</a>
              <a href="loggedin-about.php">About us</a>
              <a href="loggedin-services.php">Services</a>
              <a href="loggedin-contact.php">Contact us</a>
              <a class="active" href="my-account.php">My account</a>
          </div>
      </nav>
  </header>

  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="profile">
        <img src="<?php echo htmlspecialchars($profile); ?>"   class="profile-pic-display">
        <h1><?php echo htmlspecialchars($firstName); ?></h1>
      </div>

      <ul class="menu">
        <a href="my-account.php" style="text-decoration:none; color:inherit;">
          <li class="active"><i class="fas fa-gauge"></i> Account Information</li>
        </a>
        <a href="loan.php" style="text-decoration:none; color:inherit;">
          <li><i class="fas fa-box"></i> Loan</li>
        </a>
        <a href="terms-cond.php" style="text-decoration:none; color:inherit;">
          <li><i class="fas fa-user-cog"></i> Terms & Condition</li>
        </a>
        
        <button id="logoutBtn" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i> Logout
        </button>
        
      </ul>
    </aside>

    <script>
    document.getElementById("logoutBtn").addEventListener("click", function (e) {
    e.preventDefault(); // Stop default navigation

      Swal.fire({
        title: "Are you sure you want to logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        reverseButtons: true,
      }).then((result) => {
        if (result.isConfirmed) {
          // Small timeout for smoother transition
          setTimeout(() => {
            window.location.href = "logout.php";
          }, 100);
        }
      });
    });
  </script>
      
    <!-- Main Content -->
    <main class="content">
<section class="account-section"> 
  <h2 class="section-title">Account Information</h2>
  <p class="section-subtitle">Manage your account details and loan information</p>

  <!-- Account Not Secure Card -->
<div class="info-card info-card-yellow border-warning mb-3">
  <div class="icon-wrapper">
    <i class="fa-solid fa-triangle-exclamation"></i>
  </div>
  <div>
    <div class="card-title-text">Account Not Secure</div>
    <p class="card-text-body">Your data is not protected.</p>
  </div>
</div>

<!-- Account Not Validated Card -->
<div class="info-card info-card-red border-danger mb-4">
  <div class="icon-wrapper">
    <i class="fa-solid fa-xmark"></i>
  </div>
  <div>
    <div class="card-title-text">Account Not Validated</div>
    <p class="card-text-body">Your account is not yet verified
      You cannot access certain features, including loan applications.
    </p>
  </div>
</div>

  <div class="row g-4">
<div class="col-lg-7 col-md-12">
  <div class="card personal-information-card h-100">
    <div class="d-flex justify-content-between align-items-center mb-3" style="display:flex; justify-content:space-between; align-items:center;">
      <h3 class="card-header-title mb-0">Personal Information</h3>
        
      <div class="edit-btns">
        <button class="edit-btn" id="editBtn"><i class="fas fa-edit me-2"></i>Edit</button>
        <button class="save-btn" id="saveBtn" form="editForm">Save Changes</button>
        <button class="mt-2 cancel-btn" id="cancelBtn">Cancel</button>
      </div>
    </div>

    <div class="text-center profile-img-container mb-4" style="text-align:center;">
      <img src="<?php echo htmlspecialchars($profile); ?>"  class="profile-pic-display" id="profilePreview">
    </div>

    <form id="editForm" action="update_profile.php" method="POST" enctype="multipart/form-data">
      <div class="info-group mb-3">
        <label class="info-label">First Name</label>
        <p class="info-value" id="firstNameText"><?php echo htmlspecialchars($firstName); ?></p>
        <input type="text" name="firstName" id="firstNameInput" value="<?php echo htmlspecialchars($firstName); ?>">
      </div>

      <div class="info-group mb-3">
        <label class="info-label">Last Name</label>
        <p class="info-value" id="lastNameText"><?php echo htmlspecialchars($lastName); ?></p>
        <input type="text" name="lastName" id="lastNameInput" value="<?php echo htmlspecialchars($lastName); ?>">
      </div>

      <div class="info-group mb-3">
        <label class="info-label">Email Address</label>
        <p class="info-value"><?php echo htmlspecialchars($email); ?></p>
      </div>

      <div class="info-group mb-3">
        <label class="info-label">Phone Number</label>
        <p class="info-value"><?php echo htmlspecialchars($phone); ?></p>
      </div>

      <div class="info-group mb-3" id="profileInputContainer">
        <label class="info-label">Change Profile Picture</label>
        <input type="file" name="profile" id="profileInput" accept="image/*">
      </div>
    </form>
  </div>
</div>

      <!--Check validation-->
    <div class="col-lg-5 col-md-12">
  <div class="card validation-status-card h-100">
    <h3 class="card-header-title mb-3">Validation Status</h3>
    <div class="validation-list mb-4">
      <div class="validation-item mb-2">
        <p class="item-label mb-0">Email Verification</p>
        <span class="status-badge not-verified">
          <i class="fas fa-times me-1"></i> Not Verified
        </span>
      </div>
      <div class="validation-item mb-2">
        <p class="item-label mb-0">ID Document</p>
        <span class="status-badge not-verified">
          <i class="fas fa-times me-1"></i> Not Verified
        </span>
      </div>
      <div class="validation-item mb-0">
        <p class="item-label mb-0">Proof of Address</p>
        <span class="status-badge not-verified">
          <i class="fas fa-times me-1"></i> Not Verified
        </span>
      </div>
    </div>

    <div class="text-center">
      <?php if ($has_validation): ?>
        <!-- User already submitted validation form -->
        <a href="my-validation.php">
          <button class="btn view-documents-btn">View Validation Documents</button>
        </a>
      <?php else: ?>
        <!-- User has not yet submitted validation -->
        <a href="validation.php">
          <button class="btn view-documents-btn">Get Validate</button>
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="container">
    <h1 class="pt-3">Loan Overview</h1>

    <!-- Top Summary Cards -->
  <?php
  // Fetch user's loans
  $loans_query = $conn->prepare("SELECT * FROM loans WHERE user_id = ?");
  $loans_query->bind_param("i", $user_id);
  $loans_query->execute();
  $loans_result = $loans_query->get_result();
  ?>

  <div class="card mt-4">
    <div class="card-body">
      <h3>Your Loan Applications</h3>
      <table class="table">
        <thead>
          <tr>
            <th>Loan ID</th>
            <th>Amount</th>
            <th>Term</th>
            <th>Interest</th>
            <th>Status</th>
            <th>Date Applied</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($loan = $loans_result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($loan['loan_id']) ?></td>
              <td>$<?= number_format($loan['amount'], 2) ?></td>
              <td><?= htmlspecialchars($loan['term']) ?> months</td>
              <td><?= htmlspecialchars($loan['interest_rate']) ?>%</td>
              <td><?= htmlspecialchars($loan['status']) ?></td>
              <td><?= htmlspecialchars($loan['date_applied']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>


 

    <!-- Loan Application + Notifications -->
    <div class="grid">
      <!-- Apply for Loan -->
<div class="apply-loan">
  <h2>Apply for a Loan</h2>
  <p class="info">
    Your application will be verified before approval. All information is securely encrypted.
  </p>

  <form action="apply_loan.php" method="POST" id="loanForm">
    <div class="loan-slider">
      <label>Loan Amount</label>
      <input type="range" id="loanAmount" name="amount" min="1000" max="25000" step="500" value="10000" />
      <div class="loan-values">
        <span>$1,000</span>
        <span id="loanValue">$10,000</span>
        <span>$25,000</span>
      </div>
    </div>

    <label for="loanTerm">Loan Term</label>
    <select id="loanTerm" name="term">
      <option value="12">12 months</option>
      <option value="24">24 months</option>
      <option value="36" selected>36 months</option>
      <option value="48">48 months</option>
    </select>

    <input type="hidden" name="interest_rate" id="interestRate" value="12.99">

    <div class="loan-summary">
      <h3>Loan Summary</h3>
      <p>Loan Amount: <span id="summaryAmount">$10,000</span></p>
      <p>Origination Fee (2.5%): <span id="summaryFee">$250.00</span></p>
      <p>APR: <span id="summaryAPR">12.99%</span></p>
      <p>Monthly Payment: <span id="summaryPayment">$321.71</span></p>
      <p class="total">Total to Repay: <span id="summaryTotal">$11,581.48</span></p>
    </div>

    <button type="submit" class="btn">💲 Apply for Loan</button>
  </form>
</div>


      <!-- Notifications -->
      <div class="notifications">
        <h2>Recent Notifications</h2>
        <div id="notificationsList">
          <!-- Notifications will be added dynamically -->
        </div>
        <button class="btn-outline">View All Notifications</button>
      </div>
    </div>
  </div>

     <!-- Payment Schedule Section -->
    <div class="card mt-5">
      <div class="card-body">
        <h2 class="card-title mb-3">Payment Schedule</h2>
        <table class="table">
          <thead>
            <tr>
              <th>Due Date</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>December 15, 2024</td>
              <td>$502.56</td>
              <td><span class="badge bg-success">Paid</span></td>
              <td><button class="btn btn-outline-secondary btn-sm">Receipt</button></td>
            </tr>
            <tr>
              <td>January 15, 2025</td>
              <td>$502.56</td>
              <td><span class="badge bg-warning text-dark">Pending</span></td>
              <td><button class="btn btn-primary btn-sm">Pay Now</button></td>
            </tr>
            <tr>
              <td>February 15, 2025</td>
              <td>$502.56</td>
              <td><span class="badge bg-warning text-dark">Pending</span></td>
              <td><button class="btn btn-primary btn-sm">Pay Now</button></td>
            </tr>
            <tr>
              <td>March 15, 2025</td>
              <td>$502.56</td>
              <td><span class="badge bg-warning text-dark">Pending</span></td>
              <td><button class="btn btn-primary btn-sm">Pay Now</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

      <div class="early-repayment">
      <h2>Early Repayment Calculator</h2>

      <label for="earlyAmount">Early Payment Amount</label>
      <input type="range" id="earlyAmount" min="0" max="11245.8" step="100" value="5500" />
      <div class="repayment-values">
        <span>$0</span>
        <span id="repaymentValue">$5,500</span>
        <span>$11,245.8</span>
      </div>

      <div class="savings-box">
        <h3><i class="fas fa-angle-down"></i> Savings Breakdown</h3>
        <p>Current Total Interest: <span id="currentInterest">$1,460.83</span></p>
        <p>New Total Interest: <span id="newInterest">$746.38</span></p>
        <p class="saved">Interest Saved: <span id="interestSaved">$714.45</span></p>
        <p>Remaining Balance: <span id="remainingBalance">$5,745.80</span></p>
      </div>

      <p class="info">
        By making an early payment of <span id="paymentInfo">$5,500</span>, you'll save <span id="savingsInfo">$714.45</span> in interest charges.
      </p>

      <button class="btn"><i class="fas fa-dollar-sign"></i> Make Early Payment</button>
    </div>

    <div class="support-section">
      <h2>Support & Messages</h2>
      <p>Need help with your account or loan? Our support team is here to assist you.</p>
      <div class="support-buttons">
        <button class="btn">Contact Support</button>
        <button class="btn-outline">View Messages</button>
      </div>
    </div>

    
</section>

      <section class="loan-section">
  <h2 class="section-title">Loan Information</h2>
  <p class="section-subtitle">Manage your loans and view payment details</p>

  <div class="info-card info-card-blue">
    <div class="icon-wrapper">
      <i class="fa-solid fa-lock"></i>
    </div>
    <div>
      <p class="secure-text"><strong>Secure & Encrypted</strong></p>
      <p class="info-subtext">Your loan data is protected with bank-level encryption</p>
    </div>
  </div>

  <div class="loan-details-grid">
    <div class="info-card">
      <p class="card-label">Loan Amount</p>
      <h3 class="card-value">$15,000</h3>
      <p class="card-subtext">Loan ID: ML-2024-78451</p>
    </div>

    <div class="info-card">
      <p class="card-label">Current Balance</p>
      <h3 class="card-value text-red">$11,245.80</h3>
      <p class="card-subtext">24 months left</p>
    </div>

    <div class="info-card">
      <p class="card-label">APR</p>
      <h3 class="card-value">12.99%</h3>
      <p class="card-subtext">Annual percentage rate</p>
    </div>

    <div class="info-card">
      <p class="card-label">Next Payment</p>
      <h3 class="card-value">$502.56</h3>
      <p class="card-subtext">Due January 15, 2025</p>
    </div>
  </div>

  <div class="repayment-section">
    <h3 class="section-subheading">Repayment Progress</h3>

    <div class="progress-info">
      <div class="progress-text">
        <p><strong>Total Paid</strong></p>
        <p>$6,030.72 of $15,000</p>
      </div>
      <div class="progress-bar">
        <div class="progress" style="width: 40.2%;"></div>
      </div>
      <p class="progress-status">40.2% Complete · 24 of 36 months remaining</p>
    </div>

    <div class="repayment-summary">
      <p>Loan Amount: <span>$15,000</span></p>
      <p>Origination Fee: <span>$375.00</span></p>
      <p>Total Interest (12.99% APR): <span>$3,092.16</span></p>
      <p class="total-repay">Total Amount to Repay: <span>$18,092.16</span></p>
    </div>

    <div class="auto-pay-card">
      <i class="fa-solid fa-check-circle"></i>
      <span>Auto-Pay Enabled</span>
      <button class="manage-btn">Manage</button>
    </div>

    <button class="primary-btn">
      <i class="fa-solid fa-credit-card"></i> Make One-Time Payment
    </button>
  </div>
  
  
      </section>


      <section class="terms-section">
            <div class="terms-container">
    <h2>Terms and Conditions</h2>
    <p>
        Welcome to MemenLoan. By accessing and using our services, you agree to comply with 
        the following terms and conditions. Please read them carefully.
    </p>

    <h3>1. Acceptance of Terms</h3>
    <p>
        By registering an account or using our services, you agree to be bound by these 
        terms. If you do not agree, please discontinue the use of our website immediately.
    </p>

    <h3>2. Eligibility</h3>
    <p>
        To use our services, you must be at least 18 years old and capable of entering into 
        a legally binding agreement.
    </p>

    <h3>3. Loan Policy</h3>
    <p>
        All loans are subject to approval based on our evaluation process. Interest rates, 
        repayment terms, and penalties will be clearly disclosed before you accept a loan.
    </p>

    <h3>4. User Responsibilities</h3>
    <ul>
        <li>You must provide accurate and truthful information during registration.</li>
        <li>You are responsible for maintaining the confidentiality of your account.</li>
        <li>You agree to repay loans in accordance with the agreed schedule.</li>
    </ul>

    <h3>5. Privacy Policy</h3>
    <p>
        Your personal data will be handled in accordance with our Privacy Policy. We will 
        not share your information without your consent except as required by law.
    </p>

    <h3>6. Limitation of Liability</h3>
    <p>
        MemenLoan shall not be held liable for any indirect, incidental, or consequential 
        damages arising from your use of our services.
    </p>

    <h3>7. Modifications</h3>
    <p>
        We reserve the right to modify these terms at any time. Continued use of the 
        platform after changes constitutes acceptance of the new terms.
    </p>

    <h3>8. Contact Us</h3>
    <p>
        For questions regarding these terms, please contact us at 
        <a href="mailto:support@memenloan.com">support@memenloan.com</a>.
    </p>
    </div>
    

      </section >
    </main>
<style>
  /* simple section show/hide. Put this sa iyong uss.css kung gusto mo */
  .content section { display: none; }
  .content section.active { display: block; }
  /* Active menu item style (adjust sa CSS mo) */
  .sidebar .menu li.active { background: rgba(0,0,0,0.06); }
</style>

<!-- === Section Switching Script === -->
<script>
const earlyAmount = document.getElementById("earlyAmount");
const repaymentValue = document.getElementById("repaymentValue");
const paymentInfo = document.getElementById("paymentInfo");
const savingsInfo = document.getElementById("savingsInfo");

const currentInterest = document.getElementById("currentInterest");
const newInterest = document.getElementById("newInterest");
const interestSaved = document.getElementById("interestSaved");
const remainingBalance = document.getElementById("remainingBalance");

earlyAmount.addEventListener("input", () => {
  const amount = parseFloat(earlyAmount.value);
  repaymentValue.textContent = `$${amount.toLocaleString()}`;
  paymentInfo.textContent = `$${amount.toLocaleString()}`;

  // Example calculation logic (you can customize this)
  const totalInterest = 1460.83;
  const newTotalInterest = Math.max(totalInterest - amount * 0.13, 0).toFixed(2);
  const saved = (totalInterest - newTotalInterest).toFixed(2);
  const remaining = (11245.8 - amount).toFixed(2);

  newInterest.textContent = `$${newTotalInterest}`;
  interestSaved.textContent = `$${saved}`;
  remainingBalance.textContent = `$${remaining}`;
  savingsInfo.textContent = `$${saved}`;
});
(function(){
  const map = [
    { keyword: 'my-account.php', selector: '.account-section' },
    { keyword: 'loan.php', selector: '.loan-section' },
    { keyword: 'terms-cond.php', selector: '.terms-section' },
  ];

  const sections = document.querySelectorAll('main.content section');
  const menuLinks = document.querySelectorAll('.sidebar .menu a');

  // Show section and hide others
  function showSection(selectorToShow) {
    sections.forEach(sec => {
      if (sec.matches(selectorToShow)) {
        sec.classList.add('active');
      } else {
        sec.classList.remove('active');
      }
    });
  }

  // Set active sidebar item
  function setActiveMenu(selectorToShow) {
    menuLinks.forEach(a => {
      const li = a.querySelector('li');
      const match = map.find(m => a.href.includes(m.keyword));
      if (match && match.selector === selectorToShow) {
        li.classList.add('active');
      } else {
        li.classList.remove('active');
      }
    });
  }

  const editBtn = document.getElementById('editBtn');
  const saveBtn = document.getElementById('saveBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const firstNameText = document.getElementById('firstNameText');
  const lastNameText = document.getElementById('lastNameText');
  const firstNameInput = document.getElementById('firstNameInput');
  const lastNameInput = document.getElementById('lastNameInput');
  const profileInput = document.getElementById('profileInput');
  const profilePreview = document.getElementById('profilePreview');
  const originalProfile = profilePreview.src;

  editBtn.addEventListener('click', () => {
    // Enable edit mode
    firstNameText.style.display = 'none';
    lastNameText.style.display = 'none';
    firstNameInput.style.display = 'block';
    lastNameInput.style.display = 'block';
    profileInput.style.display = 'block';
    saveBtn.style.display = 'inline-block';
    cancelBtn.style.display = 'inline-block';
    editBtn.style.display = 'none';
  });

  // Swal edit info
  const editForm = document.getElementById("editForm");

// Store the original values when the page loads
const originalData = {};
const inputs = editForm.querySelectorAll("input[name]");

inputs.forEach(input => {
  originalData[input.name] = input.value;
});

editForm.addEventListener("submit", function (e) {
  e.preventDefault();

  // Check if any field has changed
  let isChanged = false;

  inputs.forEach(input => {
    if (input.type === "file") {
      // For file inputs, check if a new file was selected
      if (input.files.length > 0) {
        isChanged = true;
      }
    } else {
      if (input.value.trim() !== originalData[input.name].trim()) {
        isChanged = true;
      }
    }
  });

  // If nothing changed, show info alert and stop
  if (!isChanged) {
    Swal.fire({
      icon: "info",
      title: "No Changes Detected",
      text: "You didn’t change any information.",
      confirmButtonColor: "#3085d6"
    });
    return;
  }

  // Ask for confirmation before saving
  Swal.fire({
    title: "Are you sure?",
    text: "Do you want to save these changes?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes, save it!",
    cancelButtonText: "Cancel",
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33"
  }).then((result) => {
    if (result.isConfirmed) {
      const formData = new FormData(editForm);

      fetch("update_profile.php", {
        method: "POST",
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Successfully Changed!",
            text: "Your account details have been updated.",
            confirmButtonColor: "#3085d6"
          }).then(() => location.reload());
        } else {
          Swal.fire({
            icon: "error",
            title: "Update Failed",
            text: data.message,
            confirmButtonColor: "#d33"
          });
        }
      })
      .catch(() => {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Something went wrong while updating your profile.",
          confirmButtonColor: "#d33"
        });
      });
    }
  });
});

  // Live preview for profile picture
  profileInput.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        profilePreview.src = e.target.result;
      }
      reader.readAsDataURL(file);
    }
  });

  // Cancel edit
  cancelBtn.addEventListener('click', () => {
    // Revert fields
    firstNameInput.value = "<?php echo htmlspecialchars($firstName); ?>";
    lastNameInput.value = "<?php echo htmlspecialchars($lastName); ?>";
    profilePreview.src = originalProfile;
    profileInput.value = "";

    // Switch back to view mode
    firstNameText.style.display = 'block';
    lastNameText.style.display = 'block';
    firstNameInput.style.display = 'none';
    lastNameInput.style.display = 'none';
    profileInput.style.display = 'none';
    saveBtn.style.display = 'none';
    cancelBtn.style.display = 'none';
    editBtn.style.display = 'inline-block';
  }); 

  // Handle link click
  menuLinks.forEach(a => {
    a.addEventListener('click', e => {
      const href = a.getAttribute('href');
      const match = map.find(m => href.includes(m.keyword));
      if (match) {
        e.preventDefault();
        history.pushState(null, '', '#' + match.keyword.split('.')[0]);
        showSection(match.selector);
        setActiveMenu(match.selector);
      }
    });
  });

  // Load initial view based on hash
  function init() {
    const hash = location.hash.replace('#','').toLowerCase();
    const matched = map.find(m => m.keyword.split('.')[0] === hash);
    const selector = matched ? matched.selector : '.account-section';
    showSection(selector);
    setActiveMenu(selector);
  }

  window.addEventListener('popstate', init);
  document.addEventListener('DOMContentLoaded', init);
})();

  const notifBtn = document.getElementById('notifBtn');
  const myDiv = document.getElementById('notif');
  // I-add ang event listener sa button para sa toggle
  notifBtn.addEventListener('click', function() {
      // Suriin kung naka-hide ba (display: none)
      if (notif.style.display === 'none') {
          // Kung oo, i-show (block)
          notif.style.display = 'flex';
          
      } else {
          // Kung hindi, i-hide (none)
          notif.style.display = 'none';
            
      }
  });

// loan Overview
const loanData = {
  currentBalance: 11245.8,
  apr: 12.99,
  nextPayment: 502.56,
  nextDue: "Jan 15, 2025",
  loanTerm: 36,
  remainingTerm: 24,
  notifications: [
    {
      text: "Payment of $502.56 successfully processed",
      time: "2 hours ago",
    },
    {
      text: "Your next payment of $502.56 is due on January 15, 2025",
      time: "5 hours ago",
    },
    {
      text: "Auto-pay has been enabled for your account",
      time: "2 days ago",
    },
  ],
};

// ---- Elements ----
const currentBalanceEl = document.getElementById("currentBalance");
const aprEl = document.getElementById("apr");
const nextPaymentEl = document.getElementById("nextPayment");
const nextDueEl = document.getElementById("nextDue");
const loanTermValueEl = document.getElementById("loanTermValue");
const remainingTermEl = document.getElementById("remainingTerm");
const notificationsList = document.getElementById("notificationsList");

// Loan calculator elements
const loanAmount = document.getElementById("loanAmount");
const loanTerm = document.getElementById("loanTerm");
const loanValue = document.getElementById("loanValue");
const summaryAmount = document.getElementById("summaryAmount");
const summaryFee = document.getElementById("summaryFee");
const summaryAPR = document.getElementById("summaryAPR");
const summaryPayment = document.getElementById("summaryPayment");
const summaryTotal = document.getElementById("summaryTotal");

// ---- Helpers ----
function formatCurrency(num) {
  return "$" + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ---- Populate Overview Cards ----
function loadOverview() {
  currentBalanceEl.textContent = formatCurrency(loanData.currentBalance);
  aprEl.textContent = loanData.apr.toFixed(2) + "%";
  nextPaymentEl.textContent = formatCurrency(loanData.nextPayment);
  nextDueEl.textContent = `Due ${loanData.nextDue}`;
  loanTermValueEl.textContent = `${loanData.loanTerm} months`;
  remainingTermEl.textContent = `${loanData.remainingTerm} months remaining`;

  // Load notifications
  loanData.notifications.forEach((notif) => {
    const div = document.createElement("div");
    div.classList.add("notif");
    div.innerHTML = `<p>${notif.text}</p><span>${notif.time}</span>`;
    notificationsList.appendChild(div);
  });
}

// ---- Loan Calculation ----
function calculateLoan() {
  const amount = parseFloat(loanAmount.value);
  const apr = loanData.apr / 100;
  const fee = amount * 0.025;
  const termMonths = parseInt(loanTerm.value);
  const monthlyRate = apr / 12;

  const monthlyPayment = (amount * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -termMonths));
  const totalToRepay = monthlyPayment * termMonths;

  // Update UI
  loanValue.textContent = formatCurrency(amount);
  summaryAmount.textContent = formatCurrency(amount);
  summaryFee.textContent = formatCurrency(fee);
  summaryAPR.textContent = loanData.apr.toFixed(2) + "%";
  summaryPayment.textContent = formatCurrency(monthlyPayment);
  summaryTotal.textContent = formatCurrency(totalToRepay);
}

// ---- Event Listeners ----
loanAmount.addEventListener("input", calculateLoan);
loanTerm.addEventListener("change", calculateLoan);

// ---- Initialize Page ----
loadOverview();
calculateLoan();


  
</script>

<!-- === Logout Script === -->
<script src="logout.js"></script>


  </body>
  </html>