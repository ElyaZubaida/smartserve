<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: a_dashboard.php?error=unauthorized');
    exit();
}

$user_id = $_GET['id'] ?? null;
$user = [
    'fullname' => 'Qai binti Yuyu',
    'username' => 'staff_Qai',
    'email' => 'Qai@smartserve.com',
    'role' => 'staff'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SmartServe - Update User</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="../staff/sastyle.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-top">
            <div class="logo-container">
                <img src="../img/logo.png" alt="SmartServe Logo">
                <h3>Smart<span>Serve</span></h3>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="active"><a href="a_dashboard.php"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                    <li><a href="a_menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li><a href="a_order_management.php"><span class="material-symbols-outlined">order_approve</span> Order Management</a></li>
                    <li><a href="user_management.php"><span class="material-symbols-outlined">manage_accounts</span> User Management</a></li>
                    <li><a href="a_report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
                    <li class="nav-divider"></li>
                    <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="../logout.php" class="logout-link"><span class="material-symbols-outlined">logout</span> Log Out</a></li>
                </ul>
            </nav>
        </div>
    </div>

<div class="main-content staff-menu-content">
    <div class="header">
        <div class="title">
            <h2>Update User Account</h2>
            <p>Modify details for: <strong><?php echo $user['username']; ?></strong></p>
        </div>
        <a href="user_management.php" class="btn-back">
            <span class="material-symbols-outlined">arrow_back</span> Back
        </a>
    </div>

    <div class="update-form-container">
        <form action="a_modify_user.php" method="POST" class="staff-update-form">
            <input type="hidden" name="id" value="<?php echo $user_id; ?>">

            <div class="form-inputs">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" value="<?php echo $user['fullname']; ?>" required>
                </div>

                <div class="input-row">
                    <div class="input-group">
                        <label>Username (Locked)</label>
                        <input type="text" value="<?php echo $user['username']; ?>" readonly style="background:#f0f0f0; cursor:not-allowed;">
                    </div>

                    <div class="input-group">
                        <label>Role (Locked)</label>
                        <input type="text" name="role" value="<?php echo ucfirst($user['role']); ?>" readonly style="background:#f0f0f0; cursor:not-allowed;">
                     </div>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>

                <div class="input-group">
                    <label>New Password (Leave blank to keep current)</label>
                    <input type="password" name="password">
                </div>

                <div class="form-actions">
                    <button type="submit" class="update-confirm-btn" style="background-color: #1b5e20;">
                        Update Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>