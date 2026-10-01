<?php
session_start();
include("connect.php");

// ✅ Redirect if not logged in
if (!isset($_SESSION['email'])) {
  header("Location: index.php");
  exit();
}

if (isset($_SESSION['pin'])) {
  header("Location: secured-account.php");
  exit();
}

$email = $_SESSION['email'];

// ✅ Fetch user data
$stmtUser = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmtUser->bind_param("s", $email);
$stmtUser->execute();
$userResult = $stmtUser->get_result();

if ($userResult->num_rows === 0) {
  die("User not found. Please login again.");
}

$user = $userResult->fetch_assoc();
$has_pin = !empty($user['pin']);

// ✅ Assign basic info
$firstName = $user['firstName'] ?? '';
$lastName  = $user['lastName'] ?? '';
$phone     = $user['phone'] ?? '';
$profile   = !empty($user['profile']) ? $user['profile'] : 'imgs/default.png';

// ✅ Detect user ID (auto-detect key name)
$user_id = null;
foreach (['id', 'user_id', 'userID', 'uid', 'userid'] as $key) {
  if (isset($user[$key])) {
    $user_id = $user[$key];
    break;
  }
}

// ✅ Check if user has loans (optional)
$loans_query = $conn->prepare("SELECT * FROM loans WHERE user_id = ? ORDER BY date_applied DESC");
$loans_query->bind_param("i", $user_id);
$loans_query->execute();
$loans_result = $loans_query->get_result();

// ✅ Check verifications table columns
$verificationColumn = null;
$cols = $conn->query("SHOW COLUMNS FROM verifications");
if ($cols) {
  while ($col = $cols->fetch_assoc()) {
    $field = strtolower($col['Field']);
    if (in_array($field, ['user_id', 'userid', 'userID', 'uid'])) {
      $verificationColumn = $col['Field'];
      break;
    } elseif ($field === 'email') {
      $verificationColumn = $col['Field'];
    }
  }
}

// ✅ Determine validation status
$has_validation = false;

if ($verificationColumn) {
  // May column name na match
  $sql = "SELECT id FROM verifications WHERE `$verificationColumn` = ? LIMIT 1";
  $stmt = $conn->prepare($sql);

  if ($verificationColumn === 'email') {
    $stmt->bind_param("s", $email);
  } else {
    $stmt->bind_param("i", $user_id);
  }

  $stmt->execute();
  $res = $stmt->get_result();
  $has_validation = ($res && $res->num_rows > 0);
}

// ✅ Verification flags (optional from users table)
$emailVerified   = !empty($user['email_verified']);
$idVerified      = !empty($user['id_verified']);
$addressVerified = !empty($user['address_verified']);
$accountSecured  = !empty($user['account_secured']);

// ✅ Derived account validation
$accountValidated = $emailVerified && $idVerified && $addressVerified;
$stmtUser = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmtUser->bind_param("s", $email);
$stmtUser->execute();
$userResult = $stmtUser->get_result();

if ($userResult->num_rows === 0) {
  die("User not found. Please login again.");
}

$user = $userResult->fetch_assoc();
$user_id = $user['id']; // ID for loan/verification queries
$firstName = $user['firstName'] ?? '';
$profile = !empty($user['profile']) ? $user['profile'] : 'imgs/default.png';


// Fetch loans for the Payment Schedule table
$stmtLoans = $conn->prepare("SELECT * FROM loans WHERE user_id = ? ORDER BY date_applied DESC");
$stmtLoans->bind_param("i", $user_id);
$stmtLoans->execute();
$loans_result = $stmtLoans->get_result();

// Calculate current balance (Pending + Approved)
// ✅ Calculate current balance (Amount + Interest)
$stmtBalance = $conn->prepare("SELECT amount, term FROM loans WHERE user_id = ? AND status IN ('Pending','Approved')");
$stmtBalance->bind_param("i", $user_id);
$stmtBalance->execute();
$resBalance = $stmtBalance->get_result();

$total_amount = 0;
$total_interest_amount = 0;

