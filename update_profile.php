<?php
// update_profile.php (Updated)
session_start();
include("connect.php");
if (!isset($_SESSION['email'])) {
  echo json_encode(["status" => "error", "message" => "Not logged in."]);
  exit();
}
$email = $_SESSION['email'];
// Get user's last update date
$q = mysqli_query($conn, "SELECT last_updated FROM users WHERE email='$email'");
if (!$q || mysqli_num_rows($q) === 0) {
  echo json_encode(["status" => "error", "message" => "User not found."]);
  exit();
}
$user = mysqli_fetch_assoc($q);
$last_updated = $user['last_updated'];
// Check if this is the first update (last_updated is NULL) or if 60 days have passed
date_default_timezone_set('Asia/Manila');
$can_update = false;
$block_message = "";

if ($last_updated === null) {
  // First time update: Allow it
  $can_update = true;
} else {
  // Not first time: Check days passed
  $lastUpdated = new DateTime($last_updated);
  $now = new DateTime();
  $interval = $lastUpdated->diff($now);
  $days_since = (int)$interval->format('%a');
  $remaining = max(0, 60 - $days_since);
  if ($days_since >= 60) {
    // 60 days or more have passed: Allow it
    $can_update = true;
  } else {
    // Less than 60 days: Block it
    $dayText = ($remaining == 1) ? "day" : "days";
    $block_message = "You can only change your information again after $remaining $dayText.";
  }
}
if (!$can_update) {
  echo json_encode([
    "status" => "blocked",
    "message" => $block_message
  ]);
  exit();
}

// Handle uploaded profile picture (if any)
$profilePath = null;
if (!empty($_FILES['profile']['name'])) {
  $targetDir = "uploads/";
  if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
  $fileName = uniqid() . "_" . basename($_FILES["profile"]["name"]);
  $targetFile = $targetDir . $fileName;
  // Basic validation for image files
  $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
  $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
  if (in_array($imageFileType, $allowedTypes) && $_FILES["profile"]["error"] === UPLOAD_ERR_OK) {
    if (move_uploaded_file($_FILES["profile"]["tmp_name"], $targetFile)) {
      $profilePath = $targetFile;
    } else {
      echo json_encode(["status" => "error", "message" => "Failed to upload profile picture."]);
      exit();
    }
  } else {
    echo json_encode(["status" => "error", "message" => "Invalid file type or upload error."]);
    exit();
  }
}
// Get updated data (only firstName and lastName are updatable as per requirement)
$firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
$lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
// Validate that changes were actually made (optional, but aligns with JS check)
if (empty(trim($firstName)) || empty(trim($lastName))) {
  echo json_encode(["status" => "error", "message" => "First name and last name cannot be empty."]);
  exit();
}

// Update query with new last_updated (always set it on successful update)
if ($profilePath) {
  $update = "UPDATE users SET firstName='$firstName', lastName='$lastName', profile='$profilePath', last_updated=NOW() WHERE email='$email'";
} else {
  $update = "UPDATE users SET firstName='$firstName', lastName='$lastName', last_updated=NOW() WHERE email='$email'";
}
if (mysqli_query($conn, $update)) {
  // Update session variables if needed (optional, but good practice)
  $_SESSION['firstName'] = $firstName;
  $_SESSION['lastName'] = $lastName;
  echo json_encode(["status" => "success", "message" => "Successfully Changed!"]);
} else {
  echo json_encode(["status" => "error", "message" => "Failed to update profile. Please try again."]);
}
?>