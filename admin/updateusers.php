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
    $user_id = trim($_POST['id']);
    $role = trim($_POST['role']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($user_id) || empty($role) || empty($fullname) || empty($email)) {
        $_SESSION['error_message'] = 'All required fields must be filled';
        header('Location: updateusers.php?id=' . $user_id . '&role=' . $role);
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = 'Invalid email format';
        header('Location: updateusers.php?id=' . $user_id . '&role=' . $role);
        exit();
    }

    // Check if email already exists for other users (excluding soft-deleted)
    $email_exists = false;
    
    if ($role === 'admin') {
        $check_query = "SELECT admin_ID FROM admins WHERE admin_email = ? AND admin_ID != ? AND is_deleted = 0";
    } elseif ($role === 'staff') {
        $check_query = "SELECT staffID FROM staff WHERE staffEmail = ? AND staffID != ? AND is_deleted = 0";
    } elseif ($role === 'customer') {
        $check_query = "SELECT student_ID FROM students WHERE student_email = ? AND student_ID != ? AND is_deleted = 0";
    }

    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $email_exists = true;
    }
    mysqli_stmt_close($stmt);

    if ($email_exists) {
        $_SESSION['error_message'] = 'Email already exists for another user';
        header('Location: updateusers.php?id=' . $user_id . '&role=' . $role);
        exit();
    }

    // Update user based on role
    $success = false;

    if ($role === 'admin') {
        if (!empty($password)) {
            $hashed_password = md5($password);
            $query = "UPDATE admins SET admin_name = ?, admin_email = ?, admin_password = ?, updated_at = CURRENT_TIMESTAMP WHERE admin_ID = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssi", $fullname, $email, $hashed_password, $user_id);
        } else {
            $query = "UPDATE admins SET admin_name = ?, admin_email = ?, updated_at = CURRENT_TIMESTAMP WHERE admin_ID = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssi", $fullname, $email, $user_id);
        }
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } 
    elseif ($role === 'staff') {
        if (!empty($password)) {
            $hashed_password = md5($password);
            $query = "UPDATE staff SET staffName = ?, staffEmail = ?, staffPassword = ?, updated_at = CURRENT_TIMESTAMP WHERE staffID = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssi", $fullname, $email, $hashed_password, $user_id);
        } else {
            $query = "UPDATE staff SET staffName = ?, staffEmail = ?, updated_at = CURRENT_TIMESTAMP WHERE staffID = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssi", $fullname, $email, $user_id);
        }
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } 
    elseif ($role === 'customer') {
        if (!empty($password)) {
            $hashed_password = md5($password);
            $query = "UPDATE students SET student_name = ?, student_email = ?, student_password = ?, updated_at = CURRENT_TIMESTAMP WHERE student_ID = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssi", $fullname, $email, $hashed_password, $user_id);
        } else {
            $query = "UPDATE students SET student_name = ?, student_email = ?, updated_at = CURRENT_TIMESTAMP WHERE student_ID = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssi", $fullname, $email, $user_id);
        }
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    if ($success) {
        $_SESSION['success_message'] = 'User account updated successfully!';
        header('Location: user_management.php');
        exit();
    } else {
        $_SESSION['error_message'] = 'Failed to update account: ' . mysqli_error($conn);
        header('Location: updateusers.php?id=' . $user_id . '&role=' . $role);
        exit();
    }
}

// Get user ID and role from URL
$user_id = $_GET['id'] ?? null;
$role = $_GET['role'] ?? null;

if (!$user_id || !$role) {
    $_SESSION['error_message'] = 'Invalid user ID or role';
    header('Location: user_management.php');
    exit();
}

// Fetch user data based on role (exclude soft-deleted)
$user = null;

if ($role === 'admin') {
    $query = "SELECT admin_ID as id, admin_name as fullname, admin_username as username, admin_email as email FROM admins WHERE admin_ID = ? AND is_deleted = 0";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} 
elseif ($role === 'staff') {
    $query = "SELECT staffID as id, staffName as fullname, staffUsername as username, staffEmail as email FROM staff WHERE staffID = ? AND is_deleted = 0";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} 
elseif ($role === 'customer') {
    $query = "SELECT student_ID as id, student_name as fullname, student_username as username, student_email as email FROM students WHERE student_ID = ? AND is_deleted = 0";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$user) {
    $_SESSION['error_message'] = 'User not found';
    header('Location: user_management.php');
    exit();
}

// Check for error message from session
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
            <h2>Update User Account</h2>
            <p>Modify details for: <strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
        </div>
        <a href="user_management.php" class="btn-back">
            <span class="material-symbols-outlined">arrow_back</span> Back
        </a>
    </div>

    <?php if ($error_message): ?>
    <div class="alert alert-error">
        <span class="material-symbols-outlined">error</span>
        <?php echo htmlspecialchars($error_message); ?>
    </div>
    <?php endif; ?>

    <div class="update-form-container">
        <form action="updateusers.php" method="POST" class="staff-update-form">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
            <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">

            <div class="form-inputs">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                </div>

                <div class="input-row">
                    <div class="input-group">
                        <label>Username (Locked)</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" readonly style="background:#f0f0f0; cursor:not-allowed;">
                    </div>

                    <div class="input-group">
                        <label>Role (Locked)</label>
                        <input type="text" value="<?php echo ucfirst($role); ?>" readonly style="background:#f0f0f0; cursor:not-allowed;">
                     </div>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="input-group">
                    <label>New Password (Leave blank to keep current)</label>
                    <input type="password" name="password" placeholder="••••••••">
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