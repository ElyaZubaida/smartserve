<?php
    session_start();
    include 'config/db_connect.php';

    // Check if user is logged in
    if (!isset($_SESSION['student_id'])) {
        header('Location: login.php');
        exit();
    }

    $student_id = $_SESSION['student_id'];

    // Check if order_id is provided
    if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
        header('Location: myorders.php');
        exit();
    }

    $order_id = intval($_GET['order_id']);

    try {
        // Fetch the order to check ownership and status
        $query = "SELECT order_status FROM orders WHERE order_ID = ? AND student_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $order_id, $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        if (!$order) {
            $_SESSION['error_message'] = "Order not found.";
            header('Location: myorders.php');
            exit();
        }

        if (strtolower($order['order_status']) === 'completed') {
            $_SESSION['error_message'] = "Completed orders cannot be cancelled.";
            header('Location: myorders.php');
            exit();
        }

        // Update order status to Cancelled
        $update = "UPDATE orders SET order_status = 'Cancelled' WHERE order_ID = ? AND student_ID = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("ii", $order_id, $student_id);
        $stmt->execute();

        $_SESSION['success_message'] = "Order #{$order_id} has been cancelled.";
        header('Location: myorders.php');
        exit();

    } catch (Exception $e) {
        $_SESSION['error_message'] = "Failed to cancel order: " . $e->getMessage();
        header('Location: myorders.php');
        exit();
    }
?>
