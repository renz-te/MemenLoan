<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $firstName = $user['firstName'];
    $profile = !empty($user['profile']) ? $user['profile'] : "imgs/default-profile.png";
} else {
    $firstName = "User";
    $profile = "imgs/default-profile.png";
}

$hasPin = !empty($user['pin']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>MemenLoan</title>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="rename-for-myaccount17.css">
<link rel="icon" href="imgs/logo.png" type="image/png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<header id="blur">         
    <div class="lm">
        <a href="homepage.php"><img class="logo" src="imgs/loggo.png" alt=""> MemenLoan</a>
    </div>
    <nav>

        <input type="checkbox" id="sidebar-active">
        <label for="sidebar-active" class="open-sidebar-button">
            <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32" fill="#1f1f1f"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg>
        </label>
        <label id="overlay" for="sidebar-active"></label>
        <div class="links-container">       
            <label for="sidebar-active" class="close-sidebar-button">
                <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32" fill="#1f1f1f"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
            </label> 
            <a class="home-link" href="homepage.php">Home</a>
            <a href="loggedin-about.php">About us</a>
            <a href="loggedin-services.php">Services</a>
            <a href="loggedin-contact.php">Contact us</a>
            <a class="active" href="my-account.php">My account</a>
        </div>
    </nav>
</header>

<style>
      /* --- Your existing CSS for the dashboard starts here --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        :root{
            /* Your provided general variables */
            --second: #007bff;
            --main: #0056b3;
            --third: rgb(0, 149, 255);
            --font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;

            /* Variables required by the dashboard design/SweetAlert */
            --primary-color: #38a169; /* Green for success/primary action */
            --secondary-color: #3b82f6; /* Blue for secondary action */
            --sidebar-bg: #ffffff;
            --active-link-bg: #3b82f6; 
            --active-link-text: #ffffff;
            --text-dark: #374151;
            --text-light: #6b7280;
            --page-bg: #f9fafb;
            --border-color: #e5e7eb;
        }

 
        /* --- End of your provided CSS --- */


        /* --- Custom SweetAlert Styling (to match 4 pinn.png) --- */
        
        /* Targets the icon in the modal (green lock) */
        .swal2-pin-icon {
            font-size: 3rem;
            color: var(--primary-color); 
            margin-bottom: 10px;
            display: block;
        }

        /* Targets the title in the modal */
        .swal2-title {
            font-weight: 700 !important;
            margin-top: 10px !important;
            margin-bottom: 5px !important;
            font-size: 1.5rem !important;
        }
        
        /* Targets the subtitle text */
        .swal2-html-container {
            font-size: 1rem !important;
            color: var(--text-light) !important;
            margin-bottom: 30px !important;
        }

        /* Container for the 4 input boxes */
        .pin-input-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            margin-bottom: 30px; 
        }
        
        /* Individual PIN box styling (4 boxes) */
        .pin-box {
            width: 60px;
            height: 60px;
            border-radius: 10px; 
            background-color: var(--page-bg); 
            border: 1px solid var(--border-color); 
            font-size: 1.5rem;
            text-align: center;
            line-height: 1;
            box-shadow: none;
            color: transparent; 
            text-shadow: 0 0 0 var(--text-dark); 
            transition: border-color 0.2s, box-shadow 0.2s;
            padding: 0;
        }

        .pin-box:focus {
            outline: none;
            border-color: var(--primary-color); 
            box-shadow: 0 0 0 2px rgba(56, 161, 105, 0.5); 
        }

        /* Adjust button padding */
        .swal2-actions button {
            padding: 0.625em 1.25em !important;
            font-size: 1em !important;
        }

        /* --- Your existing dashboard CSS continues after this point (from your original code) --- */
        
        /* --- Main Dashboard Layout (Grid) --- */
        .dashboard-container {
            display: grid;
            /* Define areas: top row for header, and two columns for sidebar/content */
            grid-template-areas:
                "top top"
                "layout layout";
        }   

        .main-layout {
            grid-area: layout;
            display: grid;
            /* Sidebar is 250px wide, content takes the rest */
            grid-template-columns: 250px 1fr; 
            min-height: calc(100vh - 46px); /* Full viewport height minus the top-nav height */
        }

        .user-profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 20px 20px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 20px;
        }

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid var(--border-color);
        }

        .username {
            font-weight: 600;
            color: var(--text-dark);
        }

        /* --- Main Content Styling --- */
        .content {
            padding: 30px;
            background-color: var(--page-bg);
        }

        main {
            margin-top: 3rem;
        }

        .page-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: var(--text-light);
            margin-bottom: 30px;
        }

        /* --- Security Action Cards --- */
        .security-actions {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
        }

        .action-card {
            flex: 1;
            background-color: var(--sidebar-bg);
            padding: 30px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .action-card i {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .pin-icon {
            color: var(--primary-color);
        }

        .password-icon {
            color: var(--secondary-color);
        }

        .action-card h3 {
            font-size: 1.25rem;
            margin-bottom: 5px;
        }

        .action-card p {
            color: var(--text-light);
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #308056;
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #3371dc;
        }

        /* --- Security Measures Section --- */
        .security-measures {
            background-color: var(--sidebar-bg);
            padding: 30px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .security-measures h2 {
            font-size: 1.35rem;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
            color: var(--text-dark);
        }

        .check-icon {
            color: var(--primary-color);
            margin-right: 10px;
        }

        .intro-text {
            color: var(--text-light);
            margin-bottom: 30px;
        }

        .measure-group {
            margin-bottom: 25px;
        }

        .measure-group h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--secondary-color);
        }

        .measure-group h3 i {
            margin-right: 8px;
        }

        .measure-group ul {
            list-style: none;
            padding-left: 0;
        }

        .measure-group li {
            padding: 5px 0 5px 25px;
            position: relative;
            color: var(--text-dark);
            line-height: 1.5;
        }

        .measure-group li::before {
            content: "•"; /* Custom bullet point */
            position: absolute;
            left: 8px;
            color: var(--text-dark);
            font-size: 1.2em;
            line-height: 1;
        }

        .footer-note {
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-light);
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            margin-top: 30px;
            border: 1px solid var(--border-color);
        }

        /* --- Responsiveness (Mobile View) --- */
        @media (max-width: 768px) {
            .main-layout {
                /* Stack the sidebar and content vertically */
                grid-template-columns: 1fr;
            }
            .security-actions {
                /* Stack action cards vertically on mobile */
                flex-direction: column;
            }

            .content {
                padding: 20px 15px;
            }
        }

    </style>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="profile">
            <img src="<?php echo htmlspecialchars($profile); ?>" class="profile-pic-display">
            <h1><?php echo htmlspecialchars($firstName); ?></h1>
        </div>

        <ul class="menu">
            <a href="my-account.php" style="text-decoration:none; color:inherit;">
                <li><i class="fas fa-gauge"></i> Account Information</li>
            </a>
            <a href="my-account.php#loan" style="text-decoration:none; color:inherit;">
                <li><i class="fas fa-box"></i> Loan</li>
            </a>
            <a href="security.php" style="text-decoration:none; color:inherit;">
                <li class="active"><i class="fas fa-lock"></i> Security</li>
            </a>
            <a href="my-account.php#terms-cond" style="text-decoration:none; color:inherit;">
                <li><i class="fas fa-user-cog"></i> Terms & Condition</li>
            </a>
            <button id="logoutBtn" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </ul>
    </aside>

    <main class="content">
        <h1 class="page-title">MemenLoan Security Overview</h1>
        <p class="page-subtitle">Your security is our priority. Manage your account protection settings below.</p>

        <div class="security-actions">
            <div class="action-card">
                <i class="fas fa-unlock-alt pin-icon"></i>
                <h3>4-Pin Security</h3>
                <p>Set up a quick 4-digit PIN for secure access</p>
                <button id="createPinBtn" class="btn btn-primary">Create 4-Pin Security</button>
            </div>
            <div class="action-card">
                <i class="fas fa-shield-alt password-icon"></i>
                <h3>Password</h3>
                <p>Update your account password anytime</p>
                <a href="change-pass.php"><button id="changePasswordBtn" class="btn btn-secondary">Change Password</button></a>
            </div>
        </div>

        <div class="security-measures">
            <h2><i class="fas fa-check-circle check-icon"></i> Security Measures</h2>
            <p class="intro-text">The website ensures user data is protected during registration, login, and loan application processes.</p>

            <div class="measure-group">
                <h3><i class="fas fa-database"></i> Data Protection</h3>
                <ul>
                    <li>All user information, including personal details, ID documents, and financial records, are securely stored and validated by admins.</li>
                    <li>End-to-end encryption protects your data during transmission and storage.</li>
                </ul>
            </div>

            <div class="measure-group">
                <h3><i class="fas fa-user-lock"></i> Account Security</h3>
                <ul>
                    <li>Users can set a 4-Pin Security and change their password at any time to keep their accounts secure.</li>
                    <li>Strong password requirements ensure your account remains protected.</li>
                </ul>
            </div>

            <div class="measure-group">
                <h3><i class="fas fa-user-shield"></i> Optional Security Features</h3>
                <ul>
                    <li>Two-Factor Authentication (2FA) provides an extra layer of protection for your account.</li>
                    <li>Secure notifications alert you of important account activities and help prevent unauthorized access.</li>
                </ul>
            </div>

            <div class="footer-note">
                We continuously monitor and update our security measures to protect your data and ensure a safe experience.
            </div>
        </div>
    </main>
