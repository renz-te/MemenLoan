<?php
require_once 'agent-init.php';

// Fetch statistics
$stmt_pending = $conn->prepare("SELECT COUNT(id) AS pending_loans FROM loans WHERE status = 'Pending' AND agent_id = ?");
$stmt_pending->bind_param("i", $_SESSION['user_id']);
$stmt_pending->execute();
$pending_loans = $stmt_pending->get_result()->fetch_assoc()['pending_loans'] ?? 0;

$stmt_approved = $conn->prepare("SELECT COUNT(id) AS approved_loans FROM loans WHERE status = 'Approved' AND agent_id = ?");
$stmt_approved->bind_param("i", $_SESSION['user_id']);
$stmt_approved->execute();
$approved_loans = $stmt_approved->get_result()->fetch_assoc()['approved_loans'] ?? 0;

$stmt_clients = $conn->prepare("SELECT COUNT(DISTINCT user_id) AS total_clients FROM loans WHERE agent_id = ?");
$stmt_clients->bind_param("i", $_SESSION['user_id']);
$stmt_clients->execute();
$total_clients = $stmt_clients->get_result()->fetch_assoc()['total_clients'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard</title>
    <link rel="stylesheet" href="admin.css"> <!-- reuse admin.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-layout">
        <input type="checkbox" id="sidebar-toggle">

        <?php include 'agent-sidebar.php'; ?>

        <div class="main-content">
            <header class="header">
                <label for="sidebar-toggle" class="toggle-button"><i class="fas fa-bars"></i></label>
                <h1>Welcome back, <?= htmlspecialchars($agent_name) ?>!</h1>
                <div class="header-right">
                    <span class="admin-profile"><i class="fas fa-user-tie"></i> Agent</span>
                </div>
            </header>

            <section class="dashboard-main-area">
                <h2 class="section-title">Performance Overview</h2>

                <div class="stats-grid">
                    <div class="stat-card pending">
                        <i class="fas fa-hourglass-half icon"></i>
                        <span class="label">Pending Loans</span>
                        <span class="value"><?= $pending_loans ?></span>
                    </div>

                    <div class="stat-card active">
                        <i class="fas fa-check-circle icon"></i>
                        <span class="label">Approved Loans</span>
                        <span class="value"><?= $approved_loans ?></span>
                    </div>

                    <div class="stat-card agents">
                        <i class="fas fa-users icon"></i>
                        <span class="label">Total Clients</span>
                        <span class="value"><?= $total_clients ?></span>
                    </div>
                </div>

                <p style="margin-top:20px;">Monitor your assigned loans and clients. Manage applications and report client issues directly to the admin.</p>
            </section>
        </div>
    </div>
</body>

</html>