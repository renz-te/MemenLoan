<?php
require_once 'admin-init.php';

// Handle form submission to assign an agent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_agent'])) {
    $loan_id = $_POST['loan_id'];
    $agent_id = $_POST['agent_id'];

    $stmt_update = $conn->prepare("UPDATE loans SET agent_id = ? WHERE id = ?");
    $stmt_update->bind_param("ii", $agent_id, $loan_id);
    if ($stmt_update->execute()) {
        $_SESSION['admin_message'] = "Loan #{$loan_id} has been successfully assigned.";
    }
    header("Location: admin-loan-assignments.php");
    exit();
}

// Fetch all loans that are pending and have no agent assigned
$stmt_loans = $conn->prepare(
    "SELECT l.id, u.firstName, u.lastName, l.amount, l.date_applied 
     FROM loans l
     JOIN users u ON l.user_id = u.id
     WHERE l.status = 'Pending' AND l.agent_id IS NULL
     ORDER BY l.date_applied ASC"
);
$stmt_loans->execute();
$unassigned_loans = $stmt_loans->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch all active agents to populate the dropdown
$stmt_agents = $conn->prepare("SELECT id, firstName, lastName FROM users WHERE role = 'agent' AND status = 'Active'");
$stmt_agents->execute();
$active_agents = $stmt_agents->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Loan Assignments - Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-layout">
        <?php include 'admin-sidebar.php'; ?>
        <div class="main-content">
            <header class="header">
                <h1>Loan Assignments</h1>
            </header>
            <section class="dashboard-main-area">
                <?php
                if (isset($_SESSION['admin_message'])) {
                    echo "<div style='padding:15px; background-color:#d4edda; color:#155724; border:1px solid #c3e6cb; border-radius:5px; margin-bottom:20px;'>{$_SESSION['admin_message']}</div>";
                    unset($_SESSION['admin_message']);
                }
                ?>
                <div class="data-table-card">
                    <div class="card-title"><i class="fas fa-user-tag"></i> Unassigned Pending Loans</div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Loan ID</th>
                                    <th>Client Name</th>
                                    <th>Amount</th>
                                    <th>Assign To Agent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($unassigned_loans)): ?>
                                    <?php foreach ($unassigned_loans as $loan): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($loan['id']) ?></td>
                                            <td><?= htmlspecialchars($loan['firstName'] . ' ' . $loan['lastName']) ?></td>
                                            <td>₱<?= number_format($loan['amount'], 2) ?></td>
                                            <td>
                                                <form method="POST" style="display:flex; gap:10px;">
                                                    <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                                                    <select name="agent_id" required style="flex-grow:1; padding:8px;">
                                                        <option value="">-- Select Agent --</option>
                                                        <?php foreach ($active_agents as $agent): ?>
                                                            <option value="<?= $agent['id'] ?>">
                                                                <?= htmlspecialchars($agent['firstName'] . ' ' . $agent['lastName']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <button type="submit" name="assign_agent" style="padding:8px 15px; cursor:pointer;">Assign</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="no-data">No unassigned loans found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>

</html>