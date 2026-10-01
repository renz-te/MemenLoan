<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="icon" href="imgs/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        :root{
            --second: #007bff;
            --main: #0056b3;
            --third: rgb(0, 149, 255);
            --font-family: 'Poppins', sans-serif; /* Changed to Poppins as it is imported */
        }

        body {
            font-family: var(--font-family);
            background: linear-gradient(135deg, #f7f9fc, #e8f0fe);
            color: var(--text-color);
        }

    header{
        position: fixed;
        height: 65px;
        width: 100%;
        padding: 1rem 9%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #007bff;
        box-shadow: 0 0 10px black;
        z-index: 9999;
    }

    nav{
        height: 60px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }
    .links-container{
        height: 100%;
        width: 100%;
        display: flex;
        flex-direction: row;
        align-items: center;
    }
    nav a{
        position: relative;
        font-size: 18px;
        color: white;
        margin: 0 23px;
        text-decoration: none;
        font-weight: 500;
        transition: 0.3s linear;
    }
    nav a::before{
        position: absolute;
        content: "";
        bottom: -3px;
        left: 0;
        width: 0%;
        height: 3px;
        background-color: var(--third);
        transition: 0.2s linear;

    }

    header .active{
        color: var(--second);
    }

    nav svg{
        fill: white;
    }
    #sidebar-active{
        display: none;
    }
    .open-sidebar-button,
    .close-sidebar-button{
        display: none;

    }


    @keyframes slideright{
        0%{
            transform: translateX(-100px);
            opacity: 0;
        }
        100%{
            transform: translateX(0px);
            opacity: 1;
        }
    }

    @keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOutLeft {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(-100%); opacity: 0; }
    }

    @keyframes slideInLeft {
        from { transform: translateX(-100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }

    header .logo{
        color: var(--main);
        cursor: pointer;
        font-size: 3rem;
        transition: .3s ease;
        height: 3rem;
        width: 3rem;
        border-radius: 20%;

    }
    header .logo:hover{
        transform: scale(1.1);
    }

    .lm{
        display: flex;
        gap: 5px;
    }

    .lm a{
        font-size: 25px;
        color: White;
        text-decoration: none;

    }
    @media (max-width: 2000px){
    .links-container{
        flex-direction: column;
        align-items: flex-start;
        position: fixed;
        top: 0;
        right: -100%;
        z-index: 10;
        background: rgba(0, 0, 0, 0.493);
        backdrop-filter: blur(10px);
        transition: .5s ease-out;
    }
    nav a{
        box-sizing: border-box;
        height: auto;
        width: 100%;
        padding: 30px 40px;
        justify-content: flex-start;
    }
    .open-sidebar-button,
    .close-sidebar-button{
        display: block;
        padding: 20px;
    }
    #sidebar-active:checked ~ .links-container{
        right: -65%;
    }
    #sidebar-active:checked ~ #overlay{
        height: 100%;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9;
    }
}
 

@media (max-width: 1100px){
    .links-container{
        flex-direction: column;
        align-items: flex-start;
        position: fixed;
        top: 0;
        right: -100%;
        z-index: 10;
        background: rgba(0, 0, 0, 0.493);
        backdrop-filter: blur(10px);
        transition: .5s ease-out;
    }
    nav a{
        box-sizing: border-box;
        height: auto;
        width: 100%;
        padding: 30px 40px;
        justify-content: flex-start;
    }
    .open-sidebar-button,
    .close-sidebar-button{
        display: block;
        padding: 20px;
    }
    #sidebar-active:checked ~ .links-container{
        right: -60%;
    }
    #sidebar-active:checked ~ #overlay{
        height: 100%;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9;
    }
}

