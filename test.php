<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | MemenLoan</title>
    <link rel="stylesheet" href="zz.css">
    <link rel="icon" href="logo.png">
</head>
<body>
    <header class="navbar">
        <div class="logo">
            <img src="logo.png" alt="MemenLoan Logo">
            <h1>MemenLoan</h1>
        </div>
        <nav>
            <ul>
                <li><a href="#account-info">Account Info</a></li>
                <li><a href="#loan-page">Loan</a></li>
                <li><a href="#terms">Terms & Conditions</a></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section id="account-info" class="content-section">
        <h2>Account Information</h2>
        <div class="account-card">
            <p><strong>Name:</strong>?></p>
            <p><strong>Email:</strong>?></p>
            <p><strong>Joined:</strong> ?></p>
        </div>
    </section>

    <section id="loan-page" class="content-section">
        <h2>Your Loan</h2>
        <div class="loan-card">
            <p><strong>Loan Amount:</strong> ₱<?php echo $_SESSION['loan_amount'] ?? '0.00'; ?></p>
            <p><strong>Status:</strong> <?php echo $_SESSION['loan_status'] ?? 'No active loan'; ?></p>
            <button class="btn">Apply for a New Loan</button>
        </div>
    </section>

    <section id="terms" class="content-section">
        <h2>Terms and Conditions</h2>
        <p>Before proceeding with any loan applications, please review our <a href="terms.php">Terms & Conditions</a>.</p>
    </section>

    <footer>
        <p>© 2025 MemenLoan. All rights reserved.</p>
    </footer>
</body>
</html>
