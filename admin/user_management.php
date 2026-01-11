<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: a_dashboard.php?error=unauthorized');
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
$user_role = $_SESSION['role'];

// Dummy Data 
$all_users = [
    ['id' => 1, 'username' => 'admin_main', 'fullname' => 'Super Admin', 'email' => 'admin@smartserve.com', 'role' => 'Admin'],
    ['id' => 2, 'username' => 'staff_Qai', 'fullname' => 'Qai binti Yuyu', 'email' => 'Qai@smartserve.com', 'role' => 'Staff'],
    ['id' => 3, 'username' => 'staff_amirah', 'fullname' => 'Amirah James', 'email' => 'amirah@smartserve.com', 'role' => 'Staff'],
    ['id' => 4, 'username' => 'aleesya', 'fullname' => 'Aleesya binti Aleesya', 'email' => 'aleesya@student.com', 'role' => 'Customer'],
    ['id' => 5, 'username' => 'staff_qis', 'fullname' => 'Qis binti Haykal', 'email' => 'qis@smartserve.com', 'role' => 'Staff'],
    ['id' => 6, 'username' => 'Firzanah', 'fullname' => 'Firzanah binti Pirjanah', 'email' => 'Firzanah@student.com', 'role' => 'Customer'],
    ['id' => 7, 'username' => 'staff_Mina', 'fullname' => 'Syamina bin Mina', 'email' => 'Mina@smartserve.com', 'role' => 'Staff']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - User Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="../staff/sastyle.css">
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-top">
            <div class="logo-container">
                <img src="../img/logo.png" alt="SmartServe Logo">
                <h3>Smart<span>Serve</span></h3>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li><a href="a_dashboard.php"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                    <li><a href="a_menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li><a href="a_order_management.php"><span class="material-symbols-outlined">order_approve</span> Order Management</a></li>
                    <li class="active"><a href="user_management.php"><span class="material-symbols-outlined">manage_accounts</span> User Management</a></li>
                    <li><a href="a_report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
                    <li class="nav-divider"></li>
                    <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="../logout.php" class="logout-link"><span class="material-symbols-outlined">logout</span> Log Out</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="main-content staff-report-content">
        <div class="header">
            <div class="title">
                <h2>User Management</h2>
                <p>Manage all users accounts (Admin, Staff, & Customers)</p>
            </div>
            <a href="adduser.php" class="staff-menu-add-btn">
                <span class="material-symbols-outlined">person_add</span> Add Users
            </a>
        </div>

        <div class="report-table-container">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($all_users as $user): ?>
                    <tr>
                        <td><?php echo str_pad($user['id'], 3, '0', STR_PAD_LEFT); ?></td>
                        <td><strong><?php echo $user['fullname']; ?></strong></td>
                        <td>
                            <?php 
                                $roleClass = 'role-' . strtolower($user['role']);
                            ?>
                            <span class="role-badge <?php echo $roleClass; ?>">
                                <?php echo $user['role']; ?>
                            </span>
                        </td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <div class="staff-menu-actions">
                                <a href="updateusers.php?id=<?php echo $user['id']; ?>" class="staff-menu-edit">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                <?php if($user['username'] !== $_SESSION['username']): ?>
                                    <a href="deleteuser.php?id=<?php echo $user['id']; ?>" class="staff-menu-edit" style="color: #d32f2f;" onclick="return confirm('Delete this account?')">
                                        <span class="material-symbols-outlined">delete</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>