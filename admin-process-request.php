<?php
require_once 'admin-init.php';

$request_id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$request_id || !$action) {
    header("Location: admin-requests.php");
    exit();
}

// 1. GET THE REQUEST DETAILS (EMAIL, DESIRED PASSWORD)
$stmt_req = $conn->prepare("SELECT user_email, desired_password, request_type FROM user_requests WHERE id = ?");
$stmt_req->bind_param("i", $request_id);
$stmt_req->execute();
$req_data = $stmt_req->get_result()->fetch_assoc();

if (!$req_data) {
    die("Request not found.");
}
$user_email = $req_data['user_email'];
$desired_password = $req_data['desired_password'];

// 2. CRUCIAL FIX: GET THE USER'S ID FROM THEIR EMAIL
$stmt_user = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt_user->bind_param("s", $user_email);
$stmt_user->execute();
$user_data = $stmt_user->get_result()->fetch_assoc();
$user_id = $user_data['id'] ?? null;

// Only proceed if a valid user was found
if ($user_id && $action == 'approve_password' && $desired_password) {
    // Hash the new password provided in the ticket
    $hashed_password = password_hash($desired_password, PASSWORD_DEFAULT);

    // Update the password in the 'users' table using the email address
    $update_user = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $update_user->bind_param("ss", $hashed_password, $user_email);
    $update_user->execute();

    // Create a notification for the user (this will now work)
    $notification_message = "Your password has been successfully reset by an administrator. You can now log in with the new password you provided.";
    $notify_stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $notify_stmt->bind_param("is", $user_id, $notification_message);
    $notify_stmt->execute();

    $_SESSION['admin_message'] = "The password for <strong>$user_email</strong> has been successfully updated.";
}

// Always mark the request as resolved after an action
if ($action == 'mark_resolved' || $action == 'approve_password') {
    $update_req = $conn->prepare("UPDATE user_requests SET status = 'resolved' WHERE id = ?");
    $update_req->bind_param("i", $request_id);
    $update_req->execute();
}

header("Location: admin-requests.php");
exit();
