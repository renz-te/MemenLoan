<?php
require_once 'admin-init.php';

$loan_id = $_POST['loan_id'];
$amount_paid = $_POST['amount_paid'];

// 1. Insert the new payment record
$stmt_insert = $conn->prepare("INSERT INTO loan_payments (loan_id, amount_paid) VALUES (?, ?)");
$stmt_insert->bind_param("id", $loan_id, $amount_paid);
$stmt_insert->execute();

// 2. Check if the loan is now fully paid
$stmt_total = $conn->prepare("SELECT l.amount, SUM(lp.amount_paid) as total_paid 
                              FROM loans l
                              LEFT JOIN loan_payments lp ON l.id = lp.loan_id
                              WHERE l.id = ? GROUP BY l.id");
$stmt_total->bind_param("i", $loan_id);
$stmt_total->execute();
$result = $stmt_total->get_result()->fetch_assoc();

if ($result['total_paid'] >= $result['amount']) {
    // If total paid is greater than or equal to loan amount, mark as 'Completed'
    $stmt_complete = $conn->prepare("UPDATE loans SET status = 'Completed' WHERE id = ?");
    $stmt_complete->bind_param("i", $loan_id);
    $stmt_complete->execute();
}

header("Location: admin-active-loans.php");
exit();
