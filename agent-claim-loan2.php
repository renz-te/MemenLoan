<?php
require_once 'agent-init.php';

// Get the loan ID from the URL (e.g., ?id=1)
$loan_id = $_GET['id'] ?? null;
$agent_id = $_SESSION['user_id'];

if ($loan_id) {
    // This query attempts to update the loan ONLY if agent_id is still NULL.
    // This prevents two agents from claiming the same loan at the same time.
    $stmt = $conn->prepare("UPDATE loans SET agent_id = ? WHERE id = ? AND agent_id IS NULL");
    $stmt->bind_param("ii", $agent_id, $loan_id);
    $stmt->execute();

    // Check if the update was successful (i.e., this agent "won" the claim)
    if ($stmt->affected_rows > 0) {
        // Success! Redirect to the agent's personal pending list.
        $_SESSION['agent_message'] = ['type' => 'success', 'text' => 'Loan #' . $loan_id . ' has been successfully claimed!'];
        header("Location: agent-loans.php");
    } else {
        // Someone else claimed it first, or the loan is no longer available.
        $_SESSION['agent_message'] = ['type' => 'error', 'text' => 'Loan could not be claimed. It may have been taken by another agent.'];
        header("Location: agent-unassigned-loans.php");
    }
    exit();
}

// Fallback redirect if no ID is provided
header("Location: agent-unassigned-loans.php");
exit();
