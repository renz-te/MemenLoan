<?php
if (!isset($pending_validations)) $pending_validations = 0;
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="logo-container">
        <a href="admin-dashboard.php">MemenLoan</a>
    </div>

    <nav class="sidebar-nav">
        <a href="admin-dashboard.php"
            class="nav-item <?= $current_page === 'admin-dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>

        <a href="admin-validation.php"
            class="nav-item <?= $current_page === 'admin-validation.php' ? 'active' : '' ?>">
            <i class="fas fa-user-check"></i> User Validations
            <span class="badge"><?= $pending_validations ?></span>
        </a>

        <a href="admin-active-loans.php"
            class="nav-item <?= $current_page === 'admin-active-loans.php' ? 'active' : '' ?>">
            <i class="fas fa-money-check-alt"></i> Active Loans
            <span class="badge"><?= $active_loans_count ?></span>
        </a>

        <a href="admin-payment-proofs.php" class="nav-item">
            <i class="fas fa-receipt"></i> Payment Proofs
            <?php if ($pending_payments > 0): ?>
                <span class="badge"><?= $pending_payments ?></span>
            <?php endif; ?>
        </a>

        <a href="admin-agents.php"
            class="nav-item <?= $current_page === 'admin-agents.php' ? 'active' : '' ?>">
            <i class="fas fa-users-cog"></i> Agent Management
        </a>

        <a href="admin-requests.php"
            class="nav-item <?= $current_page === 'admin-request.php' ? 'active' : '' ?>">
            <i class="fas fa-envelope-open-text"></i> Requests
        </a>


        <a href="logout.php" class="nav-item logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>