</div>

<script>

const hasPin = <?php echo $hasPin ? 'true' : 'false'; ?>;

// ✅ CREATE PIN BUTTON (unchanged)
const createPinBtn = document.getElementById('createPinBtn');
createPinBtn.addEventListener('click', async () => {
    if (hasPin) {
        Swal.fire({
            icon: 'info',
            title: 'PIN Already Set',
            text: 'You have already set a 4-PIN. You cannot create it again.',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }

    const { value: pin } = await Swal.fire({
        title: 'Set up 4-PIN Security',
        html: `
        <div class="pin-input-container">
            <input type="password" maxlength="1" class="pin-box" id="pin1" autofocus>
            <input type="password" maxlength="1" class="pin-box" id="pin2">
            <input type="password" maxlength="1" class="pin-box" id="pin3">
            <input type="password" maxlength="1" class="pin-box" id="pin4">
        </div>`,
        confirmButtonText: 'Save PIN',
        focusConfirm: false,
        preConfirm: () => {
            const p1 = document.getElementById('pin1').value;
            const p2 = document.getElementById('pin2').value;
            const p3 = document.getElementById('pin3').value;
            const p4 = document.getElementById('pin4').value;
            if (!p1 || !p2 || !p3 || !p4) {
                Swal.showValidationMessage('Please enter all 4 digits');
                return false;
            }
            return p1 + p2 + p3 + p4;
        },
        didOpen: () => setupPinInputNavigation()
    });

    if (pin) {
        const formData = new FormData();
        formData.append('pin', pin);
        try {
            const response = await fetch('save-pin.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                Swal.fire('Success', '4-PIN saved successfully!', 'success').then(() => {
                    location.reload(); // Refresh so hasPin becomes true
                });
            } else {
                Swal.fire('Error', result.message || 'Failed to save PIN', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Server connection failed', 'error');
        }
    }
});

// ✅ CHANGE PASSWORD BUTTON BEHAVIOR
const changePasswordBtn = document.getElementById('changePasswordBtn');
changePasswordBtn.addEventListener('click', async (e) => {
    e.preventDefault();

    if (!hasPin) {
        Swal.fire({
            icon: 'warning',
            title: 'Set 4-PIN Security First',
            text: 'You must create your 4-digit PIN before changing your password.',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }

    const { value: enteredPin } = await Swal.fire({
        title: 'Enter 4-PIN',
        html: `
        <div class="pin-input-container">
            <input type="password" maxlength="1" class="pin-box" id="pin1" autofocus>
            <input type="password" maxlength="1" class="pin-box" id="pin2">
            <input type="password" maxlength="1" class="pin-box" id="pin3">
            <input type="password" maxlength="1" class="pin-box" id="pin4">
        </div>`,
        confirmButtonText: 'Verify PIN',
        focusConfirm: false,
        preConfirm: () => {
            const p1 = document.getElementById('pin1').value;
            const p2 = document.getElementById('pin2').value;
            const p3 = document.getElementById('pin3').value;
            const p4 = document.getElementById('pin4').value;
            if (!p1 || !p2 || !p3 || !p4) {
                Swal.showValidationMessage('Please enter all 4 digits');
                return false;
            }
            return p1 + p2 + p3 + p4;
        },
        didOpen: () => setupPinInputNavigation()
    });

    if (enteredPin) {
        const formData = new FormData();
        formData.append('pin', enteredPin);
        try {
            const response = await fetch('verify-pin.php', { method: 'POST', body: formData });
            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: '4-PIN Correct!',
                    showConfirmButton: false,
                    timer: 1200
                }).then(() => {
                    window.location.href = 'change-pass.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Incorrect PIN',
                    text: 'Please try again.',
                    confirmButtonColor: '#d33'
                });
            }
        } catch (error) {
            Swal.fire('Error', 'Server connection failed', 'error');
        }
    }
});

// ✅ Helper: PIN input auto navigation
function setupPinInputNavigation() {
    const inputs = Array.from(document.querySelectorAll('.pin-box'));
    inputs.forEach((input, idx) => {
        input.addEventListener('input', () => {
            if (input.value.length === 1 && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && input.value === '' && idx > 0) {
                inputs[idx - 1].focus();
            }
        });
    });
}

  document.getElementById("logoutBtn").addEventListener("click", function (e) {
  e.preventDefault();
  Swal.fire({
    title: "Are you sure you want to logout?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes",
    cancelButtonText: "No",
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "logout.php";
    }
  });
});


</script>

</body>
</html>