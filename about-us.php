<?php
session_start();
include("connect.php");
if (isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MemenLoan - Get the Funds You Need</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="system8.css">
    <link rel="icon" href="imgs/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
 
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
            <a class="home-link" href="index.php">Home</a>
            <a class="active" href="about-us.php">About us</a>
            <a href="services.php">Services</a>
            <a href="contact.php">Contact us</a>
            <a href="login.php">Login</a>
        </div>
    </nav>
    </header>
    <body>
        
    <section class="section-body">
        <div class="qc-header-container">
            <h1>About MemenLoan</h1>
            <p>Your trusted partner in achieving financial freedom and stability.</p>
        </div>
    </div>

  <!-- What We Do -->
  <section class="sec2">
    <div class="container flex-row">
      <div class="text-box">
        <h2>Our Mission</h2>
        <p>
          At MemenLoan, we believe everyone deserves access to fast, fair, and transparent financial
          services. Founded in 2025, we've helped hundread of Filipinos overcome financial challenges
          and achieve their goals through our innovative lending platform.
        </p>
      </div>
            <div class="img-box circle">
        <img src="imgs/hu1.jpg" alt="Market vendor" />
      </div>
    </div>
  </section>

  <!-- How We Do It -->
  <section class="sec3">
    <div class="container flex-row reverse">
      <div class="img-box">
        <img src="imgs/hu2.jpg" alt="Loan app on phone" />
      </div>
      <div class="text-box">
        <h2>Who We Are</h2>
        <p>
          We are a team of financial experts, technologists, and customer service professionals dedicated
          to revolutionizing personal lending in the Philippines. Our platform combines cutting-edge
          technology with personalized service to deliver a lending experience that's fast, secure, and
          tailored to your needs.
        </p>
      </div>
    </div>
  </section>

  <section class="about-section">
  <div class="about-container">

    <div class="about-text">
      <h2>What We Offer</h2>
      <p>
       QuickCash provides personal loans ranging from <strong> ₱1,000 to ₱50,000 </strong>with flexible repayment
       terms. Our streamlined application process and validation system ensure that qualified borrowers
       can access funds within 24 hours. Whether you need money for education, medical expenses,
       home improvements, or any personal need, <br> we're here to help.
      </p>
    </div>
        <div class="about-image">
      <img src="imgs/hu3.jpg" alt="Why We Do It">
    </div>
  </div>
</section>

<section class="team-section">
  <div>
    <h2 class="text-center text-dark">Meet our team</h1>
    <p class="text-dark text-center">Dedicated professionals committed to helping you achieve your financial goals.</p>
  </div>
<div class="team-members-grid">
    <div class="team-member-card">
        <img src="imgs/ami.jpg" alt="Team Member 1 - Chief Executive Officer" class="member-photo">
        <div class="member-details">
            <p class="name">Christian Ami</p>
            <p class="title">Front-End Developer</p>
        </div>
    </div>

    <div class="team-member-card">
        <img src="imgs/kyle.jpg" alt="Team Member 2 - Chief Financial Officer" class="member-photo">
        <div class="member-details">
            <p class="name">Kyle Pingul</p>
            <p class="title">Full Stack Developer</p>
        </div>
    </div>

    <div class="team-member-card">
        <img src="imgs/gizmo.jpg" alt="Team Member 3 - Chief Operations Officer" class="member-photo">
        <div class="member-details">
            <p class="name">Gerald Diezmo</p>
            <p class="title">Full Stack Developer</p>
        </div>
    </div>

    <div class="team-member-card">
        <img src="imgs/memen.jpg" alt="Team Member 4 - Head of Customer Service" class="member-photo">
        <div class="member-details">
            <p class="name">Terrence Danlag</p>
            <p class="title">Back-End Developer</p>
        </div>
    </div>

    <div class="team-member-card">
        <img src="imgs/kevin.jpg" alt="Team Member 5 - Senior Loan Specialist" class="member-photo">
        <div class="member-details">
            <p class="name">John Kevin Calibuso</p>
            <p class="title">Front-End Developer</p>
        </div>
    </div>
</div>
</section>

<section class="commitment-section">
    <div class="commitment-container">
        <h2 class="commitment-heading">Our Commitment to You</h2>
        <div class="commitment-grid">
            <div class="commitment-item">
                <i class="fas fa-check-circle commitment-icon"></i>
                <p><strong>Fast Processing:</strong> We value your time. Our automated validation and approval system ensures quick turnaround times.</p>
            </div>
            <div class="commitment-item">
                <i class="fas fa-check-circle commitment-icon"></i>
                <p><strong>Fair Rates:</strong> We offer competitive interest rates based on your profile, ensuring affordability and transparency.</p>
            </div>
            <div class="commitment-item">
                <i class="fas fa-check-circle commitment-icon"></i>
                <p><strong>24/7 Support:</strong> Our dedicated customer service team is always available to assist you with any questions or concerns.</p>
            </div>
            <div class="commitment-item">
                <i class="fas fa-check-circle commitment-icon"></i>
                <p><strong>Responsible Lending:</strong> We promote financial literacy and responsible borrowing practices to ensure long-term financial health.</p>
            </div>
        </div>
    </div>
</section>

<?php
 include "footer2.php";
?>
