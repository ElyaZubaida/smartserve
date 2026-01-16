<?php
    /**
     * Frontend: Elya
     * Backend: Aleesya
     * 
     * Handles adding items to cart
     */

    session_start();
    include 'config/db_connect.php';

    // Set JSON response header
    header('Content-Type: application/json');

    // Check if user is logged in
    if (!isset($_SESSION['student_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first.']);
        exit();
    }

    // Validate POST data
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        exit();
    }

    $student_id = $_SESSION['student_id'];
    $menuID = intval($_POST['menuID']);
    $quantity = intval($_POST['quantity']);
    $special_request = trim($_POST['special_request'] ?? '');

    // Validate inputs
    if ($menuID <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid menu item or quantity.']);
        exit();
    }

    // Check if menu item exists and is available
    $check_query = "SELECT menuID, menuName, menuPrice, menuAvailability FROM menus WHERE menuID = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $menuID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Menu item not found.']);
        exit();
    }

    $menu_item = $result->fetch_assoc();

    if ($menu_item['menuAvailability'] == 0) {
        echo json_encode(['success' => false, 'message' => 'This item is currently unavailable.']);
        exit();
    }

    // First, get or create the student's cart
    $cart_query = "SELECT cart_ID FROM carts WHERE student_ID = ? LIMIT 1";
    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();
    
    if ($cart_result->num_rows > 0) {
        // Cart exists, get the cart_ID
        $cart = $cart_result->fetch_assoc();
        $cart_id = $cart['cart_ID'];
    } else {
        // Create a new cart for this student
        $create_cart = "INSERT INTO carts (student_ID, cart_totalPrice) VALUES (?, 0)";
        $stmt = $conn->prepare($create_cart);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $cart_id = $stmt->insert_id;
    }
    
    // Calculate subtotal
    $subtotal = $menu_item['menuPrice'] * $quantity;

    // Check if item already exists in cart_menu for this cart
    $check_item = "SELECT cart_menu.cart_ID, cart_menu.menuID, cart_menu.cm_quantity, cart_menu.cm_subtotal
                   FROM cart_menu
                   WHERE cart_menu.cart_ID = ? AND cart_menu.menuID = ?";
    $stmt = $conn->prepare($check_item);
    $stmt->bind_param("ii", $cart_id, $menuID);
    $stmt->execute();
    $cart_menu_result = $stmt->get_result();

    if ($cart_menu_result->num_rows > 0) {
        // Update existing cart item
        $existing_item = $cart_menu_result->fetch_assoc();
        $new_quantity = $existing_item['cm_quantity'] + $quantity;
        $new_subtotal = $menu_item['menuPrice'] * $new_quantity;
        
        $update_query = "UPDATE cart_menu 
                        SET cm_quantity = ?, cm_subtotal = ? 
                        WHERE cart_ID = ? AND menuID = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("idii", $new_quantity, $new_subtotal, $cart_id, $menuID);
        
        if ($stmt->execute()) {
            // Update cart total price
            $update_total = "UPDATE carts 
                           SET cart_totalPrice = (
                               SELECT SUM(cm_subtotal) 
                               FROM cart_menu 
                               WHERE cart_ID = ?
                           ) 
                           WHERE cart_ID = ?";
            $stmt = $conn->prepare($update_total);
            $stmt->bind_param("ii", $cart_id, $cart_id);
            $stmt->execute();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Cart updated successfully!',
                'cart_id' => $cart_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update cart.']);
        }
    } else {
        // Insert new cart item into cart_menu
        $insert_query = "INSERT INTO cart_menu (cart_ID, menuID, cm_quantity, cm_subtotal) 
                        VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iiid", $cart_id, $menuID, $quantity, $subtotal);
        
        if ($stmt->execute()) {
            // Update cart total price
            $update_total = "UPDATE carts 
                           SET cart_totalPrice = (
                               SELECT SUM(cm_subtotal) 
                               FROM cart_menu 
                               WHERE cart_ID = ?
                           ) 
                           WHERE cart_ID = ?";
            $stmt = $conn->prepare($update_total);
            $stmt->bind_param("ii", $cart_id, $cart_id);
            $stmt->execute();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Item added to cart successfully!',
                'cart_id' => $cart_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add item to cart.']);
        }
    }

    $stmt->close();
    $conn->close();
?>