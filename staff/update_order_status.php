<?php
// Database connection
include('../databaseconnect.php');

// Check connection
if (!$connection) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// Check if form submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    // Validate data
    if ($order_id == 0 || empty($status)) {
        header("Location: order_details.php?id=" . $order_id . "&error=1");
        exit;
    }

    // Update order status in database
    $query = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Success - redirect back with success message
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
        header("Location: order_details.php?id=" . $order_id . "&success=1");
        exit;
    } else {
        // Error - redirect back with error message
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
        header("Location: order_details.php?id=" . $order_id . "&error=1");
        exit;
    }
} else {
    // If someone tries to access this file directly (not via POST)
    // Redirect to order management page
    mysqli_close($connection);
    header("Location: order_management.php");
    exit;
}
?>