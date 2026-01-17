<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: loginadmin.php');
    exit();
}

include '../config/db_connect.php';

$current_page = basename($_SERVER['PHP_SELF']);

// Fetch all users from database
$all_users = [];

// Fetch admins (exclude soft-deleted)
$admin_query = "SELECT admin_ID as id, admin_name as fullname, admin_username as username, admin_email as email, 'Admin' as role FROM admins WHERE is_deleted = 0 ORDER BY admin_ID";
$admin_result = mysqli_query($conn, $admin_query);
while ($row = mysqli_fetch_assoc($admin_result)) {
    $all_users[] = $row;
}

// Fetch staff (exclude soft-deleted)
$staff_query = "SELECT staffID as id, staffName as fullname, staffUsername as username, staffEmail as email, 'Staff' as role FROM staff WHERE is_deleted = 0 ORDER BY staffID";
$staff_result = mysqli_query($conn, $staff_query);
while ($row = mysqli_fetch_assoc($staff_result)) {
    $all_users[] = $row;
}

// Fetch students (customers) (exclude soft-deleted)
$student_query = "SELECT student_ID as id, student_name as fullname, student_username as username, student_email as email, 'Customer' as role FROM students WHERE is_deleted = 0 ORDER BY student_ID";
$student_result = mysqli_query($conn, $student_query);
while ($row = mysqli_fetch_assoc($student_result)) {
    $all_users[] = $row;
}

// Check for success message
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Check for error message
$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
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
                    <li><a href="a_order_management.php"><span class="material-symbols-outlined">order_approve</span> Orders</a></li>
                    <li class="active"><a href="user_management.php"><span class="material-symbols-outlined">manage_accounts</span> User Management</a></li>
                    <li><a href="a_report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
                    <li class="nav-divider"></li>
                    <li><a href="a_profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="logoutadmin.php" class="logout-link"><span class="material-symbols-outlined">logout</span> Log Out</a></li>
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

        <?php if ($success_message): ?>
        <div class="alert alert-success">
            <span class="material-symbols-outlined">check_circle</span>
            <?php echo htmlspecialchars($success_message); ?>
        </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
        <div class="alert alert-error">
            <span class="material-symbols-outlined">error</span>
            <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>

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
                        <td><strong><?php echo htmlspecialchars($user['fullname']); ?></strong></td>
                        <td>
                            <?php 
                                $roleClass = 'role-' . strtolower($user['role']);
                            ?>
                            <span class="role-badge <?php echo $roleClass; ?>">
                                <?php echo htmlspecialchars($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <div class="staff-menu-actions">
                                <a href="updateusers.php?id=<?php echo $user['id']; ?>&role=<?php echo strtolower($user['role']); ?>" class="staff-menu-edit">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                <?php if($user['username'] !== $_SESSION['admin_username']): ?>
                                    <a href="deleteuser.php?id=<?php echo $user['id']; ?>&role=<?php echo strtolower($user['role']); ?>" class="staff-menu-edit" style="color: #d32f2f;" onclick="return confirm('Are you sure you want to delete this account? This action cannot be undone.')">
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

    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .role-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .role-admin {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .role-staff {
            background-color: #fff3e0;
            color: #f57c00;
        }
        .role-customer {
            background-color: #e8f5e9;
            color: #388e3c;
        }
    </style>
</body>
</html>

<?php
mysqli_close($conn);
?>