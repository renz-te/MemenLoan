<?php
// Make sure no whitespace is before this tag
session_start();
include("connect.php");

// 1. Redirect if already logged in
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin-dashboard.php");
    } elseif ($_SESSION['role'] === 'agent') {
        header("Location: agent-dashboard.php");
    } else {
        header("Location: homepage.php");
    }
    exit();
}

// 2. Handle login form submission
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // MODIFIED QUERY: Added 'status' to the SELECT statement
    $stmt = $conn->prepare("SELECT id, firstName, password, role, status FROM users WHERE email = ? LIMIT 1");
    if (!$stmt) {
        die("Database query failed to prepare.");
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            // NEW CHECK: Block suspended agents
            if ($user['role'] === 'agent' && $user['status'] === 'Suspended') {
                echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Account Suspended',
                            text: 'Your account has been suspended. Please contact an administrator.',
                            confirmButtonColor: '#d33'
                        });
                    });
                </script>
                ";
            } else {
                // SUCCESS: Regenerate session ID for security
                session_regenerate_id(true);

                // Store user data in the session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;
                $_SESSION['firstName'] = $user['firstName'];
                $_SESSION['role'] = $user['role'];

                // Determine the redirect URL
                $redirect_url = 'homepage.php';
                if ($user['role'] === 'admin') {
                    $redirect_url = 'admin-dashboard.php';
                } elseif ($user['role'] === 'agent') {
                    $redirect_url = 'agent-dashboard.php';
                }

                // Perform the redirect and stop the script
                header("Location: " . $redirect_url);
                exit();
            }
        } else {
            // If login fails (bad password), show a generic error.
            echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Credentials',
                        text: 'The email or password you entered is incorrect.',
                        confirmButtonColor: '#d33'
                    });
                });
            </script>
            ";
        }
    } else {
        // If login fails (email not found), show a generic error.
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Credentials',
                    text: 'The email or password you entered is incorrect.',
                    confirmButtonColor: '#d33'
                });
            });
        </script>
        ";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MemenLoan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="imgs/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="endex1.css">
</head>

<body>

    <form method="post" action="login.php">
        <div class="login-container">

            <h1>Login</h1>

            <div class="input-group">
                <input type="email" name="email" id="loginEmail" placeholder="Enter your email" required>
                <i class="fa-regular fa-envelope icon"></i>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="loginPassword" placeholder="Enter your password" required>
                <i class="fa-solid fa-eye toggle-eye" id="togglePassword"></i>
            </div>

            <div class="options-group">
                <div class="forgot-password">
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>
            </div>

            <button type="submit" class="btn-login" value="Login" name="login">Login</button>
    </form>

    <div class="register-link2">
        Don't have an account? <a href="register.php">Register</a>
    </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('loginPassword');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>

</body>

</html>