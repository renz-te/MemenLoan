<?php
session_start();
include("connect.php");

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = $_SESSION['email'];
$pin = $_POST['pin'] ?? '';

// ✅ Validate PIN format
if (!preg_match('/^\d{4}$/', $pin)) {
    echo json_encode(['success' => false, 'message' => 'PIN must be exactly 4 digits']);
    exit;
}

// ✅ Fetch hashed PIN from DB
$stmt = $conn->prepare("SELECT pin FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $user = $result->fetch_assoc()) {
    // ✅ Use password_verify to check hashed PIN
    if (password_verify($pin, $user['pin'])) {
        echo json_encode(['success' => true, 'message' => 'PIN verified successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect PIN']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}

$stmt->close();
$conn->close();
?>