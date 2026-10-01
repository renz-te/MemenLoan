<?php
session_start();
include("connect.php");

$formMessage = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sendMessage'])) {
    $fullName = $conn->real_escape_string($_POST['fullName']);
    $emailAddress = $conn->real_escape_string($_POST['emailAddress']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    // FIXED: Insert into the correct 'user_requests' table
    $stmt = $conn->prepare("INSERT INTO user_requests (user_email, request_type, subject, message) VALUES (?, 'GENERAL_INQUIRY', ?, ?)");
    $stmt->bind_param("sss", $emailAddress, $subject, $message);

    if ($stmt->execute()) {
        $formMessage = "Your message has been sent successfully!";
        $formSuccess = true;
    } else {
        $formMessage = "There was an error sending your message.";
        $formSuccess = false;
    }
    $stmt->close();
}

if (isset($_SESSION['email'])) {
    header("Location: loggedin-contact.php");
    exit();
}
