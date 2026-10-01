<?php
require_once 'admin-init.php';

$proof_id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$proof_id || !in_array($action, ['approve', 'reject'])) {
    header("Location: admin-payment-proofs.php");
    exit;
}

// Get the proof details
$stmt_proof = $conn->prepare("SELECT loan_id, amount, user_id FROM payment_proofs WHERE id = ?");
$stmt_proof->bind_param("i", $proof_id);
$stmt_proof->execute();
$proof_data = $stmt_proof->get_result()->fetch_assoc();
$loan_id = $proof_data['loan_id'];
$amount = $proof_data['amount'];
$user_id = $proof_data['user_id'];

if ($action === 'approve') {
    // 1. Add the payment to the loan_payments table
    $stmt_insert = $conn->prepare("INSERT INTO loan_payments (loan_id, amount_paid) VALUES (?, ?)");
    $stmt_insert->bind_param("id", $loan_id, $amount);
    $stmt_insert->execute();

    // 2. Update the proof status to 'approved'
    $stmt_update_proof = $conn->prepare("UPDATE payment_proofs SET status = 'approved' WHERE id = ?");
    $stmt_update_proof->bind_param("i", $proof_id);
    $stmt_update_proof->execute();

    // 3. Send a confirmation notification to the user
    $message = "Your payment of ₱" . number_format($amount, 2) . " for Loan ID #{$loan_id} has been verified and recorded.";
    $stmt_notify = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt_notify->bind_param("is", $user_id, $message);
    $stmt_notify->execute();

    // 4. Check if the loan is now fully paid
    $stmt_total = $conn->prepare("SELECT l.amount, l.interest_rate, SUM(lp.amount_paid) as total_paid 
                                  FROM loans l 
                                  LEFT JOIN loan_payments lp ON l.id = lp.loan_id 
                                  WHERE l.id = ? GROUP BY l.id");
    $stmt_total->bind_param("i", $loan_id);
    $stmt_total->execute();
    $result = $stmt_total->get_result()->fetch_assoc();

    $total_due_for_loan = $result['amount'] + ($result['amount'] * ($result['interest_rate'] / 100));

    if ($result['total_paid'] >= $total_due_for_loan) {
        // Mark the loan as 'Completed'
        $stmt_complete = $conn->prepare("UPDATE loans SET status = 'Completed' WHERE id = ?");
        $stmt_complete->bind_param("i", $loan_id);
        $stmt_complete->execute();
    }
} elseif ($action === 'reject') {
    // Just update the proof status to 'rejected'
    $stmt_update_proof = $conn->prepare("UPDATE payment_proofs SET status = 'rejected' WHERE id = ?");
    $stmt_update_proof->bind_param("i", $proof_id);
    $stmt_update_proof->execute();
}

header("Location: admin-payment-proofs.php");
exit();
