<?php
include("connect.php");
$token = $_GET['token'] ?? '';
$error = '';
$success = '';

// Find the token in the database
$stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$reset_request = $result->fetch_assoc();

if (!$reset_request) {
    $error = "This link is invalid or has expired. Please request a new one.";
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $email = $reset_request['email'];

            // Update user's password
            $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt_update->bind_param("ss", $hashed_password, $email);
            $stmt_update->execute();

            // Delete the used token
            $stmt_delete = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt_delete->bind_param("s", $token);
            $stmt_delete->execute();

            $success = "Your password has been updated successfully! You can now log in.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/bootstrap.css">
</head>

<body class="bg-light">
    <div class="container" style="max-width: 500px; margin-top: 80px;">
        <div class="card p-4">
            <h2 class="text-center">Set New Password</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
                <a href="login.php" class="btn btn-primary w-100">Go to Login</a>
            <?php else: ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>