<!-- 
 Frontend: Insyirah 
 Backend: Aleesya
 -->
<?php
    session_start();
    date_default_timezone_set('Asia/Kuala_Lumpur');
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

    $grand_total = $subtotal;

    // Generate time slots (15-minute intervals)
    function generateTimeSlots() {
        $slots = [];

        $now = new DateTime();

        // Round UP to next 15-minute interval
        $minutes = (int)$now->format('i');
        $roundedMinutes = ceil($minutes / 15) * 15;

        if ($roundedMinutes >= 60) {
            $now->modify('+1 hour');
            $roundedMinutes = 0;
        }

        $now->setTime((int)$now->format('H'), $roundedMinutes, 0);

        // Business hours: 10:00 AM – 9:45 PM
        $businessStart = new DateTime();
        $businessStart->setTime(10, 0, 0);

        $businessEnd = new DateTime();
        $businessEnd->setTime(21, 45, 0);

        // Decide start time
        if ($now < $businessStart) {
            $slotTime = clone $businessStart;
        } elseif ($now > $businessEnd) {
            return $slots; // No slots available
        } else {
            $slotTime = clone $now;
        }

        // Generate slots every 15 minutes
        while ($slotTime <= $businessEnd) {
            $slots[] = [
                'value' => $slotTime->format('H:i:s'),
                'display' => $slotTime->format('g:i A')
            ];
            $slotTime->modify('+15 minutes');
        }

        return $slots;
    }

    $timeSlots = generateTimeSlots();
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
                    <form id="orderForm" method="POST" class="order-final-form">
                        <div class="pickup-box">
                            <div class="icon-label">
                                <span class="material-symbols-outlined">schedule</span>
                                <label>When will you pick this up?</label>
                            </div>
                            <select name="pickup_time" class="pickup-select-styled">
                                <option value="">Pick up immediately</option>
                                <?php foreach ($timeSlots as $slot): ?>
                                    <option value="<?php echo $slot['value']; ?>">
                                        <?php echo $slot['display']; ?>
                                    </option>
                                <?php endforeach; ?>
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
                            <button type="button" onclick="showConfirmModal()" class="place-order-confirm-btn">
                                Confirm & Place Order <span class="material-symbols-outlined">arrow_forward</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div id="confirmModal" class="modal">
            <div class="modal-content">
                <span class="material-symbols-outlined modal-icon warning">info</span>
                <h2>Confirm Order?</h2>
                <p>Are you sure you want to place this order?</p>
                <div class="modal-actions">
                    <button class="modal-btn-cancel" onclick="closeConfirmModal()">Cancel</button>
                    <button class="modal-btn-confirm" onclick="submitOrder()">Confirm</button>
                </div>
            </div>
        </div>

        <script>
            function showConfirmModal() {
                document.getElementById('confirmModal').style.display = 'block';
            }

            function closeConfirmModal() {
                document.getElementById('confirmModal').style.display = 'none';
            }

            function submitOrder() {
                const form = document.getElementById('orderForm');
                const formData = new FormData(form);

                fetch('processorder.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    closeConfirmModal();
                    if (data.success) {
                        showResultModal('success', 'Order Placed!', data.message);
                        setTimeout(() => {
                            window.location.href = 'myorders.php';
                        }, 2000);
                    } else {
                        showResultModal('error', 'Error', data.message);
                    }
                })
                .catch(error => {
                    closeConfirmModal();
                    showResultModal('error', 'Error', 'Something went wrong. Please try again.');
                });
            }

            function showResultModal(type, title, message) {
                const modalHtml = `
                    <div id="resultModal" class="modal" style="display: block;">
                        <div class="modal-content">
                            <span class="material-symbols-outlined modal-icon ${type}">
                                ${type === 'success' ? 'check_circle' : 'error'}
                            </span>
                            <h2>${title}</h2>
                            <p>${message}</p>
                            <button class="modal-btn" onclick="closeResultModal()">OK</button>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
            }

            function closeResultModal() {
                const modal = document.getElementById('resultModal');
                if (modal) {
                    modal.remove();
                }
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                const confirmModal = document.getElementById('confirmModal');
                if (event.target == confirmModal) {
                    closeConfirmModal();
                }
            }
        </script>
    </body>
</html>