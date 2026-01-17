<?php
session_start();

if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}

include '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = isset($_POST['order_ID']) ? intval($_POST['order_ID']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $staff_id = $_SESSION['staff_id'];

    if ($order_id == 0 || empty($status)) {
        $_SESSION['error_message'] = "Invalid order ID or status.";
        header("Location: s_orderdetails.php?id=" . $order_id);
        exit;
    }

    // Check current status - prevent changes if already Completed or Cancelled
    $check_query = "SELECT order_status FROM orders WHERE order_ID = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $order_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $current_order = $check_result->fetch_assoc();
    $check_stmt->close();

    if ($current_order['order_status'] == 'Completed' || $current_order['order_status'] == 'Cancelled') {
        $_SESSION['error_message'] = "This order is " . strtolower($current_order['order_status']) . " and cannot be changed.";
        header("Location: s_orderdetails.php?id=" . $order_id);
        exit;
    }

    // Allowed status values
    $allowed_statuses = [
        'Pending', 
        'Preparing', 
        'Ready for Pickup', 
        'Completed', 
        'Cancelled'
    ];

    if (!in_array($status, $allowed_statuses)) {
        $_SESSION['error_message'] = "Invalid order status.";
        header("Location: s_orderdetails.php?id=" . $order_id);
        exit;
    }

    // Update order status AND staffID
    $query = "UPDATE `orders` SET order_status = ?, staffID = ? WHERE order_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $status, $staff_id, $order_id);
    
    if ($stmt->execute()) {
        $_SESSION['order_status_updated'] = true;
        $_SESSION['success_message'] = "Order status updated successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to update order status.";
    }
    
    $stmt->close();
    $conn->close();
    header("Location: s_orderdetails.php?id=" . $order_id);
    exit;
    
} else {
    $conn->close();
    header("Location: order_management.php");
    exit;
}
?>