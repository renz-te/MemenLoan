<?php
// Start session if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'connect.php';

// Restrict access to admins only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// --- ADD THIS CODE ---
// Calculate pending validations for the sidebar
$stmt_val_count = $conn->prepare("SELECT COUNT(id) AS pending_count FROM verifications WHERE status = 'pending'");
$stmt_val_count->execute();
$pending_validations = $stmt_val_count->get_result()->fetch_assoc()['pending_count'] ?? 0;
// --- END OF ADDED CODE ---

// In admin-init.php
$stmt_active_count = $conn->prepare("SELECT COUNT(id) AS active_count FROM loans WHERE status = 'Approved'");
$stmt_active_count->execute();
$active_loans_count = $stmt_active_count->get_result()->fetch_assoc()['active_count'] ?? 0;

// Add this to admin-init.php
$stmt_payments = $conn->prepare("SELECT COUNT(id) AS pending_payments FROM payment_proofs WHERE status = 'pending'");
$stmt_payments->execute();
$pending_payments = $stmt_payments->get_result()->fetch_assoc()['pending_payments'] ?? 0;

// Optional: store admin info for reuse
$admin_name = $_SESSION['firstName'] ?? 'Admin';
// Optional: store admin info for reuse
$admin_name = $_SESSION['firstName'] ?? 'Admin';
$admin_email = $_SESSION['email'] ?? '';
