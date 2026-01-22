<?php
session_start();
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}
// Include database connection
include '../config/db_connect.php';

// Check if menu ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "No menu item selected for deletion.";
    header("Location: menu_management.php");
    exit();
}

$menu_id = mysqli_real_escape_string($conn, $_GET['id']);

// Soft delete query
$delete_query = "UPDATE `menus` SET `is_deleted` = 1 WHERE `menuID` = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("i", $menu_id);

try {
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Menu item soft deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting menu item: " . $stmt->error;
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = "An unexpected error occurred: " . $e->getMessage();
} finally {
    // Close statement and connection
    $stmt->close();
    $conn->close();
    
    // Redirect back to menu management
    header("Location: menu_management.php");
    exit();
}
?>