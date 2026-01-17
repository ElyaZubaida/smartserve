<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: loginadmin.php');
    exit();
}

include '../config/db_connect.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $admin_id = $_SESSION['admin_id'];

    // Validate inputs
    if (empty($fullname) || empty($username) || empty($email) || empty($password) || empty($role)) {
        $error_message = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Invalid email format';
    } else {
        // Hash password using MD5 to match existing system
        $hashed_password = md5($password);

        // Check if username or email already exists (excluding soft-deleted users)
        $check_queries = [
            "SELECT admin_username FROM admins WHERE (admin_username = ? OR admin_email = ?) AND is_deleted = 0",
            "SELECT staffUsername FROM staff WHERE (staffUsername = ? OR staffEmail = ?) AND is_deleted = 0",
            "SELECT student_username FROM students WHERE (student_username = ? OR student_email = ?) AND is_deleted = 0"
        ];

        $username_exists = false;
        foreach ($check_queries as $check_query) {
            $stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($stmt, "ss", $username, $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                $username_exists = true;
                mysqli_stmt_close($stmt);
                break;
            }
            mysqli_stmt_close($stmt);
        }

        if ($username_exists) {
            $error_message = 'Username or email already exists';
        } else {
            // Insert based on role
            $success = false;
            
            if ($role === 'admin') {
                $query = "INSERT INTO admins (admin_name, admin_email, admin_username, admin_password) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "ssss", $fullname, $email, $username, $hashed_password);
                $success = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } 
            elseif ($role === 'staff') {
                $query = "INSERT INTO staff (admin_ID, staffName, staffEmail, staffUsername, staffPassword) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "issss", $admin_id, $fullname, $email, $username, $hashed_password);
                $success = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } 
            elseif ($role === 'student') {
                $query = "INSERT INTO students (admin_ID, student_name, student_email, student_username, student_password) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "issss", $admin_id, $fullname, $email, $username, $hashed_password);
                $success = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            if ($success) {
                $_SESSION['success_message'] = ucfirst($role) . ' account created successfully!';
                header('Location: user_management.php');
                exit();
            } else {
                $error_message = 'Failed to create account: ' . mysqli_error($conn);
            }
        }
    }
}

// Check for error message from session
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

    <?php if (isset($error_message)): ?>
    <div class="alert alert-error">
        <span class="material-symbols-outlined">error</span>
        <?php echo htmlspecialchars($error_message); ?>
    </div>
    <?php endif; ?>

    <div class="update-form-container">
        <form action="adduser.php" method="POST" class="staff-update-form">
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

<style>
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>
</body>
</html>

<?php
mysqli_close($conn);
?>