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

    // Handle quantity update
    if (isset($_POST['update_qty'])) {
        $cart_id = intval($_POST['cart_id']);
        $menu_id = intval($_POST['menu_id']);
        $new_qty = intval($_POST['quantity']);
        
        if ($new_qty > 0) {
            // Get menu price to calculate new subtotal
            $price_query = "SELECT menuPrice FROM menus WHERE menuID = ?";
            $stmt = $conn->prepare($price_query);
            $stmt->bind_param("i", $menu_id);
            $stmt->execute();
            $price_result = $stmt->get_result();
            $menu = $price_result->fetch_assoc();
            $new_subtotal = $menu['menuPrice'] * $new_qty;
            
            // Update cart_menu
            $update = "UPDATE cart_menu SET cm_quantity = ?, cm_subtotal = ? WHERE cart_ID = ? AND menuID = ?";
            $stmt = $conn->prepare($update);
            $stmt->bind_param("idii", $new_qty, $new_subtotal, $cart_id, $menu_id);
            $stmt->execute();
            
            // Update cart total
            $update_total = "UPDATE carts SET cart_totalPrice = (SELECT SUM(cm_subtotal) FROM cart_menu WHERE cart_ID = ?) WHERE cart_ID = ?";
            $stmt = $conn->prepare($update_total);
            $stmt->bind_param("ii", $cart_id, $cart_id);
            $stmt->execute();
        }
        header("Location: cart.php");
        exit();
    }

    // Handle item removal
    if (isset($_POST['remove_item'])) {
        $cart_id = intval($_POST['cart_id']);
        $menu_id = intval($_POST['menu_id']);
        
        // Delete from cart_menu
        $delete = "DELETE FROM cart_menu WHERE cart_ID = ? AND menuID = ?";
        $stmt = $conn->prepare($delete);
        $stmt->bind_param("ii", $cart_id, $menu_id);
        $stmt->execute();
        
        // Update cart total
        $update_total = "UPDATE carts SET cart_totalPrice = (SELECT IFNULL(SUM(cm_subtotal), 0) FROM cart_menu WHERE cart_ID = ?) WHERE cart_ID = ?";
        $stmt = $conn->prepare($update_total);
        $stmt->bind_param("ii", $cart_id, $cart_id);
        $stmt->execute();
        
        header("Location: cart.php");
        exit();
    }

    // Fetch cart items using JOIN
    $cart_query = "SELECT carts.cart_ID, cart_menu.cm_quantity, cart_menu.menuID,
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
    while ($item = $cart_items->fetch_assoc()) {
        $total += $item['menuPrice'] * $item['cm_quantity'];
        $items_array[] = $item;
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

            <section class="cart-layout">
                <div class="cart-items-wrapper">
                    <?php if (count($items_array) > 0): ?>
                        <?php foreach ($items_array as $index => $item): ?>
                            <div class="cart-card">
                                <div class="cart-card-img">
                                    <img src="<?php echo htmlspecialchars($item['menuImage']); ?>" 
                                        alt="<?php echo htmlspecialchars($item['menuName']); ?>">
                                </div>
                                <div class="cart-card-info">
                                    <div class="info-top">
                                        <h3><?php echo htmlspecialchars($item['menuName']); ?></h3>
                                        <p class="price-tag">RM <?php echo number_format($item['menuPrice'], 2); ?></p>
                                    </div>
                                    
                                    <div class="cart-card-actions">
                                        <form method="POST" class="qty-form" id="form-<?php echo $index; ?>">
                                            <div class="qty-selector">
                                                <label>Qty:</label>
                                                <input type="number" 
                                                    id="quantity-<?php echo $index; ?>"
                                                    name="quantity" 
                                                    value="<?php echo $item['cm_quantity']; ?>" 
                                                    min="1" 
                                                    max="99">
                                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_ID']; ?>">
                                                <input type="hidden" name="menu_id" value="<?php echo $item['menuID']; ?>">
                                                <button type="submit" name="update_qty" class="btn-update-qty">Update</button>
                                            </div>
                                        </form>
                                        
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_ID']; ?>">
                                            <input type="hidden" name="menu_id" value="<?php echo $item['menuID']; ?>">
                                            <button type="submit" name="remove_item" class="text-remove-btn" 
                                                    onclick="return confirm('Remove this item from cart?')">
                                                <span class="material-symbols-outlined">delete_outline</span> Remove
                                            </button>
                                        </form>
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
    </body>
</html>
<?php
    $conn->close();
?>