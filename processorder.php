<?php
session_start();
include 'config/db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login first'
    ]);
    exit();
}

$student_id = $_SESSION['student_id'];

try {
    // Get pickup time (optional)
    $pickup_time = isset($_POST['pickup_time']) && !empty($_POST['pickup_time']) 
                   ? $_POST['pickup_time'] 
                   : null;

    // Start transaction
    $conn->begin_transaction();

    // Get cart items
    $cart_query = "SELECT cm.cart_ID, cm.menuID, cm.cm_quantity, cm.cm_subtotal, cm.cm_request
                   FROM carts c
                   JOIN cart_menu cm ON c.cart_ID = cm.cart_ID
                   WHERE c.student_ID = ?";
    
    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();

    if ($cart_result->num_rows === 0) {
        throw new Exception('Your cart is empty');
    }

    // Calculate total amount
    $total_amount = 0;
    $cart_items = [];
    
    while ($item = $cart_result->fetch_assoc()) {
        $total_amount += $item['cm_subtotal'];
        $cart_items[] = $item;
    }

    // Prepare pickup_time for database
    $pickup_datetime = null;
    if ($pickup_time) {
        $today = date('Y-m-d');
        $pickup_datetime = $today . ' ' . $pickup_time;
    }

    // Create order
    $order_query = "INSERT INTO orders (student_ID, order_date, pickup_time, order_status, order_totalAmount, order_amountPaid, created_at, updated_at) 
                    VALUES (?, NOW(), ?, 'Pending', ?, 0.00, NOW(), NOW())";
    
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("isd", $student_id, $pickup_datetime, $total_amount);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create order');
    }

    $order_id = $conn->insert_id;

    // Insert order items
    foreach ($cart_items as $item) {
        $order_menu_query = "INSERT INTO order_menu (order_ID, menuID, om_quantity, om_subtotal, request) 
                            VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($order_menu_query);
        $stmt->bind_param("iiids", 
            $order_id, 
            $item['menuID'], 
            $item['cm_quantity'], 
            $item['cm_subtotal'],
            $item['cm_request']
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to add items to order');
        }
    }

    // Clear cart
    $cart_id = $cart_items[0]['cart_ID'];
    
    // Delete cart items
    $delete_cart_items = "DELETE FROM cart_menu WHERE cart_ID = ?";
    $stmt = $conn->prepare($delete_cart_items);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();

    // Update cart total
    $update_cart = "UPDATE carts SET cart_totalPrice = 0.00 WHERE cart_ID = ?";
    $stmt = $conn->prepare($update_cart);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    $pickup_message = $pickup_time 
        ? "scheduled for pickup at " . date('g:i A', strtotime($pickup_time))
        : "ready for immediate pickup";

    echo json_encode([
        'success' => true,
        'message' => "Order #$order_id placed successfully and $pickup_message!",
        'order_id' => $order_id
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
// Final Stock Check Before Completing Order
$sql_check = "SELECT m.menuName 
              FROM cart_menu cm 
              JOIN menus m ON cm.menuID = m.menuID 
              JOIN carts c ON cm.cart_ID = c.cart_ID
              WHERE c.student_ID = ? AND (m.menuAvailability = 0 OR m.is_deleted = 1)";

$stmt = $conn->prepare($sql_check);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$res_check = $stmt->get_result();

if ($res_check->num_rows > 0) {
    $row = $res_check->fetch_assoc();
    echo json_encode([
        'success' => false, 
        'message' => 'Order failed! ' . $row['menuName'] . ' is no longer available. Please remove it from your cart.'
    ]);
    exit();
}
$conn->close();
?>