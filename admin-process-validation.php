<?php
require_once 'admin-init.php';

// Get ID and action from URL
$validation_id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$validation_id || !in_array($action, ['approve', 'deny'])) {
    header("Location: admin-validation.php");
    exit;
}

// First, get the user_id associated with this validation request
$stmt_user = $conn->prepare("SELECT user_id FROM verifications WHERE id = ?");
$stmt_user->bind_param("i", $validation_id);
$stmt_user->execute();
$user_id = $stmt_user->get_result()->fetch_assoc()['user_id'];

if (!$user_id) {
    die("Could not find user for this validation record.");
}

if ($action === 'approve') {
    // 1. Update verification status in the 'verifications' table
    $stmt_v = $conn->prepare("UPDATE verifications SET status = 'approved' WHERE id = ?");
    $stmt_v->bind_param("i", $validation_id);
    $stmt_v->execute();

    // 2. THIS IS THE CRITICAL FIX: Update all flags in the 'users' table
    $stmt_u = $conn->prepare("UPDATE users SET account_secured = 1, email_verified = 1, id_verified = 1, address_verified = 1 WHERE id = ?");
    $stmt_u->bind_param("i", $user_id);
    $stmt_u->execute();

    // 3. Send a notification to the user
    $message = "Your account validation has been approved! You can now apply for loans.";
    $stmt_n = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt_n->bind_param("is", $user_id, $message);
    $stmt_n->execute();
} elseif ($action === 'deny') {
    // This part is for denying a request
    $stmt_v = $conn->prepare("UPDATE verifications SET status = 'rejected' WHERE id = ?");
    $stmt_v->bind_param("i", $validation_id);
    $stmt_v->execute();

    $message = "Your validation was denied. Please review your submitted documents and resubmit if necessary.";
    $stmt_n = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt_n->bind_param("is", $user_id, $message);
    $stmt_n->execute();
}

// Redirect back to the validation list
header("Location: admin-validation.php");
exit;
