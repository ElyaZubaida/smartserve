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

    // Fetch cart items with menu details and initial availability check
    $query = "SELECT cm.cart_ID, cm.menuID, m.menuName, m.menuImage, m.menuPrice, m.menuAvailability,
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

    $cart_items = [];
    $subtotal = 0;
    $initial_can_proceed = true; 

    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $subtotal += $row['cm_subtotal'];
        if ($row['menuAvailability'] == 0) {
            $initial_can_proceed = false;
        }
    }

    if (empty($cart_items)) {
        header('Location: cart.php');
        exit();
    }

    $grand_total = $subtotal;

    function generateTimeSlots() {
        $slots = [];
        $now = new DateTime();
        $minutes = (int)$now->format('i');
        $roundedMinutes = ceil($minutes / 15) * 15;

        if ($roundedMinutes >= 60) {
            $now->modify('+1 hour');
            $roundedMinutes = 0;
        }
        $now->setTime((int)$now->format('H'), $roundedMinutes, 0);

        $businessStart = new DateTime();
        $businessStart->setTime(10, 0, 0);
        $businessEnd = new DateTime();
        $businessEnd->setTime(21, 45, 0);

        if ($now < $businessStart) {
            $slotTime = clone $businessStart;
        } elseif ($now > $businessEnd) {
            return $slots; 
        } else {
            $slotTime = clone $now;
        }

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
    <title>SmartServe - Review Order</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
    <style>
        /* Essential styles for live protection */
        .order-item-card.out-of-stock {
            border: 2px solid #ef5350 !important;
            background-color: #fff8f8;
            position: relative;
        }
        .stock-badge-alert {
            color: #d32f2f;
            font-weight: 800;
            font-size: 0.7rem;
            text-transform: uppercase;
            background: #ffebee;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 8px;
            border: 1px solid #d32f2f;
        }
        .btn-disabled {
            background-color: #b0bec5 !important;
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
        }
        .stock-error-notice {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #fff3cd;
            color: #856404;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 15px;
            border: 1px solid #ffeeba;
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="place-order-page">

    <header>
        <div class="menubar">
            <div class="logo"><img src="img/logo.png" alt="Logo"></div>
            <nav>
                <ul>
                    <li><a href="menu.php"><span class="material-symbols-outlined">home</span> Home</a></li>
                    <li><a href="ai.php"><span class="material-symbols-outlined">psychology</span> Recommendation</a></li>
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
            <p>Finalize your selection before confirmation</p>
        </div>

        <div class="back-nav">
            <a href="cart.php" class="back-link">
                <span class="material-symbols-outlined">chevron_left</span> Back to Cart
            </a>
        </div>

        <div class="checkout-content">
            <div class="order-summary-header">
                <h3><span class="material-symbols-outlined">restaurant_menu</span> Your Selection</h3>
            </div>
        
            <div class="order-items-list">
                <?php foreach ($cart_items as $item): 
                    $imgPath = (strpos($item['menuImage'], 'img/') === false) ? 'img/' . $item['menuImage'] : $item['menuImage'];
                    $is_out = ($item['menuAvailability'] == 0);
                ?>
                <div class="order-item-card <?php echo $is_out ? 'out-of-stock' : ''; ?>" data-id="<?php echo $item['menuID']; ?>">
                    <div class="item-img-container">
                        <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Food">
                    </div>
                    <div class="item-info">
                        <span class="item-name">
                            <?php echo htmlspecialchars($item['menuName']); ?>
                            <?php if($is_out): ?><span class="stock-badge-alert">Sold Out</span><?php endif; ?>
                        </span>
                        <span class="item-price">RM <?php echo number_format($item['menuPrice'], 2); ?></span>
                        <?php if (!empty($item['cm_request'])): ?>
                            <span class="item-request"><span class="material-symbols-outlined">edit_note</span> <?php echo htmlspecialchars($item['cm_request']); ?></span>
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
                            <label>Pick-up Time</label>
                        </div>
                        <select name="pickup_time" class="pickup-select-styled">
                            <option value="">Pick up immediately</option>
                            <?php foreach ($timeSlots as $slot): ?>
                                <option value="<?php echo $slot['value']; ?>"><?php echo $slot['display']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="final-total-box">
                        <div class="total-row"><span>Subtotal</span><span>RM <?php echo number_format($subtotal, 2); ?></span></div>
                        <div class="total-row main"><span>Grand Total</span><span>RM <?php echo number_format($grand_total, 2); ?></span></div>

                        <div id="liveNoticeContainer">
                            <?php if (!$initial_can_proceed): ?>
                                <div class="stock-error-notice">
                                    <span class="material-symbols-outlined">warning</span>
                                    <p>Some items are out of stock. Please return to the cart to remove them.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <button type="button" id="mainCheckoutBtn" onclick="showConfirmModal()" 
                                class="place-order-confirm-btn <?php echo !$initial_can_proceed ? 'btn-disabled' : ''; ?>"
                                <?php echo !$initial_can_proceed ? 'disabled' : ''; ?>>
                            <?php echo $initial_can_proceed ? 'Confirm & Place Order' : 'Checkout Blocked'; ?>
                            <span class="material-symbols-outlined"><?php echo $initial_can_proceed ? 'arrow_forward' : 'block'; ?></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
        // --- LIVE MONITORING LOGIC ---
        function monitorStockLive() {
            fetch('check_stock.php')
                .then(res => res.json())
                .then(unavailableIDs => {
                    let hasError = false;
                    document.querySelectorAll('.order-item-card').forEach(card => {
                        const id = parseInt(card.getAttribute('data-id'));
                        if (unavailableIDs.includes(id)) {
                            hasError = true;
                            card.classList.add('out-of-stock');
                            
                            // Update text to show it's completely unavailable
                            if (!card.querySelector('.stock-badge-alert')) {
                                card.querySelector('.item-name').insertAdjacentHTML('beforeend', '<span class="stock-badge-alert">Unavailable</span>');
                            }
                        }
                    });

                    if (hasError) {
                        const btn = document.getElementById('mainCheckoutBtn');
                        btn.disabled = true;
                        btn.classList.add('btn-disabled');
                        btn.innerHTML = 'Order Blocked <span class="material-symbols-outlined">block</span>';

                        if (!document.querySelector('.stock-error-notice')) {
                            const notice = `
                                <div class="stock-error-notice" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
                                    <span class="material-symbols-outlined">report</span>
                                    <p>One or more items are no longer available on the menu. Please go back and update your cart.</p>
                                </div>`;
                            document.getElementById('liveNoticeContainer').innerHTML = notice;
                        }
                    }
                });
        }
        setInterval(monitorStockLive, 3000); // Check every 3 seconds

        // --- ORDER SUBMISSION LOGIC ---
        function showConfirmModal() { document.getElementById('confirmModal').style.display = 'block'; }
        function closeConfirmModal() { document.getElementById('confirmModal').style.display = 'none'; }

        function submitOrder() {
            const formData = new FormData(document.getElementById('orderForm'));
            fetch('processorder.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                closeConfirmModal();
                if (data.success) {
                    showResultModal('success', 'Success!', data.message);
                    setTimeout(() => { window.location.href = 'myorders.php'; }, 2000);
                } else {
                    showResultModal('error', 'Failed', data.message);
                    // If backend rejected due to stock, trigger a refresh after user clicks OK
                }
            });
        }

        function showResultModal(type, title, message) {
            const modalHtml = `<div id="resultModal" class="modal" style="display: block;"><div class="modal-content"><span class="material-symbols-outlined modal-icon ${type}">${type === 'success' ? 'check_circle' : 'error'}</span><h2>${title}</h2><p>${message}</p><button class="modal-btn" onclick="location.reload()">OK</button></div></div>`;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }
    </script>
</body>
</html>