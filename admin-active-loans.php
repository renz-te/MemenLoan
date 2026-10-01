<?php
require_once 'admin-init.php';

// Fetch all approved loans with client and agent names
$stmt = $conn->prepare(
    "SELECT l.id, l.amount, l.term, l.status,
            client.firstName AS client_fname, client.lastName AS client_lname,
            agent.firstName AS agent_fname, agent.lastName AS agent_lname
     FROM loans l
     JOIN users client ON l.user_id = client.id
     JOIN users agent ON l.agent_id = agent.id
     WHERE l.status = 'Approved'
     ORDER BY l.date_applied DESC"
);
$stmt->execute();
$active_loans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Active Loans Monitoring</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <div class="dashboard-layout">
        <?php include 'admin-sidebar.php'; ?>
        <div class="main-content">
            <header class="header">
                <h1>Active Loans Monitoring</h1>
            </header>
            <section class="dashboard-main-area">
                <div class="data-table-card">
                    <div class="card-title"><i class="fas fa-tasks"></i> Ongoing Loans</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Client</th>
                                <th>Agent</th>
                                <th>Amount</th>
                                <th>Term</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($active_loans)): ?>
                                <?php foreach ($active_loans as $loan): ?>
                                    <tr>
                                        <td><?= $loan['id'] ?></td>
                                        <td><?= htmlspecialchars($loan['client_fname'] . ' ' . $loan['client_lname']) ?></td>
                                        <td><?= htmlspecialchars($loan['agent_fname'] . ' ' . $loan['agent_lname']) ?></td>
                                        <td>₱<?= number_format($loan['amount'], 2) ?></td>
                                        <td><?= $loan['term'] ?> months</td>
                                        <td>
                                            <button class="action-link view-details-btn" data-loan-id="<?= $loan['id'] ?>">View Details</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="no-data">No active loans found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <div id="loanModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
        <div class="modal-content" style="background:white; padding:20px; border-radius:10px; width:90%; max-width:600px;">
            <span class="close-btn" style="float:right; cursor:pointer; font-size:1.5rem;">&times;</span>
            <h2 id="modalLoanId">Loan Details</h2>
            <div id="modalBody">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('loanModal');
            const modalBody = document.getElementById('modalBody');
            const modalLoanId = document.getElementById('modalLoanId');
            const closeBtn = modal.querySelector('.close-btn');

            document.querySelectorAll('.view-details-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const loanId = this.dataset.loanId;
                    modalLoanId.textContent = `Loan Details #${loanId}`;
                    modalBody.innerHTML = '<p>Loading...</p>';
                    modal.style.display = 'flex';

                    // Fetch loan details via AJAX
                    fetch(`admin-get-loan-details.php?id=${loanId}`)
                        .then(response => response.text())
                        .then(html => {
                            modalBody.innerHTML = html;
                        });
                });
            });

            closeBtn.addEventListener('click', () => modal.style.display = 'none');
            window.addEventListener('click', (event) => {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>