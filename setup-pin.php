<?php
session_start();
include("connect.php");

// Always return JSON
header('Content-Type: application/json');

// ✅ Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

// ✅ Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

$email = $_SESSION['email'];
$pin = $_POST['pin'] ?? '';

// ✅ Validate PIN (must be exactly 4 digits)
if (!preg_match('/^\d{4}$/', $pin)) {
    echo json_encode([
        'success' => false,
        'message' => 'PIN must be exactly 4 digits'
    ]);
    exit;
}

try {
    // ✅ Check if user already has a PIN
    $stmt = $conn->prepare("SELECT pin FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $user = $result->fetch_assoc()) {
        if (!empty($user['pin'])) {
            echo json_encode([
                'success' => false,
                'message' => 'PIN already set'
            ]);
            $stmt->close();
            $conn->close();
            exit;
        }
    }
    $stmt->close();

    // ✅ Hash the PIN before saving
    $hashedPin = password_hash($pin, PASSWORD_DEFAULT);

    // ✅ Save PIN to database
    $stmt = $conn->prepare("UPDATE users SET pin = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPin, $email);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'PIN saved successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database update failed'
        ]);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
