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
    <title>MemenLoan | Services</title>
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
            <a class="home-link " href="homepage.php">Home</a>
            <a href="loggedin-about.php">About us</a>
            <a class="active" href="loggedin-services.php">Services</a>
            <a href="loggedin-contact.php">Contact us</a>
            <a href="my-account.php">My account</a>
        </div>
    </nav>
    </header>

    <section class="header-section">
        <h1>Our Loan Services</h1>
        <p>Choose from our range of flexible loan products designed to meet your financial needs.</p>
    </section>

   <!-- Our Services Section -->
<section class="Our-services">
    <div class="services-container">
        <div class="service-item">
            <h3>Low-Interest Loans</h3>
            <p>We understand that every peso counts. That’s why our loans come with <strong>competitive low interest rates</strong>, helping you manage your finances without unnecessary burden. Our rates are clearly communicated, ensuring full transparency from application to repayment.</p>
        </div>

        <div class="service-item">
            <h3>Fast and Hassle-Free Transactions</h3>
            <p>Need cash in a hurry? MemenLoan ensures that loan applications are <strong>processed quickly</strong>, allowing you to receive funds in no time. Our streamlined online process eliminates long queues and paperwork, giving you a smooth experience from start to finish.</p>
        </div>

        <div class="service-item">
            <h3>Flexible Repayment Terms</h3>
            <p>We believe in flexibility and understanding. Our loans come with <strong>adjustable payment durations</strong> to fit your schedule. If you’re unable to pay on time, don’t worry! You have a grace period, and only a <strong>small additional interest</strong> is applied per missed payment, keeping repayment manageable.</p>
        </div>

        <div class="service-item">
            <h3>Secure and Reliable Platform</h3>
            <p>Your safety is our priority. All transactions are secure, and our platform ensures that your data is never compromised. Additionally, we provide a <strong>4-PIN security feature</strong>, giving your account an extra layer of protection so only you can access your funds and personal details.</p>
        </div>

        <div class="service-item">
            <h3>User-Friendly Online Experience</h3>
            <p>From registration to loan application, MemenLoan is designed to be <strong>simple and intuitive</strong>. Track your loan status, repayment schedule, and payment history directly from your account anytime, anywhere.</p>
        </div>

        <div class="service-item">
            <h3>Dedicated Customer Support</h3>
            <p>Have questions or concerns? Our <strong>friendly support team</strong> is always ready to assist you. Whether it’s guidance on loan application or repayment queries, we provide reliable assistance to make your experience stress-free.</p>
        </div>
    </div>
</section>

<style>
.Our-services {
    padding: 50px 20px;
    background-color: #f9f9f9;
}

.Our-services .services-container {
    max-width: 1000px;
    margin: 0 auto;
}

.Our-services h2 {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 20px;
    color: #1f1f1f;
}

.Our-services p {
    text-align: center;
    font-size: 1.1rem;
    margin-bottom: 40px;
    color: #555;
}

