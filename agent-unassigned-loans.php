<?php
require_once 'agent-init.php';

// Fetch all loans that are pending and have no agent assigned
$stmt = $conn->prepare(
    "SELECT l.id, u.firstName, u.lastName, l.amount, l.date_applied 
     FROM loans l
     JOIN users u ON l.user_id = u.id
     WHERE l.status = 'Pending' AND l.agent_id IS NULL 
     ORDER BY l.date_applied ASC"
);
$stmt->execute();
$unassigned_loans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Unclaimed Loans - Agent</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-layout">
        <?php include 'agent-sidebar.php'; ?>
        <div class="main-content">
            <header class="header">
                <h1>Unclaimed Loan Applications</h1>
            </header>
            <section class="dashboard-main-area">
                <div class="data-table-card">
                    <div class="card-title"><i class="fas fa-inbox"></i> Available to Claim</div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Client Name</th>
                                    <th>Amount</th>
                                    <th>Date Applied</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($unassigned_loans)): ?>
                                    <?php foreach ($unassigned_loans as $loan): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($loan['firstName'] . ' ' . $loan['lastName']) ?></td>
                                            <td>₱<?= number_format($loan['amount'], 2) ?></td>
                                            <td><?= date('F j, Y', strtotime($loan['date_applied'])) ?></td>
                                            <td>
                                                <a href="agent-claim-loan.php?id=<?= $loan['id'] ?>" class="action-link" style="background-color:#28a745; color:white; padding: 5px 10px; border-radius:5px; text-decoration: none;">Claim This Loan</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="no-data">No unclaimed loans available.</td>
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