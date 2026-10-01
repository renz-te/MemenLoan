<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
  exit("Not logged in");
}

$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id");
