<?php
require_once 'admin-init.php';

// CORRECTED: Fetch all pending requests from the user_requests table
$stmt = $conn->prepare("SELECT id, user_email, request_type, subject, created_at FROM user_requests WHERE status = 'pending' ORDER BY created_at DESC");
$stmt->execute();
$requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Requests - Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-layout">
        <?php include 'admin-sidebar.php'; ?>
        <div class="main-content">
            <header class="header">
                <h1>User Requests</h1>
            </header>
            <section class="dashboard-main-area">
                <div class="data-table-card">
                    <div class="card-title"><i class="fas fa-envelope-open-text"></i> Pending Requests</div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>From</th>
                                    <th>Request Type</th>
                                    <th>Subject</th>
                                    <th>Received</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($requests)): ?>
                                    <?php foreach ($requests as $request): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($request['user_email']) ?></td>
                                            <td>
                                                <?php
                                                if ($request['request_type'] == 'PIN_RESET') {
                                                    echo '<span class="status-badge status-inactive">PIN Reset</span>';
                                                } elseif ($request['request_type'] == 'PASSWORD_RESET') {
                                                    echo '<span class="status-badge status-pending">Password Reset</span>';
                                                } else {
                                                    echo '<span class="status-badge status-active">General</span>';
                                                }
                                                ?>
                                            </td>
                                            <td><?= htmlspecialchars($request['subject']) ?></td>
                                            <td><?= date('F j, Y, g:i a', strtotime($request['created_at'])) ?></td>
                                            <td><a href="admin-view-request.php?id=<?= $request['id'] ?>" class="action-link">View Details</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="no-data">No pending requests.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>

</html>