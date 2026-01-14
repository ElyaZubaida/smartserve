<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: a_dashboard.php?error=unauthorized');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Add New User</title>
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
            <h2>Add New User</h2>
            <p>Create credentials for Admins, Staff, or Customers</p>
        </div>
        <a href="user_management.php" class="btn-back">
            <span class="material-symbols-outlined">arrow_back</span> Back to List
        </a>
    </div>

    <div class="update-form-container">
        <form action="a_insert_user.php" method="POST" class="staff-update-form">
            <div class="form-inputs">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" placeholder="e.g. Ahmad bin Ibrahim" required>
                </div>

                <div class="input-row">
                    <div class="input-group">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Unique username" required>
                    </div>

                    <div class="input-group">
                        <label>Account Role</label>
                        <select name="role" required>
                            <option value="student">Customer</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="user@smartserve.com" required>
                </div>

                <div class="input-group">
                    <label>Set Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="update-confirm-btn" style="background-color: #1b5e20;">
                        Create Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>