while ($loan = $resBalance->fetch_assoc()) {
  $amount = $loan['amount'];
  $term = $loan['term'];

  // Auto interest rate based on term
  switch ($term) {
    case 1:
      $interest_rate = 3.0;
      break;
    case 3:
      $interest_rate = 5.0;
      break;
    case 6:
      $interest_rate = 7.5;
      break;
    case 12:
      $interest_rate = 10.0;
      break;
    default:
      $interest_rate = 0;
  }

  $interest_amount = $amount * ($interest_rate / 100);
  $total_amount += $amount;
  $total_interest_amount += $interest_amount;
}

$total_with_interest = $total_amount + $total_interest_amount;

// Format numbers
$current_balance = number_format($total_with_interest, 2);
$total_interest_display = number_format($total_interest_amount, 2);




?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MemenLoan</title>
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="rename-for-myaccount17.css">
  <link rel="icon" href="imgs/logo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <style>
    /* CSS to show the loan section on this page */
    .content section {
      display: none;
    }

    .content section.active {
      display: block;
    }

    .sidebar .menu li.active {
      background: rgba(0, 0, 0, 0.06);
    }

    /* Loan specific styles from your original code (adjust as needed) */
    :root {
      --primary-blue: #007aff;
      --dark-blue: #1c58aa;
      --light-blue-bg: #eaf3ff;
      --text-dark: #1f2a37;
      --text-light: #f9fafb;
    }

    .loan-acts-card {
      background-image: linear-gradient(to right bottom, var(--primary-blue), var(--dark-blue));
      color: var(--text-light);
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);

    }

    .balance-amount {
      font-size: 38px;
      font-weight: 700;
      margin-top: 5px;
    }

    .apply-loan-banner {
      background-color: var(--light-blue-bg);
      padding: 15px 20px;
      border-radius: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
    }

    .banner-content {
      display: flex;
      align-items: center;
    }

    .banner-icon {
      background-color: var(--primary-blue);
      color: var(--text-light);
      width: 35px;
      height: 35px;
      border-radius: 8px;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-right: 15px;
      width: 200px;
    }

    .limit-text {
      font-size: 14px;
      color: #6b7280;
      margin-right: 0.5rem;
    }

    .apply-text {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-dark);
    }

    .arrow-icon {
      color: #6b7280;
      font-size: 18px;
    }
  </style>
  <!-- Header -->
  <header id="blur">
    <div class="lm">
      <a href="homepage.php"><img class="logo" src="imgs/loggo.png" alt=""> MemenLoan</a>
    </div>
    <nav>


      </div>

      <input type="checkbox" id="sidebar-active">
      <label for="sidebar-active" class="open-sidebar-button">
        <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32" fill="#1f1f1f">
          <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z" />
        </svg>
      </label>
      <label id="overlay" for="sidebar-active"></label>

      <div class="links-container">
        <label for="sidebar-active" class="close-sidebar-button">
          <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32" fill="#1f1f1f">
            <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z" />
          </svg>
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
        <img src="<?php echo htmlspecialchars($profile); ?>" class="profile-pic-display">
        <h1><?php echo htmlspecialchars($firstName); ?></h1>
      </div>

      <ul class="menu">
        <a href="my-account.php" style="text-decoration:none; color:inherit;">
          <li class="active"><i class="fas fa-gauge"></i> Account Information</li>
        </a>
        <a href="loan.php" style="text-decoration:none; color:inherit;">
          <li><i class="fas fa-box"></i> Loan</li>
        </a>
        <a href="security.php" style="text-decoration:none; color:inherit;">
          <li><i class="fas fa-lock"></i> Security</li>
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
      document.getElementById("logoutBtn").addEventListener("click", function(e) {
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

        <!-- Account Security Card -->
        <?php if ($has_pin): ?>
          <div class="info-card info-card-blue border-primary mb-3">
            <div class="icon-wrapper">
              <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
              <div class="card-title-text text-primary">Account Secured</div>
              <p class="card-text-body text-primary">
                Your data is protected with your 4-digit security PIN.
              </p>
            </div>
          </div>
        <?php else: ?>
          <div class="info-card info-card-yellow border-warning mb-3">
            <div class="icon-wrapper">
              <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
              <div class="card-title-text text-warning">Account Not Secure</div>
              <p class="card-text-body text-warning">Your data is not protected.</p>
            </div>
          </div>
        <?php endif; ?>

        <?php
        // ✅ Determine if all validations are approved
        $isApproved = ($emailVerified && $idVerified && $addressVerified);
        ?>
        <!-- ✅ Account Validation Status -->
        <!-- ✅ Account Validation Status -->
        <?php if ($isApproved): ?>
          <div class="info-card info-card-green border-success mb-4">
            <div class="icon-wrapper icon-green">
              <i class="fa-solid fa-check-circle"></i>
            </div>
            <div>
              <div class="card-title-text text-success">Account Validated</div>
              <p class="card-text-body text-success">
                Your account has been successfully verified. You now have full access to all features.
              </p>
            </div>
          </div>
        <?php else: ?>
          <div class="info-card info-card-red border-danger mb-4">
            <div class="icon-wrapper">
              <i class="fa-solid fa-xmark"></i>
            </div>
            <div>
              <div class="card-title-text text-danger">Account Not Validated</div>
              <p class="card-text-body text-danger">
                Your account is not yet verified. You cannot access certain features, including loan applications.
              </p>
            </div>
          </div>
        <?php endif; ?>



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
                <img src="<?php echo htmlspecialchars($profile); ?>" class="profile-pic-display" id="profilePreview">
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

          <!-- Validation Status -->
          <div class="col-lg-5 col-md-12">
            <div class="card validation-status-card h-100">
              <h3 class="card-header-title mb-3">Validation Status</h3>
              <div class="validation-list mb-4">

                <?php if ($has_validation): ?>
                  <!-- If may record sa verifications table -->
                  <div class="validation-item mb-2">
                    <p class="item-label mb-0">Email Verification</p>
                    <span class="status-badge 
  <?php echo ($emailVerified ? 'approved' : ($has_validation ? 'pending' : 'not-verified')); ?>">
                      <?php if ($emailVerified): ?>
                        <i class="fas fa-check me-1"></i> Approved
                      <?php elseif ($has_validation): ?>
                        <i class="fas fa-hourglass-half me-1"></i> Pending
                      <?php else: ?>
                        <i class="fas fa-times me-1"></i> Not Verified
                      <?php endif; ?>
                    </span>

                    </span>

                  </div>

                  <div class="validation-item mb-2">
                    <p class="item-label mb-0">ID Document</p>
                    <span class="status-badge 
  <?php echo ($emailVerified ? 'approved' : ($has_validation ? 'pending' : 'not-verified')); ?>">
                      <?php if ($emailVerified): ?>
                        <i class="fas fa-check me-1"></i> Approved
                      <?php elseif ($has_validation): ?>
                        <i class="fas fa-hourglass-half me-1"></i> Pending
                      <?php else: ?>
                        <i class="fas fa-times me-1"></i> Not Verified
                      <?php endif; ?>
                    </span>

                  </div>

                  <div class="validation-item mb-0">
                    <p class="item-label mb-0">Proof of Address</p>
                    <span class="status-badge 
  <?php echo ($emailVerified ? 'approved' : ($has_validation ? 'pending' : 'not-verified')); ?>">
                      <?php if ($emailVerified): ?>
                        <i class="fas fa-check me-1"></i> Approved
                      <?php elseif ($has_validation): ?>
                        <i class="fas fa-hourglass-half me-1"></i> Pending
                      <?php else: ?>
                        <i class="fas fa-times me-1"></i> Not Verified
                      <?php endif; ?>
                    </span>

                  </div>

                  <div class="text-center mt-3">
                    <a href="my-validation.php">
                      <button class="btn view-documents-btn">View Validation Documents</button>
                    </a>
                  </div>

                <?php else: ?>
                  <!-- If wala pang validation record -->
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

                  <div class="text-center mt-3">
                    <a href="validation.php">
                      <button class="btn view-documents-btn">Get Validate</button>
                    </a>
                  </div>

                <?php endif; ?>

              </div>
            </div>
          </div>

          <!-- Top Summary Cards -->
          <?php
          // Fetch user's loans
          $loans_query = $conn->prepare("SELECT * FROM loans WHERE user_id = ?");
          $loans_query->bind_param("i", $user_id);
          $loans_query->execute();
          $loans_result = $loans_query->get_result();
          ?>

          <div class="container">

            <div class="support-section mt-4">
              <h2>Support & Messages</h2>
              <p>Need help with your account or loan? Our support team is here to assist you.</p>
              <div class="support-buttons">
                <a href="loggedin-contact.php"><button class="btn">Contact Support</button></a>
              </div>
            </div>


      </section>

      <section class="loan-section active">
        <h2 class="section-title">Loan Information</h2>
        <p class="section-subtitle">Manage your loans and view payment details</p>
        <div class="loan-container">


          <div class="grid">
            <div class="loan-acts-card">
              <div class="current-balance">
                <h3 class="fs-5">Current Balance</h3>
                <div class="balance-amount">
                  ₱<?= $current_balance; ?>
                </div>
                <small class="text-light" style="opacity:0.8;">
                  (Includes ₱<?= $total_interest_display; ?> interest)
                </small>
              </div>
            </div>

            <?php
            // ✅ Check if user has any active or unpaid loans
            $stmtPendingLoan = $conn->prepare("
            SELECT COUNT(*) AS active_loans 
            FROM loans 
            WHERE user_id = ? 
            AND status IN ('Pending', 'Approved')
        ");
            $stmtPendingLoan->bind_param("i", $user_id);
            $stmtPendingLoan->execute();
            $resultPendingLoan = $stmtPendingLoan->get_result();
            $rowPending = $resultPendingLoan->fetch_assoc();
            $hasPendingLoan = $rowPending['active_loans'] > 0;
            ?>

            <div class="apply-loan-banner mb-3" id="applyLoanBtn" style="cursor:pointer;">
              <div class="banner-content">
                <i class="fas fa-peso-sign banner-icon"></i>
                <div class="text-group">
                  <span class="limit-text">Get up to ₱50,000</span>
                  <span class="apply-text">Apply for a Loan</span>
                </div>
              </div>
              <i class="fas fa-arrow-right arrow-icon"></i>
            </div>

            <script>
              document.getElementById("applyLoanBtn").addEventListener("click", function() {
                const hasPending = <?= $hasPendingLoan ? 'true' : 'false'; ?>;

                if (hasPending) {
                  Swal.fire({
                    icon: "warning",
                    title: "Loan Application Blocked",
                    html: `
                <p style="color:black;">You cannot apply for a new loan yet because you still have a <b>pending or unpaid loan</b>.</p>
                <p style="color:black;">Please complete your current loan before applying again.</p>
              `,
                    confirmButtonColor: "#d33"
                  });
                } else {
                  window.location.href = "apply-loan.php";
                }
              });
            </script>




            <div class="pay-ment">
              <h2 class="card-title mb-3">Payment Schedule</h2>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Loan ID</th>
                    <th>Amount</th>
                    <th>Term</th>
                    <th>Interest Rate</th>
                    <th>Date Applied</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($loans_result->num_rows > 0): ?>
                    <?php while ($loan = $loans_result->fetch_assoc()):
                      $term = $loan['term'] ?? 0;
                      $base_amount = $loan['amount'];

                      // ✅ Auto-compute interest rate depende sa term
                      $interest = $loan['interest_rate'] ?? 0;

                      // ✅ Compute total with interest
                      $interest_amount = $base_amount * ($interest / 100);
                      $total_with_interest = $base_amount + $interest_amount;

                      // ✅ Compute Due Date based on term (in months)
                      $dateApplied = new DateTime($loan['date_applied']);
                      $dueDate = clone $dateApplied;
                      $dueDate->modify("+{$term} months");
                      $dueDateFormatted = $dueDate->format('M d, Y');

                      // ✅ Status badge & button
                      $badgeClass = ($loan['status'] == 'Completed') ? 'bg-success' : 'bg-warning text-dark';
                      $actionBtn = ($loan['status'] == 'Completed')
                        ? '<button class="btn btn-outline-secondary btn-sm" disabled>Paid</button>'
                        : '<button class="btn btn-primary btn-sm pay-now-btn" data-loan-id="' . $loan['id'] . '">Pay Now</button>';
                    ?>
                      <tr>
                        <td><?= htmlspecialchars($loan['id']); ?></td>
                        <td>
                          ₱<?= number_format($base_amount, 2); ?><br>
                          <small class="text-muted">(Total: ₱<?= number_format($total_with_interest, 2); ?>)</small>
                        </td>
                        <td><?= htmlspecialchars($term); ?> mo.</td>
                        <td><?= number_format($interest, 2); ?>%</td>
                        <td><?= date('M d, Y', strtotime($loan['date_applied'])); ?></td>
                        <td><?= $dueDateFormatted; ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($loan['status']); ?></span></td>
                        <td><?= $actionBtn; ?></td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="8" class="text-center">No loans found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>

              </table>

            </div>
          </div>
        </div>
      </section>

      <section></section>

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


      </section>
    </main>
    <style>
      /* simple section show/hide. Put this sa iyong uss.css kung gusto mo */
      .content section {
        display: none;
      }

      .content section.active {
        display: block;
      }

      /* Active menu item style (adjust sa CSS mo) */
      .sidebar .menu li.active {
        background: rgba(0, 0, 0, 0.06);
      }
    </style>

    <!-- === Section Switching Script === -->
    <script>
      (function() {
        const map = [{
            keyword: 'my-account.php',
            selector: '.account-section'
          },
          {
            keyword: 'loan.php',
            selector: '.loan-section'
          },
          {
            keyword: 'terms-cond.php',
            selector: '.terms-section'
          },
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
        document.querySelectorAll('.pay-now-btn').forEach(button => {
          button.addEventListener('click', function() {
            const loanId = this.dataset.loanId;

            Swal.fire({
              title: 'Submit Proof of Payment',
              html: `
                    <form id="paymentProofForm" enctype="multipart/form-data" style="text-align: left;">
                        <input type="hidden" name="loan_id" value="${loanId}">
                        <div class="mb-3">
                            <label for="swal-amount" class="form-label">Amount Paid (₱)</label>
                            <input id="swal-amount" name="amount" type="number" class="form-control" placeholder="Enter the amount you paid" required>
                        </div>
                        <div class="mb-3">
                            <label for="swal-proof" class="form-label">Upload Screenshot/Receipt</label>
                            <input id="swal-proof" name="proof_image" type="file" class="form-control" accept="image/*" required>
                        </div>
                    </form>
                `,
              showCancelButton: true,
              confirmButtonText: 'Submit Proof',
              // --- THIS IS THE FIX ---
              confirmButtonColor: '#28a745', // Green color for the confirm button
              cancelButtonColor: '#6c757d', // Grey color for the cancel button
              // -----------------------
              showLoaderOnConfirm: true,
              preConfirm: () => {
                const form = document.getElementById('paymentProofForm');
                const formData = new FormData(form);

                if (!formData.get('amount') || !formData.get('proof_image').size) {
                  Swal.showValidationMessage('Please fill in all fields.');
                  return false;
                }

                return fetch('submit-payment-proof.php', {
                    method: 'POST',
                    body: formData
                  })
                  .then(response => response.json())
                  .then(data => {
                    if (!data.success) {
                      throw new Error(data.message);
                    }
                    return data;
                  })
                  .catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                  });
              },
              allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
              if (result.isConfirmed) {
                Swal.fire({
                  title: 'Submitted!',
                  text: 'Your payment proof has been sent for verification.',
                  icon: 'success'
                }).then(() => {
                  location.reload();
                });
              }
            });
          });
        });

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

        // ✅ Get last edit date from PHP (if any)
        const lastEditDate = "<?php echo isset($user['last_edit']) ? $user['last_edit'] : ''; ?>";

        editForm.addEventListener("submit", function(e) {
          e.preventDefault();

          // 🔒 Check if user can edit (60-day rule)
          if (lastEditDate) {
            const lastEdit = new Date(lastEditDate);
            const now = new Date();
            const diffDays = Math.floor((now - lastEdit) / (1000 * 60 * 60 * 24));

            if (diffDays < 60) {
              Swal.fire({
                icon: "warning",
                title: "Edit Restricted",
                html: `
          <p>You can only edit your profile every <b>60 days</b>.</p>
          <p>Please wait for <b>${60 - diffDays}</b> more day(s) before making another change.</p>
        `,
                confirmButtonColor: "#d33"
              });
              return;
            }
          }

          // Check if any field has changed
          let isChanged = false;

          inputs.forEach(input => {
            if (input.type === "file") {
              if (input.files.length > 0) isChanged = true;
            } else {
              if (input.value.trim() !== originalData[input.name].trim()) isChanged = true;
            }
          });

          // If nothing changed, show info alert and stop
          if (!isChanged) {
            Swal.fire({
              icon: "info",
              title: "No Changes Detected",
              html: '<p style="color:black;">You didn’t change any information.</p>', // ✅ only this text is black
              confirmButtonColor: "#3085d6"
            });
            return;
          }

          // Ask for confirmation before saving
          Swal.fire({
            title: "Save Changes?",
            html: `
      <p style="color:black;">Update your details? You can’t edit again for <b>60 days</b>.</p>
    `,
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
                      title: "Successfully Updated!",
                      html: `
          <p style="color: black;">Your account details have been updated successfully.</p>`,
                      confirmButtonColor: "#3085d6"
                    }).then(() => location.reload());
                  } else {
                    Swal.fire({
                      icon: "error",
                      title: "Update Failed",
                      html: `<p style="color:black;">${data.message}</p>`,
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
          const hash = location.hash.replace('#', '').toLowerCase();
          const matched = map.find(m => m.keyword.split('.')[0] === hash);
          const selector = matched ? matched.selector : '.account-section';
          showSection(selector);
          setActiveMenu(selector);
        }

        window.addEventListener('popstate', init);
        document.addEventListener('DOMContentLoaded', init);
      })();


      // loan Overview
      const loanData = {
        currentBalance: 11245.8,
        apr: 12.99,
        nextPayment: 502.56,
        nextDue: "Jan 15, 2025",
        loanTerm: 36,
        remainingTerm: 24,
        notifications: [{
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
        return "$" + num.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
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

      // Add this inside the main <script> tag at the bottom of my-account.php

      document.querySelectorAll('.pay-now-btn').forEach(button => {
        button.addEventListener('click', function() {
          const loanId = this.dataset.loanId;

          Swal.fire({
            title: 'Submit Proof of Payment',
            html: `
                <form id="paymentProofForm" enctype="multipart/form-data">
                    <input type="hidden" name="loan_id" value="${loanId}">
                    <div class="mb-3 text-start">
                        <label for="swal-amount" class="form-label">Amount Paid (₱)</label>
                        <input id="swal-amount" name="amount" type="number" class="form-control" placeholder="Enter the amount you paid" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="swal-proof" class="form-label">Upload Screenshot/Receipt</label>
                        <input id="swal-proof" name="proof_image" type="file" class="form-control" accept="image/*" required>
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Submit Proof',
            showLoaderOnConfirm: true,
            preConfirm: () => {
              const form = document.getElementById('paymentProofForm');
              const formData = new FormData(form);

              if (!formData.get('amount') || !formData.get('proof_image').size) {
                Swal.showValidationMessage('Please fill in all fields.');
                return false;
              }

              return fetch('submit-payment-proof.php', {
                  method: 'POST',
                  body: formData
                })
                .then(response => response.json())
                .then(data => {
                  if (!data.success) {
                    throw new Error(data.message);
                  }
                  return data;
                })
                .catch(error => {
                  Swal.showValidationMessage(`Request failed: ${error}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
          }).then((result) => {
            if (result.isConfirmed) {
              Swal.fire({
                title: 'Submitted!',
                text: 'Your payment proof has been sent for verification.',
                icon: 'success'
              }).then(() => {
                location.reload(); // Refresh the page to show changes
              });
            }
          });
        });
      });

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

      document.getElementById("logoutBtn").addEventListener("click", function(e) {
        e.preventDefault();
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
            window.location.href = "logout.php";
          }
        });
      });
    </script>
    <!-- === Logout Script === -->
    <script src="logout.js"></script>


</body>

</html>