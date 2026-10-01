<?php
session_start();
include("connect.php");

// 1. Check if user is logged in and validated
if (!isset($_SESSION['email'])) {
  header("Location: index.php");
  exit();
}
$email = $_SESSION['email'];
$stmt_check = $conn->prepare("SELECT id, email_verified, id_verified, address_verified FROM users WHERE email = ? LIMIT 1");
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$user_data = $stmt_check->get_result()->fetch_assoc();
$accountValidated = !empty($user_data['email_verified']) && !empty($user_data['id_verified']) && !empty($user_data['address_verified']);

if (!$accountValidated) {
  $_SESSION['loan_error'] = "You must validate your account before applying for a loan.";
  header("Location: my-account.php#loan");
  exit();
}
$user_id = $user_data['id'];

// 2. Handle loan submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $amount = $_POST['amount'] ?? 0;
  $term = $_POST['term'] ?? 0;
  $reason = trim($_POST['reason'] ?? '');
  $wallet_type = $_POST['wallet_type'] ?? '';
  $wallet_number = trim($_POST['wallet_number'] ?? '');

  if ($amount < 1000 || $amount > 50000) {
    $_SESSION['loan_error'] = "Loan amount must be between ₱1,000 and ₱50,000.";
  } else {
    // --- THIS IS THE FIX ---
    // 1. Calculate the interest rate based on the term
    $interest_rate = 0.0;
    switch ($term) {
      case 3:
        $interest_rate = 5.0;
        break;
      case 6:
        $interest_rate = 7.5;
        break;
      case 9:
        $interest_rate = 9.0;
        break;
      case 12:
        $interest_rate = 10.0;
        break;
    }
    // --- END OF FIX ---

    $status = "Pending";
    $agent_id = NULL;
    $date_applied = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime("+$term months"));

    // 2. Add interest_rate to the INSERT query
    $stmt = $conn->prepare("INSERT INTO loans (user_id, agent_id, amount, term, reason, wallet_type, wallet_number, status, date_applied, due_date, interest_rate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // 3. Add the interest_rate variable to the bind_param (with 'd' for double/decimal)
    $stmt->bind_param("iiidssssssd", $user_id, $agent_id, $amount, $term, $reason, $wallet_type, $wallet_number, $status, $date_applied, $due_date, $interest_rate);

    if ($stmt->execute()) {
      $_SESSION['loan_success'] = "Your loan application has been submitted successfully!";
    } else {
      $_SESSION['loan_error'] = "Database error. Please try again.";
    }
  }
  header("Location: apply-loan.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Apply for Loan</title>
  <link rel="stylesheet" href="css/bootstrap.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #f3f6fb;
    }

    .loan-form-container {
      max-width: 600px;
      margin: 60px auto;
      padding: 40px;
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-apply {
      width: 100%;
    }

    #wallet_number_group {
      display: none;
    }
  </style>
</head>

<body>
  <div class="loan-form-container">
    <h2 class="text-center mb-4">Apply for a Loan</h2>
    <form id="loanForm" method="POST" action="apply-loan.php">
      <div class="mb-3">
        <label for="amount" class="form-label">Loan Amount (₱)</label>
        <input type="number" class="form-control" name="amount" id="amount" min="1000" max="50000" required placeholder="1,000 - 50,000">
      </div>
      <div class="mb-3">
        <label for="term" class="form-label">Repayment Term</label>
        <select name="term" id="term" class="form-control" required>
          <option value="">Select term in months</option>
          <option value="3">3 months</option>
          <option value="6">6 months</option>
          <option value="9">9 months</option>
          <option value="12">12 months</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="wallet_type" class="form-label">E-Wallet</label>
        <select name="wallet_type" id="wallet_type" class="form-control" required>
          <option value="">Select e-wallet</option>
          <option value="GCash">GCash</option>
          <option value="PayMaya">PayMaya</option>
        </select>
      </div>
      <div class="mb-3" id="wallet_number_group">
        <label for="wallet_number" class="form-label" id="wallet_label">E-Wallet Number</label>
        <input type="text" class="form-control" name="wallet_number" id="wallet_number" placeholder="Enter your e-wallet number">
      </div>
      <div class="mb-3">
        <label for="reason" class="form-label">Reason for Loan</label>
        <textarea name="reason" id="reason" rows="3" class="form-control"></textarea>
      </div>
      <button type="submit" class="btn btn-primary btn-apply">Submit Application</button>
    </form>
    <div class="text-center mt-3">
      <a href="my-account.php#loan" class="text-secondary">← Back to My Account</a>
    </div>
  </div>
  <script>
    document.getElementById('wallet_type').addEventListener('change', function() {
      const walletNumberGroup = document.getElementById('wallet_number_group');
      if (this.value) {
        walletNumberGroup.style.display = 'block';
        document.getElementById('wallet_label').textContent = this.value + ' Number';
        document.getElementById('wallet_number').required = true;
      } else {
        walletNumberGroup.style.display = 'none';
        document.getElementById('wallet_number').required = false;
      }
    });
  </script>
</body>

</html>