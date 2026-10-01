<?php
session_start();
include("connect.php");
if (!isset($_SESSION['email'])) {
  header("Location: index.php");
  exit();
}
$email = $_SESSION['email'];
$q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$user = mysqli_fetch_assoc($q);
// Fetch user's validation record
$user_id = $user['id'];
$query = mysqli_query($conn, "SELECT * FROM verifications WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1");
$validation = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Validation Form</title>
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="icon" href="imgs/logo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <style>
    :root {
      --primary-color: #007bff;
      --secondary-color: #6c757d;
      --success-color: #28a745;
      --warning-color: #ffc107;
      --danger-color: #dc3545;
      --light-bg: #f8f9fa;
      --white: #fff;
      --shadow: 0 4px 12px rgba(0,0,0,0.1);
      --border-radius: 12px;
      --transition: all 0.3s ease;
    }
    body {
      background: linear-gradient(135deg, var(--light-bg) 0%, #e9ecef 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }
    .container {
      max-width: 900px;
      margin: 60px auto;
      background: var(--white);
      padding: 40px;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
    }
    .container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, var(--primary-color), #0056b3);
    }
    h2 {
      text-align: center;
      margin-bottom: 35px;
      color: var(--primary-color);
      font-weight: 600;
      position: relative;
    }
    h2 i {
      margin-right: 10px;
      color: var(--primary-color);
    }
    h2::after {
      content: '';
      display: block;
      width: 50px;
      height: 3px;
      background: var(--primary-color);
      margin: 10px auto 0;
      border-radius: 2px;
    }
    .info {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 30px;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 10px;
      border-left: 4px solid var(--primary-color);
      transition: var(--transition);
    }
    .info:hover {
      box-shadow: 0 2px 8px rgba(0,123,255,0.1);
    }
    .info img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--primary-color);
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .details {
      line-height: 1.6;
      flex: 1;
    }
    .details strong {
      font-size: 1.2em;
      color: #333;
      display: block;
    }
    .details small {
      color: var(--secondary-color);
      font-size: 0.95em;
    }
     .validation-details {
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      border: 1px solid #e9ecef;
      margin-bottom: 30px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .validation-details p {
      margin-bottom: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px solid #f1f3f4;
    }
    .validation-details p:last-child {
      border-bottom: none;
      margin-bottom: 0;
    }
    .validation-details strong {
      color: #333;
      min-width: 120px;
      font-weight: 500;
    }
    .status-badge {
      display: inline-block;
      padding: 8px 16px;
      border-radius: 25px;
      font-weight: 600;
      font-size: 14px;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: var(--transition);
    }
    .status-badge:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .pending { background-color: var(--warning-color); color: #212529; }
    .approved { background-color: var(--success-color); }
    .rejected { background-color: var(--danger-color); }
    .id-images {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 25px;
      margin-top: 30px;
    }
    .id-card {
      text-align: center;
      background: #f8f9fa;
      padding: 20px;
      border-radius: 10px;
      border: 1px solid #e9ecef;
      transition: var(--transition);
    }
    .id-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .id-card strong {
      display: block;
      margin-bottom: 15px;
      color: var(--primary-color);
      font-size: 1.1em;
    }
    .id-card img {
      width: 100%;
      max-width: 250px;
      height: auto;
      border-radius: 8px;
      border: 2px solid #ddd;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transition: var(--transition);
    }
    .id-card img:hover {
      transform: scale(1.02);
    }
    .no-validation {
      text-align: center;
      color: var(--secondary-color);
      font-size: 18px;
      padding: 40px;
      background: #f8f9fa;
      border-radius: 10px;
      border: 2px dashed #dee2e6;
      margin: 20px 0;
    }
    .no-validation i {
      font-size: 48px;
      color: #adb5bd;
      margin-bottom: 15px;
      display: block;
    }
     .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-top: 30px;
      text-decoration: none;
      background: linear-gradient(135deg, var(--primary-color), #0056b3);
      color: var(--white);
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: 500;
      transition: var(--transition);
      box-shadow: 0 2px 6px rgba(0,123,255,0.3);
      border: none;
      cursor: pointer;
    }
    .back-btn:hover {
      background: linear-gradient(135deg, #0056b3, #004085);
      color: var(--white);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,123,255,0.4);
    }
    /* Responsive Design */
    @media (max-width: 768px) {
      .container {
        margin: 20px;
        padding: 20px;
      }
      .info {
        flex-direction: column;
        text-align: center;
        gap: 15px;
      }
      .info img {
        width: 70px;
        height: 70px;
      }
      .id-images {
        grid-template-columns: 1fr;
        gap: 20px;
      }
      .validation-details p {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
      }
      .validation-details strong {
        min-width: auto;
      }
    }
    /* Loading/Animation */
    .fade-in {
      animation: fadeIn 0.5s ease-in;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
<div class="container fade-in">
  <h2><i class="fa-solid fa-id-card"></i> My Validation Form</h2>
  <div class="info">
    <img src="<?php echo !empty($user['profile']) ? $user['profile'] : 'imgs/default.png'; ?>" alt="Profile">
    <div class="details">
      <strong><?php echo $user['firstName'] . ' ' . $user['lastName']; ?></strong><br>
      <small><?php echo $user['email']; ?></small>
    </div>
  </div>
  <?php if ($validation): ?>
    <div class="validation-details">
      <p><strong>Full Name:</strong> <span><?php echo htmlspecialchars($validation['full_name']); ?></span></p>
      <p><strong>Birthdate:</strong> <span><?php echo htmlspecialchars($validation['birthdate']); ?></span></p>
      <p><strong>Age:</strong> <span><?php echo htmlspecialchars($validation['age']); ?></span></p>
      <p><strong>Gender:</strong> <span><?php echo htmlspecialchars($validation['gender']); ?></span></p>
      <p><strong>Address:</strong> <span><?php echo htmlspecialchars($validation['address']); ?></span></p>
      <p><strong>ID Type:</strong> <span><?php echo htmlspecialchars($validation['id_type']); ?></span></p>
      <p><strong>ID Number:</strong> <span><?php echo htmlspecialchars($validation['id_number']); ?></span></p>
      <p><strong>Status:</strong>
        <?php
          $status = strtolower($validation['status']);
          echo "<span class='status-badge $status'>" . ucfirst($status) . "</span>";
        ?>
      </p>
      <div class="id-images">
        <div class="id-card">
          <strong>ID Image</strong>
          <img src="<?php echo $validation['id_image']; ?>" alt="ID Image">
        </div>
        <div class="id-card">
          <strong>Selfie with ID</strong>
          <img src="<?php echo $validation['selfie_image']; ?>" alt="Selfie with ID">
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="no-validation">
      <i class="fa-solid fa-user-check"></i>
      You have not submitted any validation form yet.
    </div>
  <?php endif; ?>
  <a href="my-account.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Go back</a>
</div>
</body>
</html>