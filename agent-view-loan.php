<?php
require_once 'agent-init.php';
$loan_id = $_GET['id'] ?? null;
if (!$loan_id) {
    header("Location: agent-loans.php");
    exit;
}

// Security: Fetch loan ONLY if assigned to THIS agent
$stmt_loan = $conn->prepare("SELECT * FROM loans WHERE id = ? AND agent_id = ?");
$stmt_loan->bind_param("ii", $loan_id, $_SESSION['user_id']);
$stmt_loan->execute();
$loan = $stmt_loan->get_result()->fetch_assoc();
if (!$loan) {
    die("Loan not found or you do not have permission to view it.");
}

// Fetch the client's verified information
$stmt_verification = $conn->prepare("SELECT * FROM verifications WHERE user_id = ? AND status = 'approved' ORDER BY id DESC LIMIT 1");
$stmt_verification->bind_param("i", $loan['user_id']);
$stmt_verification->execute();
$verification_data = $stmt_verification->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Review Loan #<?= $loan_id ?></title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .detail-item {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .detail-item strong {
            display: block;
            color: #555;
            margin-bottom: 5px;
        }

        .detail-item span {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .action-buttons a {
            padding: 12px 25px;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .approve-btn {
            background-color: #28a745;
        }

        .deny-btn {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="dashboard-layout">
        <?php include 'agent-sidebar.php'; ?>
        <div class="main-content">
            <header class="header">
                <h1>Review Loan Application</h1>
            </header>
            <section class="dashboard-main-area">
                <div class="data-table-card">
                    <div class="card-title">Loan Details</div>
                    <div class="details-grid">
                        <div class="detail-item"><strong>Loan Amount:</strong> <span>₱<?= number_format($loan['amount'], 2) ?></span></div>
                        <div class="detail-item"><strong>Term:</strong> <span><?= htmlspecialchars($loan['term']) ?> months</span></div>
                        <div class="detail-item"><strong>Wallet:</strong> <span><?= htmlspecialchars($loan['wallet_type']) ?> (<?= htmlspecialchars($loan['wallet_number']) ?>)</span></div>
                        <div class="detail-item" style="grid-column: 1 / -1;"><strong>Reason:</strong> <span><?= htmlspecialchars($loan['reason']) ?></span></div>
                    </div>

                    <?php if ($verification_data): ?>
                        <div class="card-title" style="margin-top: 30px;">Client's Verified Information</div>
                        <div class="details-grid">
                            <div class="detail-item"><strong>Full Name:</strong> <span><?= htmlspecialchars($verification_data['full_name']) ?></span></div>
                            <div class="detail-item"><strong>Occupation:</strong> <span><?= htmlspecialchars($verification_data['occupation']) ?></span></div>
                            <div class="detail-item"><strong>Monthly Salary:</strong> <span>₱<?= number_format($verification_data['monthly_salary'], 2) ?></span></div>
                        </div>
                    <?php endif; ?>

                    <div class="action-buttons mt-4">
                        <a href="agent-process-loan.php?id=<?= $loan_id ?>&action=approve" class="approve-btn confirm-action">Approve Loan</a>
                        <a href="agent-process-loan.php?id=<?= $loan_id ?>&action=deny" class="deny-btn confirm-action">Deny Loan</a>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.confirm-action').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.href;
                const actionText = this.textContent.trim().toLowerCase();
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to ${actionText}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: (actionText === 'approve loan' ? '#28a745' : '#dc3545'),
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, proceed!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
</body>

</html>