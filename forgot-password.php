<?php
include("connect.php");
$message = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $desired_password = $_POST['desired_password'];
    $proof_image_path = '';

    if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/proofs/";
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time() . "_" . basename($_FILES['proof_image']['name']);
        $targetPath = $uploadDir . $fileName;
        move_uploaded_file($_FILES['proof_image']['tmp_name'], $targetPath);
        $proof_image_path = $targetPath;
    }

    if (empty($proof_image_path)) {
        $message = "Proof of identity is required. Please upload an image.";
        $success = false;
    } else {
        $subject = "Manual Password Reset Request";
        $request_message = "User requested a manual password reset with proof.";

        $stmt_insert = $conn->prepare("INSERT INTO user_requests (user_email, request_type, subject, message, desired_password, proof_image) VALUES (?, 'PASSWORD_RESET', ?, ?, ?, ?)");
        $stmt_insert->bind_param("sssss", $email, $subject, $request_message, $desired_password, $proof_image_path);
        $stmt_insert->execute();

        $message = "Your request has been submitted. An administrator will review it within one business day. You will receive a notification once your password has been updated.";
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manual Password Reset</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light">
    <div class="container" style="max-width: 500px; margin-top: 80px;">
        <div class="card p-4">
            <h2 class="text-center">Manual Reset Request</h2>
            <div class="alert alert-warning small">This high-security process requires manual review and may take up to 24 hours.</div>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3"><label class="form-label">Your Email Address</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Desired New Password</label><input type="password" name="desired_password" class="form-control" required minlength="8"></div>
                <div class="mb-3"><label class="form-label">Proof of Identity</label>
                    <p class="small text-muted">Upload a clear photo of you holding your ID.</p><input type="file" name="proof_image" class="form-control" required accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit Request</button>
                <a href="login.php" class="btn btn-link w-100 mt-2">Back to Login</a>
            </form>
        </div>
    </div>
    <?php if ($message): ?>
        <script>
            Swal.fire({
                icon: '<?= $success ? "success" : "error" ?>',
                title: '<?= $success ? "Request Sent!" : "Error!" ?>',
                text: '<?= $message ?>'
            });
        </script>
    <?php endif; ?>
</body>

</html>