@media (max-width: 1000px){
    .links-container{
        flex-direction: column;
        align-items: flex-start;
        position: fixed;
        top: 0;
        right: -100%;
        z-index: 10;
        background: rgba(0, 0, 0, 0.493);
        backdrop-filter: blur(10px);
        transition: .5s ease-out;
    }
    nav a{
        box-sizing: border-box;
        height: auto;
        width: 100%;
        padding: 30px 40px;
        justify-content: flex-start;
    }
    .open-sidebar-button,
    .close-sidebar-button{
        display: block;
        padding: 20px;
    }
    #sidebar-active:checked ~ .links-container{
        right: -60%;
    }
    #sidebar-active:checked ~ #overlay{
        height: 100%;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9;
    }
}
@media (max-width: 900px){
    .links-container{
        flex-direction: column;
        align-items: flex-start;
        position: fixed;
        top: 0;
        right: -100%;
        z-index: 10;
        background: rgba(0, 0, 0, 0.493);
        backdrop-filter: blur(10px);
        transition: .5s ease-out;
    }
    nav a{
        box-sizing: border-box;
        height: auto;
        width: 100%;
        padding: 30px 40px;
        justify-content: flex-start;
    }
    .open-sidebar-button,
    .close-sidebar-button{
        display: block;
        padding: 20px;
    }
    #sidebar-active:checked ~ .links-container{
        right: -60%;
    }
    #sidebar-active:checked ~ #overlay{
        height: 100%;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9;
    }

}
@media (max-width: 800px){
    .links-container{
        flex-direction: column;
        align-items: flex-start;
        position: fixed;
        top: 0;
        right: -100%;
        z-index: 10;
        background: rgba(0, 0, 0, 0.493);
        backdrop-filter: blur(10px);
        transition: .5s ease-out;
    }
    nav a{
        box-sizing: border-box;
        height: auto;
        width: 100%;
        padding: 30px 40px;
        justify-content: flex-start;
    }
    .open-sidebar-button,
    .close-sidebar-button{
        display: block;
        padding: 20px;
    }
    #sidebar-active:checked ~ .links-container{
        right: -50%;
    }
    #sidebar-active:checked ~ #overlay{
        height: 100%;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9;
    }

}

@media (max-width: 769px){
    .sidebar{
      padding-top: 4.5rem;
      display: flex;
      position: relative;
      width: 100%;
      z-index: 999;
      height: 210px;
    }
    .sidebar .menu{
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
      gap: 1px;
    }
    .sidebar .menu .logout-btn{
      width: auto;
    }
    .dashboard-container{
      display: block;
     
    }
    .profile{
      display: none;
    }
    .info-card{
      padding: 1px 1px;
    }
 
}

