<?php
require_once 'admin-init.php';

// 🧭 Fetch all agents from DB
$stmt_agents = $conn->prepare("SELECT id, firstName, lastName, email, status FROM users WHERE role = 'agent' ORDER BY firstName ASC");
$stmt_agents->execute();
$result = $stmt_agents->get_result();
$agents = $result->fetch_all(MYSQLI_ASSOC);

// 🧩 Add new agent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_agent'])) {
    $fname = trim($_POST['firstName']);
    $lname = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $plainPassword = $_POST['password']; // temp password input
    $hashed = password_hash($plainPassword, PASSWORD_DEFAULT); // ✅ hash it
    $status = 'Active';
    $role = 'agent';


    // Check for duplicates
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        $error = "Email already exists!";
    } else {
        $insert = $conn->prepare("INSERT INTO users (firstName, lastName, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->bind_param("ssssss", $fname, $lname, $email, $hashed, $role, $status);
        $insert->execute();
        header("Location: admin-agents.php?added=1");
        exit;
    }
}

// ⚙️ Suspend / Reactivate agent
if (isset($_GET['toggle'])) {
    $agent_id = $_GET['toggle'];
    $current_status = $_GET['status'] ?? 'Active';
    $new_status = ($current_status === 'Active') ? 'Suspended' : 'Active';

    $update = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $update->bind_param("si", $new_status, $agent_id);
    $update->execute();
    header("Location: admin-agents.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Management - Admin</title>
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
                <h1>Agent Management</h1>
            </header>

            <section class="dashboard-main-area">
                <h2 class="section-title">Add New Agent</h2>

                <?php if (isset($error)): ?>
                    <div style="color: red; margin-bottom: 10px;"><?= htmlspecialchars($error) ?></div>
                <?php elseif (isset($_GET['added'])): ?>
                    <div style="color: green; margin-bottom: 10px;">Agent added successfully!</div>
                <?php elseif (isset($_GET['updated'])): ?>
                    <div style="color: green; margin-bottom: 10px;">Agent status updated successfully!</div>
                <?php endif; ?>

                <form method="POST" class="add-agent-form" style="margin-bottom: 30px;">
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <input type="text" name="firstName" placeholder="First Name" required style="flex: 1; padding: 10px;">
                        <input type="text" name="lastName" placeholder="Last Name" required style="flex: 1; padding: 10px;">
                    </div>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
                        <input type="email" name="email" placeholder="Email" required style="flex: 1; padding: 10px;">
                        <input type="password" name="password" placeholder="Temporary Password" required style="flex: 1; padding: 10px;">
                    </div>
                    <button type="submit" name="add_agent" class="add-btn" style="margin-top: 15px; padding: 10px 20px; background-color: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer;">
                        <i class="fas fa-user-plus"></i> Add Agent
                    </button>
                </form>

                <h2 class="section-title">Existing Agents</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($agents)): ?>
                                <?php foreach ($agents as $agent): ?>
                                    <?php
                                    $currentStatus = ucfirst(strtolower($agent['status'] ?? 'Active'));
                                    $toggleText = ($currentStatus === 'Active') ? 'Suspend' : 'Reactivate';
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($agent['id']) ?></td>
                                        <td><?= htmlspecialchars($agent['firstName'] . ' ' . $agent['lastName']) ?></td>
                                        <td><?= htmlspecialchars($agent['email']) ?></td>
                                        <td>
                                            <span class="status-badge status-<?= strtolower($currentStatus) ?>">
                                                <?= htmlspecialchars($currentStatus) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="?toggle=<?= $agent['id'] ?>&status=<?= $currentStatus ?>" class="action-link confirm-action"> <?= $toggleText ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="no-data">No agents found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.confirm-action').forEach(link => {
            link.addEventListener('click', function(event) {
                // Stop the link from navigating immediately
                event.preventDefault();

                const url = this.href;
                const actionText = this.textContent.trim();

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to ${actionText.toLowerCase()} this agent?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `Yes, ${actionText}!`
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, proceed to the URL
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
</body>

</html>