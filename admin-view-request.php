<?php
require_once 'admin-init.php';

$request_id = $_GET['id'] ?? null;
if (!$request_id) {
    header("Location: admin-requests.php");
    exit;
}

// Fetch the full request details from the user_requests table
$stmt = $conn->prepare("SELECT * FROM user_requests WHERE id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

if (!$request) {
    die("Request not found.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Request</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        /* Basic styles for the buttons and layout */
        .detail-card {
            padding: 25px;
        }

        .detail-item {
            font-size: 1.1rem;
            margin-bottom: 12px;
        }

        .message-body {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .action-buttons a {
            padding: 10px 20px;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            font-weight: 600;
        }

        .btn-success {
            background-color: #198754;
        }

        .btn-secondary {
            background-color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="dashboard-layout">
        <?php include 'admin-sidebar.php'; ?>
        <div class="main-content">
            <header class="header">
                <h1>Review Request #<?= $request_id ?></h1>
            </header>
            <section class="dashboard-main-area">
                <div class="data-table-card detail-card">
                    <div class="detail-item"><strong>From:</strong> <?= htmlspecialchars($request['user_email']) ?></div>
                    <div class="detail-item"><strong>Request Type:</strong> <?= htmlspecialchars($request['request_type']) ?></div>
                    <hr>

                    <?php if ($request['request_type'] == 'PASSWORD_RESET'): ?>
                        <div class="detail-item"><strong>User's Desired New Password:</strong> <kbd><?= htmlspecialchars($request['desired_password']) ?></kbd></div>
                        <div class="mt-3">
                            <strong>Proof of Identity Submitted:</strong><br>
                            <a href="<?= htmlspecialchars($request['proof_image']) ?>" target="_blank">
                                <img src="<?= htmlspecialchars($request['proof_image']) ?>" style="max-width: 300px; border: 1px solid #ddd; margin-top: 10px;">
                            </a>
                        </div>
                        <hr>
                    <?php endif; ?>

                    <div class="action-buttons mt-4">
                        <?php if ($request['request_type'] == 'PASSWORD_RESET'): ?>
                            <a href="admin-process-request.php?id=<?= $request_id ?>&action=approve_password" class="confirm-request"
                                style="background-color:#198754; color:white; text-decoration:none; padding:10px 15px; border-radius:5px;"
                                data-message="This will update the user's password to their desired one.">Approve & Change Password</a>
                        <?php endif; ?>

                        <a href="admin-process-request.php?id=<?= $request_id ?>&action=mark_resolved" class="confirm-request"
                            style="background-color:#6c757d; color:white; text-decoration:none; padding:10px 15px; border-radius:5px;"
                            data-message="This will only mark the ticket as resolved.">Just Mark as Resolved</a>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // SweetAlert confirmation script
        document.querySelectorAll('.confirm-request').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const url = this.href;
                const message = this.getAttribute('data-message');
                Swal.fire({
                    title: 'Are you sure?',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, proceed!'
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