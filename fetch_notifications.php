<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
  echo json_encode(["status" => "error", "message" => "Not logged in"]);
  exit;
}

$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

$sql = "SELECT id, message, is_read, date_created FROM notifications WHERE user_id = ? ORDER BY date_created DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$notifications = [];
while ($row = $res->fetch_assoc()) {
  $notifications[] = $row;
}

echo json_encode(["status" => "success", "data" => $notifications]);
