<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: loginadmin.php');
    exit();
}

include '../config/db_connect.php';

// Get user ID and role from URL
$user_id = $_GET['id'] ?? null;
$role = $_GET['role'] ?? null;

if (!$user_id || !$role) {
    $_SESSION['error_message'] = 'Invalid user ID or role';
    header('Location: user_management.php');
    exit();
}

// Prevent admin from deleting themselves
if ($role === 'admin' && $user_id == $_SESSION['admin_id']) {
    $_SESSION['error_message'] = 'You cannot delete your own account';
    header('Location: user_management.php');
    exit();
}

// Soft delete user based on role (set is_deleted = 1)
$success = false;

if ($role === 'admin') {
    $query = "UPDATE admins SET is_deleted = 1, updated_at = CURRENT_TIMESTAMP WHERE admin_ID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} 
elseif ($role === 'staff') {
    $query = "UPDATE staff SET is_deleted = 1, updated_at = CURRENT_TIMESTAMP WHERE staffID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} 
elseif ($role === 'customer') {
    $query = "UPDATE students SET is_deleted = 1, updated_at = CURRENT_TIMESTAMP WHERE student_ID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

if ($success) {
    $_SESSION['success_message'] = ucfirst($role) . ' account deleted successfully!';
} else {
    $_SESSION['error_message'] = 'Failed to delete account: ' . mysqli_error($conn);
}

mysqli_close($conn);
header('Location: user_management.php');
exit();
?>