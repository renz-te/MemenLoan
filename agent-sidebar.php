<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="logo-container">
        <a href="agent-dashboard.php">MemenLoan Agent</a>
    </div>

    <nav class="sidebar-nav">
        <a href="agent-dashboard.php" class="nav-item <?= $current_page === 'agent-dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>

        <a href="agent-unassigned-loans.php" class="nav-item <?= $current_page === 'agent-unassigned-loans.php' ? 'active' : '' ?>">
            <i class="fas fa-inbox"></i> Unclaimed Loans
            <?php if ($unassigned_count > 0): ?>
                <span class="badge"><?= $unassigned_count ?></span>
            <?php endif; ?>
        </a>

        <a href="agent-loans.php" class="nav-item <?= $current_page === 'agent-loans.php' ? 'active' : '' ?>">
            <i class="fas fa-tasks"></i> My Pending Loans
        </a>

        <a href="agent-clients.php" class="nav-item <?= $current_page === 'agent-clients.php' ? 'active' : '' ?>">
            <i class="fas fa-users"></i> My Clients
        </a>
        <a href="logout.php" class="nav-item logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>