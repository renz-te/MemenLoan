<?php
session_start();
include("connect.php");

header('Content-Type: application/json');



if (isset($_SESSION['pin'])) {
    header("Location: secured.php");
    exit();
}

$email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT pin FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $user = $result->fetch_assoc()) {
    // Return true if PIN exists
    echo json_encode(['hasPin' => !empty($user['pin'])]);
} else {
    echo json_encode(['hasPin' => false]);
}
?>