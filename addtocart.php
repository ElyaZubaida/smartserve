<?php
    session_start();
    include 'config/db_connect.php';

    header('Content-Type: application/json');

    // 1. Check Login
    if (!isset($_SESSION['student_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit();
    }

    // 2. Validate input
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
        // 3. Fetch Menu Details
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

        // 4. Get or Create Cart Header
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

        // 5. THE FIX: Check if menuID exists in this cart_ID (Ignoring request for Primary Key safety)
        $check_query = "SELECT cm_quantity, cm_subtotal FROM cart_menu 
                        WHERE cart_ID = ? AND menuID = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ii", $cart_ID, $menuID);
        $stmt->execute();
        $existing = $stmt->get_result();

        if ($existing->num_rows > 0) {
            // IF EXISTS: UPDATE instead of INSERT to avoid "Duplicate Entry"
            $item = $existing->fetch_assoc();
            $new_quantity = $item['cm_quantity'] + $quantity;
            $new_subtotal = $new_quantity * $menu['menuPrice'];

            $update_query = "UPDATE cart_menu 
                            SET cm_quantity = ?, cm_subtotal = ?, cm_request = ? 
                            WHERE cart_ID = ? AND menuID = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("idssi", $new_quantity, $new_subtotal, $cm_request, $cart_ID, $menuID);
            $stmt->execute();
        } else {
            // IF NOT EXISTS: INSERT new row
            $insert_query = "INSERT INTO cart_menu (cart_ID, menuID, cm_quantity, cm_request, cm_subtotal) 
                            VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("iiisd", $cart_ID, $menuID, $quantity, $cm_request, $subtotal);
            $stmt->execute();
        }

        // 6. Sync the Total Price in Carts table
        $update_total = "UPDATE carts 
                        SET cart_totalPrice = (SELECT SUM(cm_subtotal) FROM cart_menu WHERE cart_ID = ?),
                            updated_at = NOW()
                        WHERE cart_ID = ?";
        $stmt = $conn->prepare($update_total);
        $stmt->bind_param("ii", $cart_ID, $cart_ID);
        $stmt->execute();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
?>