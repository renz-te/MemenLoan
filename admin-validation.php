<?php
require_once 'admin-init.php';

// Fetch all pending validation requests, joining with the users table to get names
$stmt = $conn->prepare(
    "SELECT v.id, v.full_name, v.date_applied, u.email 
     FROM verifications v
     JOIN users u ON v.user_id = u.id
     WHERE v.status = 'pending' 
     ORDER BY v.date_applied ASC"
);
$stmt->execute();
$pending_requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Validations - Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-layout">
        <input type="checkbox" id="sidebar-toggle">
        <?php include 'admin-sidebar.php'; ?>

        <div class="main-content">
            <header class="header">
                <label for="sidebar-toggle" class="toggle-button"><i class="fas fa-bars"></i></label>
                <h1>User Validation Requests</h1>
            </header>

            <section class="dashboard-main-area">
                <div class="data-table-card">
                    <div class="card-title">
                        <i class="fas fa-user-clock"></i> Pending Submissions
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Validation ID</th>
                                    <th>Full Name</th>
                                    <th>User Email</th>
                                    <th>Date Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pending_requests)): ?>
                                    <?php foreach ($pending_requests as $request): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($request['id']) ?></td>
                                            <td><?= htmlspecialchars($request['full_name']) ?></td>
                                            <td><?= htmlspecialchars($request['email']) ?></td>
                                            <td><?= date('F j, Y', strtotime($request['date_applied'])) ?></td>
                                            <td>
                                                <a href="admin-view-validation.php?id=<?= $request['id'] ?>" class="action-link">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="no-data">No pending validation requests found.</td>
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