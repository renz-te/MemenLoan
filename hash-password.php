<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Hash Generator</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
        }

        .hash-result {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            word-wrap: break-word;
            margin-top: 20px;
            font-family: monospace;
        }
    </style>
</head>

<body>
    <div class="container card p-4">
        <h2 class="mb-3">Password Hash Generator</h2>
        <form method="post">
            <div class="mb-3">
                <label for="password" class="form-label">Enter New Password:</label>
                <input type="text" class="form-control" name="password" id="password" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary">Generate Hash</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
            $plaintext_password = $_POST['password'];
            // Generate the hash using PHP's default secure algorithm
            $hashed_password = password_hash($plaintext_password, PASSWORD_DEFAULT);

            echo '<div class="mt-4">';
            echo '<strong>Hashed Password:</strong>';
            echo '<div class="hash-result">' . htmlspecialchars($hashed_password) . '</div>';
            echo '<small class="text-muted">Copy this entire string and paste it into the password column in your database.</small>';
            echo '</div>';
        }
        ?>
    </div>
</body>

</html>