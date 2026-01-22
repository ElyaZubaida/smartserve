<?php
    session_start();
    date_default_timezone_set('Asia/Kuala_Lumpur');
    include 'config/db_connect.php';

    // Check if user is logged in
    if (!isset($_SESSION['student_id'])) {
        header('Location: login.php');
        exit();
    }

    // Check if order_id is provided
    if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
        header('Location: myorders.php');
        exit();
    }

    $student_id = $_SESSION['student_id'];
    $order_id = intval($_GET['order_id']);

    // Fetch order details
    $order_query = "SELECT * FROM orders WHERE order_ID = ? AND student_ID = ?";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("ii", $order_id, $student_id);
    $stmt->execute();
    $order_result = $stmt->get_result();
    $order = $order_result->fetch_assoc();

    if (!$order) {
        header('Location: myorders.php');
        exit();
    }

    // UPDATED QUERY: Added m.menuImage and om.om_request
    $items_query = "SELECT om.*, m.menuName, m.menuPrice, m.menuImage
                    FROM order_menu om
                    JOIN menus m ON om.menuID = m.menuID
                    WHERE om.order_ID = ?";
    $stmt = $conn->prepare($items_query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items_result = $stmt->get_result();
    $order_items = [];
    while ($row = $items_result->fetch_assoc()) {
        $order_items[] = $row;
    }

    // Format pickup time
    $pickup_time = date('h:i A', strtotime($order['pickup_time']));
    $order_date = date('d/m/Y', strtotime($order['order_date']));

    $completed_datetime = null;
    if (strtolower(trim($order['order_status'])) === 'completed' && !empty($order['completed_date'])) {
        $completed_datetime = date('d/m/Y, h:i A', strtotime($order['completed_date']));
    }

    if (!empty($order['pickup_time'])) {
        $pickup_time_display = date('h:i A', strtotime($order['pickup_time']));
    } else {
        $pickup_time_display = 'Immediately after ready';
    }

    $status_class = strtolower($order['order_status']);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SmartServe - Order Details</title>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
        <link rel="stylesheet" href="style.css">
        <style>
            /* Minimal styles to support the new elements */
            .item-with-img { display: flex; align-items: center; gap: 15px; margin-bottom: 10px; }
            .item-img { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; }
            .request-text { font-size: 0.85rem; color: #d32f2f; font-style: italic; display: block; margin-top: 2px; }
            .summary-item { height: auto !important; padding: 10px 0; }
        </style>
    </head>
    <body class="details-page">
        <header>
            <div class="menubar">
                <div class="logo"><img src="img/logo.png" alt="Smart Serve Logo"></div>
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

        <div class="order-details-container">
            <div class="section-header-box">
            <div class="header-title-group">
                <span class="material-symbols-outlined pulse-icon">receipt_long</span>
                <h1>Order Details</h1>
            </div>
            <p>View the details of your order and track its progress.</p>
        </div>
            <div class="back-nav"><a href="myorders.php"><span class="material-symbols-outlined">arrow_back</span> My Orders</a></div>

            <div class="details-main-card">
                <div class="details-header">
                    <div class="header-left">
                        <h1>Order #<?php echo htmlspecialchars($order['order_ID']); ?></h1>
                        <p>Placed on: <?php echo $order_date; ?></p>
                        <?php if ($completed_datetime): ?><p class="completed-info">Completed on: <?php echo $completed_datetime; ?></p><?php endif; ?>
                    </div>
                    <div class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($order['order_status']); ?></div>
                </div>

                <div class="order-tracker">
                    <?php
                    $statuses = ['Pending', 'Preparing', 'Ready', 'Completed'];
                    foreach ($statuses as $status):
                        $active = (strtolower($order['order_status']) === strtolower($status)) ? 'active' : '';
                    ?>
                        <div class="step <?php echo $active; ?>">
                            <?php
                            switch (strtolower($status)) {
                                case 'pending': echo '<span class="material-symbols-outlined">check_circle</span>'; break;
                                case 'preparing': echo '<span class="material-symbols-outlined">restaurant</span>'; break;
                                case 'ready': echo '<span class="material-symbols-outlined">shopping_bag</span>'; break;
                                case 'completed': echo '<span class="material-symbols-outlined">task_alt</span>'; break;
                            }
                            ?>
                            <p><?php echo $status; ?></p>
                        </div>
                    <?php endforeach; ?>
                    <div class="progress-line"></div>
                </div>

                <div class="details-grid">
                    <div class="items-summary">
                        <h3>Items Ordered</h3>
                        <?php $total_paid = 0; ?>
                        <?php foreach ($order_items as $item): 
                            $imgPath = (strpos($item['menuImage'], 'img/') === false) ? 'img/' . $item['menuImage'] : $item['menuImage'];
                        ?>
                            <div class="summary-item">
                                <div class="item-with-img">
                                    <img src="<?php echo htmlspecialchars($imgPath); ?>" onerror="this.src='img/default_food.png'" class="item-img">
                                    
                                    <div>
                                        <span><?php echo htmlspecialchars($item['menuName']); ?> x<?php echo $item['om_quantity']; ?></span>
                                        <?php if(!empty($item['om_request'])): ?>
                                            <span class="request-text">Note: "<?php echo htmlspecialchars($item['om_request']); ?>"</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span>RM <?php echo number_format($item['om_subtotal'], 2); ?></span>
                            </div>
                            <?php $total_paid += $item['om_subtotal']; ?>
                        <?php endforeach; ?>
                        <hr>
                        <div class="summary-total">
                            <span>Total Paid</span>
                            <span>RM <?php echo number_format($total_paid, 2); ?></span>
                        </div>
                    </div>

                    <div class="pickup-info-card">
                        <h3>Pickup Information</h3>
                        <div class="info-row">
                            <span class="material-symbols-outlined">schedule</span>
                            <div><strong>Pickup Time</strong><p><?php echo $pickup_time_display; ?></p></div>
                        </div>
                        <div class="info-row">
                            <span class="material-symbols-outlined">location_on</span>
                            <div><strong>Location</strong><p>Dataran Cendekia</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>