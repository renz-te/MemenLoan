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

if (!preg_match('/^\d{4}$/', $pin)) {
    echo json_encode(['success' => false, 'message' => 'PIN must be exactly 4 digits']);
    exit;
}

// ✅ Fetch user data securely
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $firstName = $user['firstName'];
    $profile = !empty($user['profile']) ? $user['profile'] : "imgs/default-profile.png";
} else {
    $firstName = "User";
    $profile = "imgs/default-profile.png";
    $user = ['pin' => null];
}

$stmt->close();

// ✅ Fix: only true if hashed PIN exists (length > 20)
$hasPin = isset($user['pin']) && strlen($user['pin']) > 20;


// ✅ Hash and save PIN
$hashedPin = password_hash($pin, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET pin = ? WHERE email = ?");
$stmt->bind_param("ss", $hashedPin, $email);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
