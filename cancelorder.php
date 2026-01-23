<?php
    session_start();
    date_default_timezone_set('Asia/Kuala_Lumpur');
    include 'config/db_connect.php';

    // Check if user is logged in
    if (!isset($_SESSION['student_id'])) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit();
        }
        header('Location: login.php');
        exit();
    }

    $student_id = $_SESSION['student_id'];

    // Check if order_id is provided
    if (!isset($_GET['order_id']) && !isset($_POST['order_id'])) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Order ID is required']);
            exit();
        }
        header('Location: myorders.php');
        exit();
    }

    // Get order_id from either GET or POST
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : intval($_GET['order_id']);

    // First, fetch the order to check all details
    $check_query = "SELECT * FROM orders WHERE order_ID = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $order_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit();
        }
        header('Location: myorders.php');
        exit();
    }

    $order = $check_result->fetch_assoc();

    // Auto-detect the student ID column name
    $student_column = null;
    if (isset($order['student_ID'])) {
        $student_column = 'student_ID';
    } elseif (isset($order['student_id'])) {
        $student_column = 'student_id';
    } elseif (isset($order['studentID'])) {
        $student_column = 'studentID';
    } elseif (isset($order['Student_ID'])) {
        $student_column = 'Student_ID';
    }

    // Verify ownership
    if (!$student_column || $order[$student_column] != $student_id) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit();
        }
        header('Location: myorders.php');
        exit();
    }

    // Check if order is already completed
    if (strtolower($order['order_status']) === 'completed') {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cannot cancel completed orders']);
            exit();
        }
        header('Location: myorders.php');
        exit();
    }

    // Check if order is already cancelled
    if (strtolower($order['order_status']) === 'cancelled') {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Order is already cancelled']);
            exit();
        }
        header('Location: myorders.php');
        exit();
    }

    // Cancel the order (force status to Cancelled for this student)
    $cancel_query = "UPDATE orders
                     SET order_status = 'Cancelled', staffID = NULL, admin_ID = NULL, updated_at = NOW()
                     WHERE order_ID = ? AND student_ID = ?";
    $cancel_stmt = $conn->prepare($cancel_query);
    $cancel_stmt->bind_param("ii", $order_id, $student_id);

    if ($cancel_stmt->execute() && $cancel_stmt->affected_rows > 0) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
            exit();
        }
        header('Location: myorders.php?cancelled=success');
        exit();
    } else {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to cancel order']);
            exit();
        }
        header('Location: myorders.php?cancelled=error');
        exit();
    }

    $conn->close();
?>