@media (max-width: 700px){

    .links-container{
        flex-direction: column;
        align-items: flex-start;
        position: fixed;
        top: 0;
        right: -100%;
        z-index: 10;

        background: rgba(0, 0, 0, 0.493);
        backdrop-filter: blur(10px);
        transition: .5s ease-out;
    }
    nav a{
        box-sizing: border-box;
        height: auto;
        width: 100%;
        padding: 30px 40px;
        justify-content: flex-start;
    }
    .open-sidebar-button,
    .close-sidebar-button{
        display: block;
        padding: 20px;
    }
    #sidebar-active:checked ~ .links-container{
        right: -50%;
    }
    #sidebar-active:checked ~ #overlay{
        height: 100%;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9;
    }
    
    
}
@media (max-width: 600px){

    .links-container{
        flex-direction: column;
        align-items: flex-start;
        position: fixed;
        top: 0;
        right: -100%;
        z-index: 10;

        background: rgba(0, 0, 0, 0.493);
        backdrop-filter: blur(10px);
        transition: .5s ease-out;
    }
    nav a{
        box-sizing: border-box;
        height: auto;
        width: 100%;
        padding: 30px 40px;
        justify-content: flex-start;
    }
    .open-sidebar-button,
    .close-sidebar-button{
        display: block;
        padding: 20px;
    }
    #sidebar-active:checked ~ .links-container{
        right: -35%;
    }
    #sidebar-active:checked ~ #overlay{
        height: 100%;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9;
        }
    }

    header .logo{
        color: var(--main);
        cursor: pointer;
        font-size: 3rem;
        transition: .3s ease;
    }
    header .logo:hover{
        transform: scale(1.1);
    }


        .login-form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 123, 255, 0.2);
            /* Increased size here */
            width: 450px; 
            transition: box-shadow 0.3s ease;
        }
        
        .form-wrapper {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--main);
            font-size: 1.8rem;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: #007bff;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e3f2fd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }
        input[type="password"]:focus, input[type="text"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        .password-container {
            position: relative;
        }
        .toggle-eye, .toggle-eye2, .toggle-eye3 {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1.3rem;
            color: #007bff;
            transition: color 0.3s ease;
        }
        .toggle-eye:hover, .toggle-eye2:hover, .toggle-eye3:hover {
            color: #0056b3;
            transform: translateY(-50%) scale(1.1);
        }
        
        /* New Button Group Styles for Side-by-Side Layout */
        .button-group {
            display: flex;
            justify-content: space-between;
            gap: 15px; /* Space between buttons */
            margin-bottom: 0; /* Remove extra margin if needed */
        }

        /* Green Style for Submit Button */
        .submit-button {
            /* width removed, flex handles width */
            flex-grow: 1; /* Allows button to take up available space */
            padding: 1rem;
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); 
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background 0.3s ease, box-shadow 0.3s ease; 
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
            /* Removed bottom margin */
        }
        .submit-button:hover {
            background: linear-gradient(135deg, #1e7e34 0%, #155724 100%);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.6);
        }

        /* Red Style for Back Button */
        .back-to-account-button {
            /* display: block removed */
            /* width removed, flex handles width */
            flex-grow: 1; /* Allows button to take up available space */
            padding: 1rem;
            text-align: center;
            background: linear-gradient(135deg, #dc3545 0%, #bd2130 100%); 
            color: white; 
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease; 
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        }
        .back-to-account-button:hover {
            background: linear-gradient(135deg, #bd2130 0%, #a71d2a 100%);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.6);
        }
        
        /* Ensure SweetAlert appears above everything */
        .swal2-container {
            z-index: 9999 !important;
            font-family: 'Poppins', sans-serif;
        }
        .swal2-popup {
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        .swal2-title {
            color: #007bff;
            font-weight: 600;
        }
 
         </style>
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
            <a class="home-link active" href="homepage.php">Home</a>
            <a href="loggedin-about.php">About us</a>
            <a href="loggedin-services.php">Services</a>
            <a href="loggedin-contact.php">Contact us</a>
            <a href="my-account.php">My account</a>
        </div>
    </nav>
    </header>
        <div class="form-wrapper">
            <form class="login-form" id="changePasswordForm" method="POST">
                <h1 class="form-header">Change Password</h1> <div class="form-group">
  <label for="currentPassword">Current password</label>
  <div class="password-container">
    <input type="password" id="currentPassword" name="currentPassword" required>
    <i class="fa-solid fa-eye toggle-eye3" onclick="togglePassword3()"></i>
  </div>
</div>

<div class="form-group">
  <label for="newPassword">New password</label>
  <div class="password-container">
    <input type="password" id="newPassword" name="newPassword" required>
    <i class="fa-solid fa-eye toggle-eye" onclick="togglePassword()"></i>
  </div>
</div>

<div class="form-group">
  <label for="confirmPassword">Confirm password</label>
  <div class="password-container">
    <input type="password" id="confirmPassword" name="confirmPassword" required>
    <i class="fa-solid fa-eye toggle-eye2" onclick="togglePassword2()"></i>
  </div>
</div>

    <div class="button-group"> <a href="my-account.php" class="back-to-account-button">Back to My account</a> 
        <button class="submit-button" type="submit">Change Password</button>
    </div>
</form>
        </div>
<script>
function togglePassword() {
  const input = document.getElementById('newPassword');
  const eye = document.querySelector('.toggle-eye');
  const isPassword = input.type === 'password';
  input.type = isPassword ? 'text' : 'password';
  eye.classList.toggle('fa-eye', !isPassword);
  eye.classList.toggle('fa-eye-slash', isPassword);
}

function togglePassword2() {
  const input = document.getElementById('confirmPassword');
  const eye = document.querySelector('.toggle-eye2');
  const isPassword = input.type === 'password';
  input.type = isPassword ? 'text' : 'password';
  eye.classList.toggle('fa-eye', !isPassword);
  eye.classList.toggle('fa-eye-slash', isPassword);
}

function togglePassword3() {
  const input = document.getElementById('currentPassword');
  const eye = document.querySelector('.toggle-eye3');
  const isPassword = input.type === 'password';
  input.type = isPassword ? 'text' : 'password';
  eye.classList.toggle('fa-eye', !isPassword);
  eye.classList.toggle('fa-eye-slash', isPassword);
}
document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    if (newPassword !== confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'New password and confirm password do not match!',
            position: 'center',
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            scrollbarPadding: false
        });
        return;
    }
    try {
        const formData = new FormData();
        formData.append('currentPassword', currentPassword);
        formData.append('newPassword', newPassword);
        const response = await fetch('change-password.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Password updated successfully!',
                position: 'center',
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                scrollbarPadding: false
            }).then(() => {
                document.getElementById('changePasswordForm').reset();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: result.message,
                position: 'center',
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                scrollbarPadding: false
            });
        }
    } catch (err) {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred while updating password.',
            position: 'center',
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            scrollbarPadding: false
        });
    }
});
</script>
</body>
</html>