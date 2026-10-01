<?php
session_start();
include("connect.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$loan_id = $_POST['loan_id'];
$amount = $_POST['amount'];
$proof_image_path = '';

if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "uploads/payments/";
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . "_" . uniqid() . "_" . basename($_FILES['proof_image']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['proof_image']['tmp_name'], $targetPath)) {
        $proof_image_path = $targetPath;
    }
}

if (empty($proof_image_path) || empty($amount) || empty($loan_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing required information.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO payment_proofs (loan_id, user_id, amount, proof_image) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iids", $loan_id, $user_id, $amount, $proof_image_path);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
}
