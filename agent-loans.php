<?php
require_once 'agent-init.php';

// Fetch all loans assigned ONLY to the currently logged-in agent that are pending
$stmt = $conn->prepare(
    "SELECT l.id, u.firstName, u.lastName, l.amount, l.date_applied 
     FROM loans l
     JOIN users u ON l.user_id = u.id
     WHERE l.status = 'Pending' AND l.agent_id = ? 
     ORDER BY l.date_applied ASC"
);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$my_pending_loans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>My Pending Loans - Agent</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-layout">
        <?php include 'agent-sidebar.php'; ?>
        <div class="main-content">
            <header class="header">
                <h1>My Pending Loan Applications</h1>
            </header>
            <section class="dashboard-main-area">
                <div class="data-table-card">
                    <div class="card-title"><i class="fas fa-tasks"></i> My Review Queue</div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Loan ID</th>
                                    <th>Client Name</th>
                                    <th>Amount</th>
                                    <th>Date Applied</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($my_pending_loans)): ?>
                                    <?php foreach ($my_pending_loans as $loan): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($loan['id']) ?></td>
                                            <td><?= htmlspecialchars($loan['firstName'] . ' ' . $loan['lastName']) ?></td>
                                            <td>₱<?= number_format($loan['amount'], 2) ?></td>
                                            <td><?= date('F j, Y', strtotime($loan['date_applied'])) ?></td>
                                            <td>
                                                <a href="agent-view-loan.php?id=<?= $loan['id'] ?>" class="action-link">Review Details</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="no-data">Your queue is empty. Visit "Unclaimed Loans" to find new applications.</td>
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