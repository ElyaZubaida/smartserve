<?php
    session_start();
    include 'config/db_connect.php';

    header('Content-Type: application/json');

    // Check login
    if (!isset($_SESSION['student_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit();
    }

    // Validate input
    if (!isset($_POST['menuID']) || !isset($_POST['quantity'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
        exit();
    }

    $student_id = $_SESSION['student_id'];
    $menuID = intval($_POST['menuID']);
    $quantity = intval($_POST['quantity']);
    $cm_request = isset($_POST['special_request']) ? trim($_POST['special_request']) : null;

    if ($quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
        exit();
    }

    try {
        $query = "SELECT menuPrice, menuAvailability FROM menus WHERE menuID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $menuID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Menu item not found']);
            exit();
        }

        $menu = $result->fetch_assoc();

        if ($menu['menuAvailability'] == 0) {
            echo json_encode(['success' => false, 'message' => 'This item is currently unavailable']);
            exit();
        }

        $subtotal = $menu['menuPrice'] * $quantity;

        $conn->begin_transaction();

        // Get or create cart
        $query = "SELECT cart_ID FROM carts WHERE student_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $insert_cart = "INSERT INTO carts (student_ID, cart_totalPrice, created_at) VALUES (?, 0.00, NOW())";
            $stmt = $conn->prepare($insert_cart);
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $cart_ID = $conn->insert_id;
        } else {
            $cart = $result->fetch_assoc();
            $cart_ID = $cart['cart_ID'];
        }

        // Check if same menu + same request exists
        $check_query = "SELECT cm_quantity, cm_subtotal FROM cart_menu 
                        WHERE cart_ID = ? AND menuID = ? AND 
                        (cm_request = ? OR (cm_request IS NULL AND ? IS NULL))";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("iiss", $cart_ID, $menuID, $cm_request, $cm_request);
        $stmt->execute();
        $existing = $stmt->get_result();

        if ($existing->num_rows > 0) {
            $item = $existing->fetch_assoc();
            $new_quantity = $item['cm_quantity'] + $quantity;
            $new_subtotal = $item['cm_subtotal'] + $subtotal;

            $update_query = "UPDATE cart_menu 
                            SET cm_quantity = ?, cm_subtotal = ? 
                            WHERE cart_ID = ? AND menuID = ? AND 
                            (cm_request = ? OR (cm_request IS NULL AND ? IS NULL))";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("idiiss", $new_quantity, $new_subtotal, $cart_ID, $menuID, $cm_request, $cm_request);
            $stmt->execute();
        } else {
            $insert_query = "INSERT INTO cart_menu (cart_ID, menuID, cm_quantity, cm_request, cm_subtotal) 
                            VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("iiisd", $cart_ID, $menuID, $quantity, $cm_request, $subtotal);
            $stmt->execute();
        }

        // Update cart total
        $update_total = "UPDATE carts 
                        SET cart_totalPrice = (SELECT SUM(cm_subtotal) FROM cart_menu WHERE cart_ID = ?),
                            updated_at = NOW()
                        WHERE cart_ID = ?";
        $stmt = $conn->prepare($update_total);
        $stmt->bind_param("ii", $cart_ID, $cart_ID);
        $stmt->execute();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Item added to cart successfully']);
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit();
    }
?>