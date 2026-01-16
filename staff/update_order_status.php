<?php
// Start session for messaging
session_start();

// Include database connection
include '../config/db_connect.php';

// Check if form submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    // Validate data
    if ($order_id == 0 || empty($status)) {
        $_SESSION['error_message'] = "Invalid order ID or status.";
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

    // Update order status in database
    $query = "UPDATE `ORDER` SET ORDER_STATUS = ? WHERE ORDER_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        // Success
        $_SESSION['success_message'] = "Order status updated successfully.";
        $stmt->close();
        $conn->close();
        header("Location: s_orderdetails.php?id=" . $order_id);
        exit;
    } else {
        // Error
        $_SESSION['error_message'] = "Failed to update order status.";
        $stmt->close();
        $conn->close();
        header("Location: s_orderdetails.php?id=" . $order_id);
        exit;
    }
} else {
    // If accessed directly
    $conn->close();
    header("Location: order_management.php");
    exit;
}
?>