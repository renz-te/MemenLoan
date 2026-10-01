<?php
require_once 'admin-init.php';
$loan_id = $_GET['id'];

// Get loan details
$stmt_loan = $conn->prepare("SELECT amount, term, interest_rate FROM loans WHERE id = ?");
$stmt_loan->bind_param("i", $loan_id);
$stmt_loan->execute();
$loan = $stmt_loan->get_result()->fetch_assoc();

// Calculate total amount due for this loan
$total_due = $loan['amount'] + ($loan['amount'] * ($loan['interest_rate'] / 100));

// Get sum of all approved payments for this loan
$stmt_payments = $conn->prepare("SELECT SUM(amount_paid) as total_paid FROM loan_payments WHERE loan_id = ?");
$stmt_payments->bind_param("i", $loan_id);
$stmt_payments->execute();
$total_paid = $stmt_payments->get_result()->fetch_assoc()['total_paid'] ?? 0;

$remaining_balance = $total_due - $total_paid;

// Get payment history
$stmt_history = $conn->prepare("SELECT amount_paid, payment_date FROM loan_payments WHERE loan_id = ? ORDER BY payment_date DESC");
$stmt_history->bind_param("i", $loan_id);
$stmt_history->execute();
$payment_history = $stmt_history->get_result()->fetch_all(MYSQLI_ASSOC);

// --- Output HTML for the modal ---
?>
<div style="font-size: 1.1rem; margin-bottom: 20px;">
    <p><strong>Total Amount Due:</strong> ₱<?= number_format($total_due, 2) ?></p>
    <p><strong>Amount Paid:</strong> ₱<?= number_format($total_paid, 2) ?></p>
    <p style="color: #dc3545; font-weight: bold;"><strong>Remaining Balance:</strong> ₱<?= number_format($remaining_balance, 2) ?></p>
</div>
<hr>
<div class="alert alert-info" style="background-color:#e1f5fe; border:1px solid #b3e5fc; padding:15px; border-radius:5px;">
    <strong>Note:</strong> To record a new payment for this loan, please review and approve the user's submission from the <strong>"Payment Proofs"</strong> page.
</div>
<hr>
<h4>Payment History</h4>
<?php if (empty($payment_history)): ?>
    <p>No payments have been recorded for this loan yet.</p>
<?php else: ?>
    <table width="100%" style="border-collapse: collapse;">
        <?php foreach ($payment_history as $payment): ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 8px 0;">₱<?= number_format($payment['amount_paid'], 2) ?></td>
                <td style="text-align:right; color: #6c757d; font-size: 0.9rem;"><?= date('F j, Y, g:i a', strtotime($payment['payment_date'])) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>