<?php
session_start();
include("connect.php");
if (!isset($_SESSION['email'])) {
  header("Location: index.php");
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
        <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32" fill="#1f1f1f">
          <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z" />
        </svg>
      </label>
      <label id="overlay" for="sidebar-active"></label>

      <div class="links-container">
        <label for="sidebar-active" class="close-sidebar-button">
          <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32" fill="#1f1f1f">
            <path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z" />
          </svg>
        </label>
        <a class="home-link active" href="homepage.php">Home</a>
        <a href="loggedin-about.php">About us</a>
        <a href="loggedin-services.php">Services</a>
        <a href="loggedin-contact.php">Contact us</a>
        <a href="my-account.php">My account</a>
      </div>
    </nav>
  </header>

  <section class="qch-hero-section">
    <div class="qch-container">
      <div class="qch-secure-tag">
        <i class="fas fa-check-circle"></i> Secure & Trusted
      </div>
      <h2 class="qch-hero-title text-light">Get the Funds You Need,<br>When You Need Them</h2>
      <p class="qch-hero-description">Fast approval on personal loans from ₱1,000 to ₱50,000. Simple application process, flexible terms, and competitive rates designed with you in mind.</p>

      <div class="qch-cta-buttons">
        <a href="my-account.php" class="qch-btn qch-btn-primary">
          <i class="fas fa-rocket"></i> Get Started Now
        </a>
        <a href="loggedin-about.php" class="qch-btn qch-btn-secondary">
          <i class="fas fa-info-circle"></i> Learn More
        </a>
      </div>

      <hr class="qch-divider">

      <div class="qch-stats-section">
        <div class="qch-stat-item">
          <p class="qch-stat-number">₱50k</p>
          <p class="qch-stat-label">Maximum Loan</p>
        </div>
        <div class="qch-stat-item">
          <p class="qch-stat-number">24h</p>
          <p class="qch-stat-label">Fast Approval</p>
        </div>
        <div class="qch-stat-item">
          <p class="qch-stat-number">10k</p>
          <p class="qch-stat-label">People trusted and used it</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <link rel="stylesheet" href="style.css">
  </head>

  <body>

    <!-- How It Works Section -->
    <section class="how-it-works">
      <h2>How It Works</h2>
      <p class="subtitle">Get your loan in four simple steps. Fast, secure, and hassle-free.</p>

      <div class="steps">
        <div class="step">
          <div class="icon-circle">
            <span>1</span>
            <i class="fa-solid fa-user icon"></i>
          </div>
          <h3 class="h3-text">Create Account</h3>
          <p>Sign up with your basic information and verify your email address.</p>
        </div>

        <div class="step">
          <div class="icon-circle">
            <span>2</span>
            <i class="fa-solid fa-id-card icon"></i>
          </div>
          <h3 class="h3-text">Get Validated</h3>
          <p>Submit your occupation and income details for quick validation.</p>
        </div>

        <div class="step">
          <div class="icon-circle">
            <span>3</span>
            <i class="fa-solid fa-file-lines icon"></i>
          </div>
          <h3 class="h3-text">Apply for Loan</h3>
          <p>Choose your loan amount and repayment terms that work for you.</p>
        </div>

        <div class="step">
          <div class="icon-circle">
            <span>4</span>
            <i class="fa-solid fa-circle-check icon"></i>
          </div>
          <h3 class="h3-text">Receive Funds</h3>
          <p>Get approved within 24 hours and receive funds directly to your account.</p>
        </div>
      </div>
    </section>


    <section class="content-section why-choose-section">
      <div class="container text-center">
        <h2 class="section-title">Why Choose MemenLoan?</h2>
        <p class="section-subtitle">
          We're committed to providing you with the best loan experience through innovation and customer care.
        </p>

        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-bolt"></i></div>
            <div class="feature-h5">
              <h5 class="feature-title">Fast Approval</h5>
              <p class="feature-desc">Get your loan approved within 24 hours with our streamlined process.</p>
            </div>
          </div>
          <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="feature-h5">
              <h5 class="feature-title">Secure & Safe</h5>
              <p class="feature-desc">Your data is protected with bank-level encryption and security measures.</p>
            </div>
          </div>
          <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="feature-h5">
              <h5 class="feature-title">Flexible Terms</h5>
              <p class="feature-desc">Choose repayment schedules that fit your budget and lifestyle.</p>
            </div>
          </div>
          <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-percent"></i></div>
            <div class="feature-h5">
              <h5 class="feature-title">Competitive Rates</h5>
              <p class="feature-desc">Enjoy low interest rates and transparent pricing with no hidden fees.</p>
            </div>
          </div>
          <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-headset"></i></div>
            <div class="feature-h5">
              <h5 class="feature-title">24/7 Support</h5>
              <p class="feature-desc">Our dedicated team is always ready to assist you anytime, anywhere.</p>
            </div>
          </div>
          <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-mobile-alt"></i></div>
            <div class="feature-h5">
              <h5 class="feature-title">Easy Access</h5>
              <p class="feature-desc">Manage your loans on any device with our user-friendly platform.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <style>
      .testimonials-section {
        background: #f8f9fa;
        padding: 60px 0;
        text-align: center;
      }

      .testimonial-list {
        display: flex;
        flex-direction: column;
        gap: 30px;
        margin-top: 40px;
      }

      .testimonial-card {
        display: flex;
        align-items: center;
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
      }

      .testimonial-card:hover {
        transform: translateY(-4px);
      }

      .testimonial-img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 20px;
      }

      .testimonial-text {
        text-align: left;
      }

      .testimonial-text p {
        font-style: italic;
        margin: 8px 0;
        color: #333;
      }

      .testimonial-author {
        font-weight: bold;
        color: #1f1f1f;
      }

      .stars {
        color: #fbc02d;
        margin-bottom: 6px;
      }

      @media (max-width: 768px) {
        .testimonial-card {
          flex-direction: column;
          text-align: center;
        }

        .testimonial-img {
          margin-right: 0;
          margin-bottom: 12px;
        }

        .testimonial-text {
          text-align: center;
        }
      }
    </style>

    <section class="testimonials-section">
      <div class="container">
        <h2 class="section-title">What Our Customers Say</h2>
        <p class="section-subtitle">Real experiences from people who trusted us.</p>

        <div class="testimonial-list">

          <!-- Testimonial 1 -->
          <div class="testimonial-card">
            <img src="imgs/Mark Lopez.jpg" alt="Customer 1" class="testimonial-img">
            <div class="testimonial-text">
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
              </div>
              <p>"MemenLoan made applying for a loan so simple and stress-free! Highly recommend."</p>
              <span class="testimonial-author">— Mark Lopez</span>
            </div>
          </div>

          <!-- Testimonial 2 -->
          <div class="testimonial-card">
            <img src="imgs/Jane Dela Cruz.jpg" alt="Customer 2" class="testimonial-img">
            <div class="testimonial-text">
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
              </div>
              <p>"Fast, reliable, and the support team was very helpful. Definitely satisfied!"</p>
              <span class="testimonial-author">— Jane Dela Cruz</span>
            </div>
          </div>

          <!-- Testimonial 3 -->
          <div class="testimonial-card">
            <img src="imgs/Carlos Reyes.jpg" alt="Customer 3" class="testimonial-img">
            <div class="testimonial-text">
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
                <i class="far fa-star"></i>
              </div>
              <p>"I was impressed with the security and speed of their services. Highly trustworthy."</p>
              <span class="testimonial-author">— Carlos Reyes</span>
            </div>
          </div>

        </div>
      </div>
    </section>

    <section class="services">
      <h2>Our Services</h2>
      <p class="subtitle">Tailored loan solutions to meet your unique financial needs.</p>

      <div class="service-boxes">
        <div class="service">
          <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
          <h3 class="text-dark">Personal Loans</h3>
          <p>Quick access to ₱1,000 - ₱50,000 for any personal need</p>
        </div>

        <div class="service">
          <div class="icon"><i class="fas fa-clock"></i></div>
          <h3 class="text-dark">Emergency Loans</h3>
          <p>Same-day approval for urgent financial requirements</p>
        </div>

      </div>

      <a href="loggedin-services.php"><button class="view-btn">View All Services <i class="fas fa-arrow-right"></i></button></a>
    </section>

    <script>
      const slidesContainer = document.querySelector('.slides-container');
      const slides = document.querySelectorAll('.slide');
      const prevBtn = document.querySelector('.prev');
      const nextBtn = document.querySelector('.next');

      let currentIndex = 0;

      function showSlide(index) {
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        slidesContainer.style.transform = `translateX(-${index * 100}%)`;
        currentIndex = index;
      }

      prevBtn.addEventListener('click', () => showSlide(currentIndex - 1));
      nextBtn.addEventListener('click', () => showSlide(currentIndex + 1));
    </script>
    <?php
    include "footer2.php";
    ?>