.service-item {
    background: #fff;
    padding: 25px 20px;
    margin-bottom: 20px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.service-item h3 {
    font-size: 1.5rem;
    color: #1f1f1f;
    margin-bottom: 10px;
}

.service-item p {
    font-size: 1rem;
    color: #555;
    line-height: 1.6;
}
</style>

                <!-- Account valid -->
    <section class="validation-section s">
            <div class="validation-box">
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
                <h2>Account Validation Required</h2>
                <p>To apply for any loan service, you need to validate your account first.</p>
                <a href="validation.php"><button class="register-button">
                    <i class="fas fa-user-plus"></i>Get Validated 
                </button></a>
            </div>
        </section>
          </section>

        <section class="eligibility-section">
            <div class="eligibility-header">
                <h2>Loan Eligibility</h2>
                <p>Understanding our requirements helps ensure a smooth application process.</p>
            </div>
            <div class="eligibility-cards-container">
                <div class="eligibility-card">
                    <div class="eligibility-icon"><i class="fas fa-user-alt"></i></div>
                    <div class="eligibility-content">
                        <h3>Basic Requirements</h3>
                        <p>Must be 18 years or older, Filipino citizen or legal resident, and have a valid government-issued ID.</p>
                    </div>
                </div>
                <div class="eligibility-card">
                    <div class="eligibility-icon"><i class="fas fa-briefcase"></i></div>
                    <div class="eligibility-content">
                        <h3>Employment & Income</h3>
                        <p>Steady employment or business income. Minimum monthly salary varies by loan type. Income verification required.</p>
                    </div>
                </div>
                <div class="eligibility-card">
                    <div class="eligibility-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="eligibility-content">
                        <h3>Loan Amount Calculation</h3>
                        <p>Maximum loan amount is determined based on your monthly salary and occupation. Typically 30-50% of monthly income.</p>
                    </div>
                </div>
            </div>
        </section>
        
                  <!-- FAQ -->
        <section id="faq-sections" class="faq-section">
        <h2 class="faq-title">Frequently Asked Questions</h2>

        <div class="faq-container">
            <div class="faq-item active">
                <div class="faq-question">
                    <h3>What are the eligibility requirements?</h3>
                    <span class="faq-toggle-icon"><i class="fas fa-chevron-up"></i></span>
                </div>
                <div class="faq-answer">
                    <p class="change">You must be at least 18 years old, have a verified occupation, and maintain a minimum monthly salary. Validation is required before loan application.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <h3>How long does approval take?</h3>
                    <span class="faq-toggle-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Standard loans are approved within 24 hours. 
                      Emergency loans may be approved on the same day, 
                      subject to complete documentation.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <h3>What documents do I need?</h3>
                    <span class="faq-toggle-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Valid ID, proof of income (payslip or certificate of employment), 
                      and proof of billing address. Additional documents may be required 
                      for secured loans.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <h3>Can I pay my loan early?</h3>
                    <span class="faq-toggle-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Yes! Early repayment is allowed without penalties. 
                      You may even qualify for interest rate adjustments on 
                      your next loan.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <h3>What if I miss a payment?</h3>
                    <span class="faq-toggle-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">
                    <p>We understand circumstances change. 
                      Contact us immediately if you're facing difficulty. 
                      We offer payment restructuring options to help you stay on track.</p>
                </div>
            </div>
        </div>
    </section>

    <script>
document.addEventListener('DOMContentLoaded', () => {
    const faqItems = document.querySelectorAll('.faq-item');

    // 1. Initialization: Ensure all items are correctly collapsed on load
    faqItems.forEach(item => {
        const answer = item.querySelector('.faq-answer');
        const toggleIcon = item.querySelector('.faq-toggle-icon i');

        // This runs once to fix the initial state from HTML
        if (item.classList.contains('active')) {
            answer.style.maxHeight = answer.scrollHeight + 'px';
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-up');
        } else {
            answer.style.maxHeight = '0';
            toggleIcon.classList.remove('fa-chevron-up');
            toggleIcon.classList.add('fa-chevron-down');
        }
    });


    // 2. Click listener logic
    faqItems.forEach(clickedItem => {
        const question = clickedItem.querySelector('.faq-question');

        question.addEventListener('click', () => {
            const isCurrentlyActive = clickedItem.classList.contains('active');

            // If the clicked item is already active, just close it (toggle off)
            if (isCurrentlyActive) {
                clickedItem.classList.remove('active');
                clickedItem.querySelector('.faq-answer').style.maxHeight = '0';
                clickedItem.querySelector('.faq-toggle-icon i').classList.remove('fa-chevron-up');
                clickedItem.querySelector('.faq-toggle-icon i').classList.add('fa-chevron-down');
                return; // Stop here if we just closed it
            }

            // Loop through all items to close any currently open one (Accordion behavior)
            faqItems.forEach(itemToClose => {
                if (itemToClose.classList.contains('active')) {
                    itemToClose.classList.remove('active');
                    itemToClose.querySelector('.faq-answer').style.maxHeight = '0';
                    itemToClose.querySelector('.faq-toggle-icon i').classList.remove('fa-chevron-up');
                    itemToClose.querySelector('.faq-toggle-icon i').classList.add('fa-chevron-down');
                }
            });

            // Open the clicked item
            clickedItem.classList.add('active');
            const answer = clickedItem.querySelector('.faq-answer');
            answer.style.maxHeight = answer.scrollHeight + 'px';
            
            const toggleIcon = clickedItem.querySelector('.faq-toggle-icon i');
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-up');
        });
    });
});
    </script>

<?php
 include "footer2.php";
?>