<?php
require_once 'agent-init.php';

$loan_id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;
if (!$loan_id || !in_array($action, ['approve', 'deny'])) {
    header("Location: agent-loans.php");
    exit;
}

// Security: Get loan details ONLY if it's Pending and assigned to THIS agent
$stmt_check = $conn->prepare("SELECT user_id, amount, wallet_type, wallet_number FROM loans WHERE id = ? AND agent_id = ? AND status = 'Pending'");
$stmt_check->bind_param("ii", $loan_id, $_SESSION['user_id']);
$stmt_check->execute();
$loan_data = $stmt_check->get_result()->fetch_assoc();
if (!$loan_data) {
    header("Location: agent-loans.php");
    exit;
}

$client_user_id = $loan_data['user_id'];
$loan_amount = number_format($loan_data['amount'], 2);
$wallet_type = $loan_data['wallet_type'];
$wallet_number = $loan_data['wallet_number'];

$new_status = ($action === 'approve') ? 'Approved' : 'Rejected';
$message = '';

if ($action === 'approve') {
    $message = "🎉 Congratulations! Your loan application #{$loan_id} for ₱{$loan_amount} has been approved. Funds will be sent to your {$wallet_type} account ({$wallet_number}) shortly.";
} else {
    $message = "We regret to inform you that your loan application #{$loan_id} has been rejected. Please contact support for more information.";
}

// 1. Update loan status
$stmt_update = $conn->prepare("UPDATE loans SET status = ? WHERE id = ?");
$stmt_update->bind_param("si", $new_status, $loan_id);
$stmt_update->execute();

// 2. Send notification to the user
$stmt_notify = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
$stmt_notify->bind_param("is", $client_user_id, $message);
$stmt_notify->execute();

header("Location: agent-loans.php");
exit();
