<!-- 
 Frontend: Insyirah 
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

    // Fetch cart items with menu details
    $query = "SELECT cm.cart_ID, cm.menuID, m.menuName, m.menuImage, m.menuPrice, 
                     cm.cm_quantity, cm.cm_subtotal, cm.cm_request
              FROM carts c
              JOIN cart_menu cm ON c.cart_ID = cm.cart_ID
              JOIN menus m ON cm.menuID = m.menuID
              WHERE c.student_ID = ?
              ORDER BY cm.cart_ID";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Calculate totals
    $cart_items = [];
    $subtotal = 0;

    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $subtotal += $row['cm_subtotal'];
    }

    // If cart is empty, redirect to cart page
    if (empty($cart_items)) {
        header('Location: cart.php');
        exit();
    }

    $grand_total = $subtotal; // Add tax/service charge here if needed
?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SmartServe - Place Order</title>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
        <link rel="stylesheet" href="style.css">
    </head>
    <body class="place-order-page">

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

        <div class="checkout-container">
            <div class="section-header-box">
                <div class="header-title-group">
                    <span class="material-symbols-outlined">receipt_long</span>
                    <h1>Review & Place Order</h1>
                </div>
                <p>Please check your items before confirming</p>
            </div>

            <div class="checkout-content">
                <div class="order-summary-header">
                <h3><span class="material-symbols-outlined">restaurant_menu</span> Your Selection</h3>
            </div>
            
            <div class="order-items-list">
                <?php foreach ($cart_items as $item): 
                    // SAFE IMAGE PATH LOGIC
                    $imgPath = $item['menuImage'];
                    if (strpos($imgPath, 'img/') === false) { 
                        $imgPath = 'img/' . $imgPath; 
                    }
                ?>
                <div class="order-item-card">
                    <div class="item-img-container">
                        <img src="<?php echo htmlspecialchars($imgPath); ?>" 
                             alt="<?php echo htmlspecialchars($item['menuName']); ?>">
                    </div>
                    <div class="item-info">
                        <span class="item-name"><?php echo htmlspecialchars($item['menuName']); ?></span>
                        <span class="item-price">RM <?php echo number_format($item['menuPrice'], 2); ?></span>
                        
                        <?php if (!empty($item['cm_request'])): ?>
                            <span class="item-request">
                                <span class="material-symbols-outlined">edit_note</span>
                                <?php echo htmlspecialchars($item['cm_request']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="item-qty-badge">x<?php echo $item['cm_quantity']; ?></div>
                    <div class="item-subtotal">RM <?php echo number_format($item['cm_subtotal'], 2); ?></div>
                </div>
                <?php endforeach; ?>
            </div>

                <div class="checkout-footer-card">
                    <form action="processorder.php" method="POST" class="order-final-form">
                        <div class="pickup-box">
                            <div class="icon-label">
                                <span class="material-symbols-outlined">schedule</span>
                                <label>When will you pick this up?</label>
                            </div>
                            <select name="pickup_time" class="pickup-select-styled" required>
                                <option value="" disabled selected>Select a time slot</option>
                                <option value="10:00:00">10.00 AM</option>
                                <option value="10:30:00">10.30 AM</option>
                                <option value="11:00:00">11.00 AM</option>
                                <option value="11:30:00">11.30 AM</option>
                                <option value="12:00:00">12.00 PM</option>
                                <option value="12:30:00">12.30 PM</option>
                                <option value="13:00:00">1.00 PM</option>
                                <option value="13:30:00">1.30 PM</option>
                                <option value="14:00:00">2.00 PM</option>
                                <option value="14:30:00">2.30 PM</option>
                                <option value="15:00:00">3.00 PM</option> 
                                <option value="15:30:00">3.30 PM</option>
                                <option value="16:00:00">4.00 PM</option>
                                <option value="16:30:00">4.30 PM</option>
                                <option value="17:00:00">5.00 PM</option>
                                <option value="17:30:00">5.30 PM</option>
                                <option value="18:00:00">6.00 PM</option>
                                <option value="18:30:00">6.30 PM</option>
                                <option value="19:00:00">7.00 PM</option>
                                <option value="19:30:00">7.30 PM</option>
                                <option value="20:00:00">8.00 PM</option>
                                <option value="20:30:00">8.30 PM</option>
                                <option value="21:00:00">9.00 PM</option>
                                <option value="21:30:00">9.30 PM</option>
                            </select>
                        </div>

                        <div class="final-total-box">
                            <div class="total-row">
                                <span>Subtotal</span>
                                <span>RM <?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="total-row main">
                                <span>Grand Total</span>
                                <span>RM <?php echo number_format($grand_total, 2); ?></span>
                            </div>
                            <button type="submit" class="place-order-confirm-btn">
                                Confirm & Place Order <span class="material-symbols-outlined">arrow_forward</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>