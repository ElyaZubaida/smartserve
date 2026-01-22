<!-- 
 Frontend: Elya 
 Backend: Aleesya 
 -->
<?php
    session_start();
    include 'config/db_connect.php';

    // Check if user is logged in
    if (!isset($_SESSION['student_id'])) {
        header('Location: login.php');
        exit();
    }

    $student_id = $_SESSION['student_id'];
    $message = '';
    $message_type = '';

    // Handle quantity update
    if (isset($_POST['update_qty'])) {
        $cart_id = intval($_POST['cart_id']);
        $menu_id = intval($_POST['menu_id']);
        $new_qty = intval($_POST['quantity']);
        $request = isset($_POST['special_request']) ? trim($_POST['special_request']) : '';
        
        if ($new_qty > 0) {
            // Get menu price to calculate new subtotal
            $price_query = "SELECT menuPrice FROM menus WHERE menuID = ?";
            $stmt = $conn->prepare($price_query);
            $stmt->bind_param("i", $menu_id);
            $stmt->execute();
            $price_result = $stmt->get_result();
            $menu = $price_result->fetch_assoc();
            $new_subtotal = $menu['menuPrice'] * $new_qty;
            
            // Update cart_menu (including request)
            $update = "UPDATE cart_menu SET cm_quantity = ?, cm_subtotal = ?, cm_request = ? WHERE cart_ID = ? AND menuID = ?";
            $stmt = $conn->prepare($update);
            $stmt->bind_param("idssi", $new_qty, $new_subtotal, $request, $cart_id, $menu_id);
            
            if ($stmt->execute()) {
                // Update cart total
                $update_total = "UPDATE carts SET cart_totalPrice = (SELECT SUM(cm_subtotal) FROM cart_menu WHERE cart_ID = ?) WHERE cart_ID = ?";
                $stmt = $conn->prepare($update_total);
                $stmt->bind_param("ii", $cart_id, $cart_id);
                $stmt->execute();
                
                $message = 'Item updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Failed to update item.';
                $message_type = 'error';
            }
        }
    } 

    // Handle item removal
    if (isset($_POST['remove_item'])) {
        $cart_id = intval($_POST['cart_id']);
        $menu_id = intval($_POST['menu_id']);
        
        // Delete from cart_menu
        $delete = "DELETE FROM cart_menu WHERE cart_ID = ? AND menuID = ?";
        $stmt = $conn->prepare($delete);
        $stmt->bind_param("ii", $cart_id, $menu_id);
        
        if ($stmt->execute()) {
            // Update cart total
            $update_total = "UPDATE carts SET cart_totalPrice = (SELECT IFNULL(SUM(cm_subtotal), 0) FROM cart_menu WHERE cart_ID = ?) WHERE cart_ID = ?";
            $stmt = $conn->prepare($update_total);
            $stmt->bind_param("ii", $cart_id, $cart_id);
            $stmt->execute();
            
            $message = 'Item removed from cart.';
            $message_type = 'success';
        } else {
            $message = 'Failed to remove item.';
            $message_type = 'error';
        }
    }

    // Handle clear cart
    if (isset($_POST['clear_cart'])) {
        $cart_id = intval($_POST['cart_id']);
        
        // Delete all items from cart_menu
        $delete_all = "DELETE FROM cart_menu WHERE cart_ID = ?";
        $stmt = $conn->prepare($delete_all);
        $stmt->bind_param("i", $cart_id);
        
        if ($stmt->execute()) {
            // Update cart total to 0
            $update_total = "UPDATE carts SET cart_totalPrice = 0 WHERE cart_ID = ?";
            $stmt = $conn->prepare($update_total);
            $stmt->bind_param("i", $cart_id);
            $stmt->execute();
            
            $message = 'Cart cleared successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to clear cart.';
            $message_type = 'error';
        }
    }

    // Fetch cart items including request
    $cart_query = "SELECT carts.cart_ID, cart_menu.cm_quantity, cart_menu.menuID, cart_menu.cm_request,
                          menus.menuName, menus.menuPrice, menus.menuImage
                   FROM carts
                   JOIN cart_menu ON carts.cart_ID = cart_menu.cart_ID
                   JOIN menus ON cart_menu.menuID = menus.menuID
                   WHERE carts.student_ID = ?";
    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $cart_items = $stmt->get_result();

    // Calculate total
    $total = 0;
    $items_array = [];
    $cart_id_for_clear = null;
    while ($item = $cart_items->fetch_assoc()) {
        $total += $item['menuPrice'] * $item['cm_quantity'];
        $items_array[] = $item;
        if ($cart_id_for_clear === null) {
            $cart_id_for_clear = $item['cart_ID'];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SmartServe - Cart</title>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <!-- Navigation Bar -->
        <header>
            <div class="menubar">
                <div class="logo">
                    <img src="img/logo.png" alt="Smart Serve Logo">
                </div>

                <nav>
                    <ul>
                        <li><a href="menu.php"><span class="material-symbols-outlined">home</span> Home</a></li>
                        <li><a href="ai.php"><span class="material-symbols-outlined">psychology</span> Food Recommendation</a></li>
                        <li><a href="myorders.php"><span class="material-symbols-outlined">receipt_long</span> Orders</a></li>
                        <li><a href="cart.php"><span class="material-symbols-outlined">shopping_cart</span> Cart</a></li>
                        <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                        <li><a href="logout.php"><span class="material-symbols-outlined">logout</span></a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- Cart Section -->
        <div class="cart-container">
            <div class="cart-title">
                <h1>My Cart</h1>
                <a href="menu.php" class="btn-continue-browsing">
                    <span class="material-symbols-outlined">arrow_back</span> Continue Browsing
                </a>
            </div>

            <?php if (count($items_array) > 0): ?>
            <div style="text-align: right; margin-bottom: 20px;">
                <button class="btn-clear-cart" onclick="showClearCartModal()">
                    <span class="material-symbols-outlined">delete_sweep</span> Clear Cart
                </button>
            </div>
            <?php endif; ?>

            <section class="cart-layout">
                <div class="cart-items-wrapper">
                    <?php if (count($items_array) > 0): ?>
                    <?php foreach ($items_array as $index => $item): 
                        $imgPath = $item['menuImage'];
                        if (strpos($imgPath, 'img/') === false) { $imgPath = 'img/' . $imgPath; }
                    ?>
                        <div class="cart-card">
                            <div class="cart-card-img">  
                                <img src="<?php echo htmlspecialchars($imgPath); ?>" 
                                     alt="<?php echo htmlspecialchars($item['menuName']); ?>">  
                            </div>
                            <div class="cart-card-info">
                                <div class="info-top">
                                    <h3><?php echo htmlspecialchars($item['menuName']); ?></h3>
                                    <p class="price-tag">RM <?php echo number_format($item['menuPrice'], 2); ?></p>
                                </div>

                                <div class="cart-card-actions">
                                    <form method="POST" class="qty-form" id="form-<?php echo $index; ?>">
                                        <div class="special-request-input">
                                            <input type="text" name="special_request" 
                                                placeholder="Special request..." 
                                                value="<?php echo htmlspecialchars($item['cm_request']); ?>" 
                                                class="cart-request-input">
                                        </div>

                                        <div class="qty-selector">
                                            <label>Qty:</label>
                                            <input type="number" 
                                                id="quantity-<?php echo $index; ?>"
                                                name="quantity" 
                                                value="<?php echo $item['cm_quantity']; ?>" 
                                                min="1" 
                                                max="99"
                                                style="width: 60px; height: 36px;">

                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_ID']; ?>">
                                            <input type="hidden" name="menu_id" value="<?php echo $item['menuID']; ?>">
                                            <button type="submit" name="update_qty" class="btn-update-qty" style="height: 36px;">Update</button>
                                        </div>
                                    </form>

                                    <button type="button" class="text-remove-btn" onclick="showRemoveModal(<?php echo $item['cart_ID']; ?>, <?php echo $item['menuID']; ?>)">
                                        <span class="material-symbols-outlined">delete_outline</span> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <span class="material-symbols-outlined">shopping_cart</span>
                            <p>Your cart is empty.</p>
                            <a href="menu.php" class="btn-outline">Browse Menu</a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (count($items_array) > 0): ?>
                <aside class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-details">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>RM <?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Service Fee</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span>RM <?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>
                    <a href="placeorder.php" class="place-order-btn-full">Confirm Order</a>
                </aside>
                <?php endif; ?>
            </section>
        </div>

        <!-- Success/Error Modal -->
        <div id="messageModal" class="modal">
            <div class="modal-content">
                <span class="material-symbols-outlined modal-icon <?php echo $message_type; ?>">
                    <?php echo $message_type === 'success' ? 'check_circle' : 'error'; ?>
                </span>
                <h2><?php echo $message_type === 'success' ? 'Success!' : 'Error'; ?></h2>
                <p id="modalMessage"><?php echo htmlspecialchars($message); ?></p>
                <button class="modal-btn" onclick="closeModal()">OK</button>
            </div>
        </div>

        <!-- Remove Item Confirmation Modal -->
        <div id="removeModal" class="modal">
            <div class="modal-content">
                <span class="material-symbols-outlined modal-icon error">warning</span>
                <h2>Remove Item?</h2>
                <p>Are you sure you want to remove this item from your cart?</p>
                <div class="modal-actions">
                    <button class="modal-btn-cancel" onclick="closeRemoveModal()">Cancel</button>
                    <button class="modal-btn-confirm" onclick="confirmRemoveAction()">Remove</button>
                </div>
            </div>
        </div>

        <!-- Clear Cart Confirmation Modal -->
        <div id="clearCartModal" class="modal">
            <div class="modal-content">
                <span class="material-symbols-outlined modal-icon error">delete_sweep</span>
                <h2>Clear Cart?</h2>
                <p>Are you sure you want to remove all items from your cart?</p>
                <div class="modal-actions">
                    <button class="modal-btn-cancel" onclick="closeClearCartModal()">Cancel</button>
                    <button class="modal-btn-confirm" onclick="confirmClearCart()">Clear All</button>
                </div>
            </div>
        </div>

        <!-- Hidden form for clear cart -->
        <form id="clearCartForm" method="POST" style="display: none;">
            <input type="hidden" name="cart_id" value="<?php echo $cart_id_for_clear; ?>">
            <input type="hidden" name="clear_cart" value="1">
        </form>

        <!-- Hidden form for remove item -->
        <form id="removeItemForm" method="POST" style="display: none;">
            <input type="hidden" name="cart_id" id="remove_cart_id">
            <input type="hidden" name="menu_id" id="remove_menu_id">
            <input type="hidden" name="remove_item" value="1">
        </form>

        <script>
            // Show message modal if there's a message
            <?php if (!empty($message)): ?>
            window.onload = function() {
                document.getElementById('messageModal').style.display = 'block';
            };
            <?php endif; ?>

            function closeModal() {
                document.getElementById('messageModal').style.display = 'none';
            }

            // Remove item confirmation
            let pendingCartId = null;
            let pendingMenuId = null;

            function showRemoveModal(cartId, menuId) {
                pendingCartId = cartId;
                pendingMenuId = menuId;
                document.getElementById('removeModal').style.display = 'block';
            }

            function closeRemoveModal() {
                document.getElementById('removeModal').style.display = 'none';
                pendingCartId = null;
                pendingMenuId = null;
            }

            function confirmRemoveAction() {
                if (pendingCartId && pendingMenuId) {
                    document.getElementById('remove_cart_id').value = pendingCartId;
                    document.getElementById('remove_menu_id').value = pendingMenuId;
                    document.getElementById('removeItemForm').submit();
                }
            }

            // Clear cart confirmation
            function showClearCartModal() {
                document.getElementById('clearCartModal').style.display = 'block';
            }

            function closeClearCartModal() {
                document.getElementById('clearCartModal').style.display = 'none';
            }

            function confirmClearCart() {
                document.getElementById('clearCartForm').submit();
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                const messageModal = document.getElementById('messageModal');
                const removeModal = document.getElementById('removeModal');
                const clearCartModal = document.getElementById('clearCartModal');
                
                if (event.target == messageModal) {
                    closeModal();
                }
                if (event.target == removeModal) {
                    closeRemoveModal();
                }
                if (event.target == clearCartModal) {
                    closeClearCartModal();
                }
            }
        </script>
    </body>
</html>
<?php
    $conn->close();
?>