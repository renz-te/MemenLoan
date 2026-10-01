<?php
session_start();
include("connect.php");

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
  echo json_encode(["success" => false, "message" => "Not logged in."]);
  exit;
}

$email = $_SESSION['email'];
$firstName = $_POST['firstName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$password = $_POST['password'] ?? '';
$profilePath = '';

if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
  $uploadDir = "uploads/";
  if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
  $fileName = time() . "_" . basename($_FILES['profile']['name']);
  $targetPath = $uploadDir . $fileName;
  if (move_uploaded_file($_FILES['profile']['tmp_name'], $targetPath)) {
    $profilePath = $targetPath;
  }
}

$sql = "UPDATE users SET firstName=?, lastName=?";
$params = [$firstName, $lastName];

if (!empty($password)) {
  $hashed = password_hash($password, PASSWORD_BCRYPT);
  $sql .= ", password=?";
  $params[] = $hashed;
}
if (!empty($profilePath)) {
  $sql .= ", profile=?";
  $params[] = $profilePath;
}

$sql .= " WHERE email=?";
$params[] = $email;

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("s", count($params)), ...$params);

if ($stmt->execute()) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false, "message" => $stmt->error]);
}
?>
