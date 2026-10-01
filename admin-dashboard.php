<?php
require_once 'admin-init.php';

$admin_name = $_SESSION['firstName'] ?? 'Admin';

// Pending user validations
$stmt_validation = $conn->prepare("SELECT COUNT(id) AS pending_validations FROM verifications WHERE status = 'pending'");
$stmt_validation->execute();
$result_validation = $stmt_validation->get_result();
$pending_validations = $result_validation->fetch_assoc()['pending_validations'] ?? 0;

// Active loans summary
$stmt_active_loans = $conn->prepare("SELECT COUNT(id) AS active_loans FROM loans WHERE status = 'Approved'");
$stmt_active_loans->execute();
$result_active_loans = $stmt_active_loans->get_result();
$active_loans = $result_active_loans->fetch_assoc()['active_loans'] ?? 0;

// Agent count
$stmt_agents = $conn->prepare("SELECT COUNT(id) AS total_agents FROM users WHERE role = 'agent' AND status = 'Active'");
$stmt_agents->execute();
$result_agents = $stmt_agents->get_result();
$total_agents = $result_agents->fetch_assoc()['total_agents'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-layout">
        <input type="checkbox" id="sidebar-toggle">

        <?php include 'admin-sidebar.php'; ?>

        <div class="main-content">
            <header class="header">
                <label for="sidebar-toggle" class="toggle-button"><i class="fas fa-bars"></i></label>
                <div class="header-left">
                    <span class="admin-profile"><i class="fas fa-user-shield"></i> Admin</span>
                </div>
            </header>

            <section class="dashboard-main-area">
                <h2 class="section-title">System Overview</h2>

                <div class="stats-grid">
                    <div class="stat-card pending">
                        <i class="fas fa-user-clock icon"></i>
                        <span class="label">Pending Validations</span>
                        <span class="value"><?= $pending_validations ?></span>
                    </div>

                    <div class="stat-card active">
                        <i class="fas fa-money-check-alt icon"></i>
                        <span class="label">Active Loans</span>
                        <span class="value"><?= $active_loans ?></span>
                    </div>

                    <div class="stat-card agents">
                        <i class="fas fa-users-cog icon"></i>
                        <span class="label">Total Agents</span>
                        <span class="value"><?= $total_agents ?></span>
                    </div>
                </div>

                <p style="margin-top:20px;">Welcome to your administrator control panel. Use the sidebar to manage users, agents, and requests.</p>
            </section>
        </div>
    </div>
</body>

</html>