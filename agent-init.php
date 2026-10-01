<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') {
    header("Location: login.php");
    exit;
}

$agent_name = $_SESSION['firstName'] ?? 'Agent';

// ADD THIS BLOCK
// Count unassigned loans for the sidebar badge
$stmt_unassigned = $conn->prepare("SELECT COUNT(id) AS unassigned_count FROM loans WHERE agent_id IS NULL AND status = 'Pending'");
$stmt_unassigned->execute();
$unassigned_count = $stmt_unassigned->get_result()->fetch_assoc()['unassigned_count'] ?? 0;
