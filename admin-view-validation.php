<?php
require_once 'admin-init.php';

// Check if an ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin-validation.php");
    exit;
}

$validation_id = $_GET['id'];

// Fetch the specific validation record
$stmt = $conn->prepare("SELECT * FROM verifications WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $validation_id);
$stmt->execute();
$validation = $stmt->get_result()->fetch_assoc();

if (!$validation) {
    die("Validation record not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Validation Details</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .detail-item {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
        }

        .detail-item strong {
            display: block;
            color: #555;
            margin-bottom: 5px;
        }

        .detail-item span {
            font-size: 1.1rem;
        }

        .image-viewer {
            text-align: center;
            margin-top: 20px;
        }

        .image-viewer img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }

        .action-buttons {
            margin-top: 30px;
            display: flex;
            gap: 15px;
        }

        .action-buttons a {
            padding: 12px 25px;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .approve-btn {
            background-color: #28a745;
        }

        .deny-btn {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="dashboard-layout">
        <input type="checkbox" id="sidebar-toggle">
        <?php include 'admin-sidebar.php'; ?>
        <div class="main-content">
            <header class="header">
                <h1>Review Validation #<?= htmlspecialchars($validation['id']) ?></h1>
            </header>
            <section class="dashboard-main-area">
                <div class="data-table-card">
                    <div class="card-title">Personal Information</div>
                    <div class="details-grid">
                        <div class="detail-item"><strong>Full Name:</strong> <span><?= htmlspecialchars($validation['full_name']) ?></span></div>
                        <div class="detail-item"><strong>Birthdate:</strong> <span><?= htmlspecialchars($validation['birthdate']) ?></span></div>
                        <div class="detail-item"><strong>Age:</strong> <span><?= htmlspecialchars($validation['age']) ?></span></div>
                        <div class="detail-item"><strong>Gender:</strong> <span><?= htmlspecialchars($validation['gender']) ?></span></div>
                        <div class="detail-item"><strong>ID Type:</strong> <span><?= htmlspecialchars($validation['id_type']) ?></span></div>
                        <div class="detail-item"><strong>ID Number:</strong> <span><?= htmlspecialchars($validation['id_number']) ?></span></div>
                        <div class="detail-item" style="grid-column: 1 / -1;"><strong>Address:</strong> <span><?= htmlspecialchars($validation['address']) ?></span></div>
                    </div>

                    <div class="card-title" style="margin-top: 30px;">Employment Information</div>
                    <div class="details-grid">
                        <div class="detail-item"><strong>Occupation:</strong> <span><?= htmlspecialchars($validation['occupation']) ?></span></div>
                        <div class="detail-item"><strong>Monthly Salary:</strong> <span>₱<?= number_format($validation['monthly_salary'], 2) ?></span></div>
                    </div>

                    <div class="card-title" style="margin-top: 30px;">Submitted Documents</div>
                    <div class="details-grid">
                        <div class="image-viewer"><strong class="d-block">ID Image</strong><img src="<?= htmlspecialchars($validation['id_image']) ?>"></div>
                        <div class="image-viewer"><strong class="d-block">Selfie with ID</strong><img src="<?= htmlspecialchars($validation['selfie_image']) ?>"></div>
                        <div class="image-viewer"><strong class="d-block">Work Evidence</strong><img src="<?= htmlspecialchars($validation['work_evidence']) ?>"></div>
                        <div class="image-viewer"><strong class="d-block">Meralco Bill</strong><img src="<?= htmlspecialchars($validation['bill_meralco']) ?>"></div>
                        <div class="image-viewer"><strong class="d-block">Water Bill</strong><img src="<?= htmlspecialchars($validation['bill_water']) ?>"></div>
                    </div>

                    <div class="action-buttons">
                        + <a href="admin-process-validation.php?id=<?= $validation['id'] ?>&action=approve" class="approve-btn confirm-validation">Approve</a>
                        + <a href="admin-process-validation.php?id=<?= $validation['id'] ?>&action=deny" class="deny-btn confirm-validation">Deny</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.confirm-validation').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                const url = this.href;
                const isApprove = this.classList.contains('approve-btn');
                const actionText = isApprove ? 'approve' : 'deny';

                Swal.fire({
                    title: `Confirm ${actionText}`,
                    text: `Are you sure you want to ${actionText} this validation request?`,
                    icon: isApprove ? 'success' : 'error',
                    showCancelButton: true,
                    confirmButtonColor: isApprove ? '#28a745' : '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: `Yes, ${actionText} it!`
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
</body>

</html>