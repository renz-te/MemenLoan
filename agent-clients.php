<?php
require_once 'agent-init.php';

// Fetch all unique clients this agent has been assigned to across all their loans
$stmt = $conn->prepare(
    "SELECT DISTINCT u.id, u.firstName, u.lastName, u.email, u.phone 
     FROM users u 
     JOIN loans l ON u.id = l.user_id 
     WHERE l.agent_id = ? 
     ORDER BY u.lastName ASC, u.firstName ASC"
);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$clients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Clients - Agent</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-layout">
        <input type="checkbox" id="sidebar-toggle">
        <?php include 'agent-sidebar.php'; ?>

        <div class="main-content">
            <header class="header">
                <label for="sidebar-toggle" class="toggle-button"><i class="fas fa-bars"></i></label>
                <h1>My Client List</h1>
            </header>

            <section class="dashboard-main-area">
                <div class="data-table-card">
                    <div class="card-title"><i class="fas fa-users"></i> Client Portfolio</div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Client Name</th>
                                    <th>Email Address</th>
                                    <th>Phone Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($clients)): ?>
                                    <?php foreach ($clients as $client): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($client['firstName'] . ' ' . $client['lastName']) ?></td>
                                            <td><?= htmlspecialchars($client['email']) ?></td>
                                            <td><?= htmlspecialchars($client['phone']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="no-data">You have not been assigned any clients yet.</td>
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