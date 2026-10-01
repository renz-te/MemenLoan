<?php
require_once 'admin-init.php';

// Fetch all pending payment proofs
$stmt = $conn->prepare(
    "SELECT pp.id, pp.loan_id, pp.amount, pp.proof_image, u.firstName, u.lastName 
     FROM payment_proofs pp
     JOIN users u ON pp.user_id = u.id
     WHERE pp.status = 'pending' ORDER BY pp.submitted_at ASC"
);
$stmt->execute();
$proofs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Verify Payment Proofs - Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <div class="dashboard-layout">
        <?php include 'admin-sidebar.php'; ?>
        <div class="main-content">
            <header class="header">
                <h1>Verify Payment Proofs</h1>
            </header>
            <section class="dashboard-main-area">
                <div class="data-table-card">
                    <div class="card-title">Pending Payment Submissions</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Client Name</th>
                                <th>Loan ID</th>
                                <th>Amount Paid</th>
                                <th>Proof</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($proofs)): ?>
                                <?php foreach ($proofs as $proof): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($proof['firstName'] . ' ' . $proof['lastName']) ?></td>
                                        <td><?= $proof['loan_id'] ?></td>
                                        <td>₱<?= number_format($proof['amount'], 2) ?></td>
                                        <td><a href="<?= htmlspecialchars($proof['proof_image']) ?>" target="_blank" class="action-link">View Image</a></td>
                                        <td>
                                            <a href="admin-process-payment.php?id=<?= $proof['id'] ?>&action=approve" class="action-link" style="color:green;">Approve</a> |
                                            <a href="admin-process-payment.php?id=<?= $proof['id'] ?>&action=reject" class="action-link" style="color:red;">Reject</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="no-data">No pending payment proofs to verify.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</body>

</html>