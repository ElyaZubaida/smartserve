<?php
// processorder.php

session_start();
include 'config/db_connect.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

// Check if form was submitted and pickup_time is set
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['pickup_time'])) {
    $_SESSION['error_message'] = "Please select a pickup time.";
    header('Location: placeorder.php');
    exit();
}

$student_id = $_SESSION['student_id'];
$pickup_time = $_POST['pickup_time']; // e.g., "15:30:00"

// Start transaction
$conn->begin_transaction();

try {
    //Get cart items
    $query = "SELECT c.cart_ID, cm.menuID, cm.cm_quantity, cm.cm_subtotal, cm.cm_request
              FROM carts c
              JOIN cart_menu cm ON c.cart_ID = cm.cart_ID
              WHERE c.student_ID = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) throw new Exception($conn->error);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cart_items = [];
    $total_amount = 0;

    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total_amount += (float) $row['cm_subtotal'];
    }

    //Check if cart is empty
    if (empty($cart_items)) {
        throw new Exception("Your cart is empty.");
    }

    // Prepare pickup datetime
    $pickup_datetime = date('Y-m-d') . ' ' . $pickup_time; 

    //Insert into orders table
    $insert_order = "INSERT INTO orders (student_ID, order_date, pickup_time, order_status, order_totalAmount, order_amountPaid)
                     VALUES (?, NOW(), ?, 'Pending', ?, 0.00)";
    $stmt = $conn->prepare($insert_order);
    if (!$stmt) throw new Exception($conn->error);
    $stmt->bind_param("isd", $student_id, $pickup_datetime, $total_amount);
    if (!$stmt->execute()) throw new Exception($stmt->error);

    $order_id = $conn->insert_id;

    //Insert each cart item into order_menu
    $insert_item = "INSERT INTO order_menu (order_ID, menuID, om_quantity, om_subtotal, request)
                    VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_item);
    if (!$stmt) throw new Exception($conn->error);

    foreach ($cart_items as $item) {
        $stmt->bind_param(
            "iiids",
            $order_id,
            $item['menuID'],
            $item['cm_quantity'],
            $item['cm_subtotal'],
            $item['cm_request'] 
        );
        if (!$stmt->execute()) throw new Exception($stmt->error);
    }

    //Clear cart_menu items for this student
    $delete_cart_menu = "DELETE cm FROM cart_menu cm
                         JOIN carts c ON cm.cart_ID = c.cart_ID
                         WHERE c.student_ID = ?";
    $stmt = $conn->prepare($delete_cart_menu);
    if (!$stmt) throw new Exception($conn->error);
    $stmt->bind_param("i", $student_id);
    if (!$stmt->execute()) throw new Exception($stmt->error);

    $conn->commit();

    // Redirect to order details page
    header("Location: orderdetails.php?order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();

    $_SESSION['error_message'] = "Failed to place order: " . $e->getMessage();
    header('Location: cart.php');
    exit();
}
?>
