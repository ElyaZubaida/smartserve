<!-- 
 Frontend: Elya 
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
        // Order not found or does not belong to this user
        header('Location: myorders.php');
        exit();
    }

    // Fetch order items
    $items_query = "SELECT om.*, m.menuName, m.menuPrice
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

    // Format completed time (if order is completed)
    $completed_datetime = null;
    if (
        strtolower(trim($order['order_status'])) === 'completed'
        && !empty($order['completed_date'])
    ) {
        $completed_datetime = date(
            'd/m/Y, h:i A',
            strtotime($order['completed_date'])
        );
    }

    // Handle pickup time (optional)
    if (!empty($order['pickup_time'])) {
        $pickup_time_display = date('h:i A', strtotime($order['pickup_time']));
    } else {
        $pickup_time_display = 'Immediately after ready';
    }

    // Map order status to CSS class
    $status_class = strtolower($order['order_status']); // e.g., pending, preparing, ready, completed
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SmartServe - Order Details</title>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
        <link rel="stylesheet" href="style.css">
    </head>
    <body class="details-page">
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

        <div class="order-details-container">
            <div class="checkout-title">
                <h1>Order Details</h1>
            </div>
            <div class="back-nav">
                <a href="myorders.php"><span class="material-symbols-outlined">arrow_back</span> My Orders</a>
            </div>

            <div class="details-main-card">
                <div class="details-header">
                    <div class="header-left">
                        <h1>Order #<?php echo htmlspecialchars($order['order_ID']); ?></h1>
                        <p>Placed on: <?php echo $order_date; ?></p>

                        <?php if ($completed_datetime): ?>
                            <p class="completed-info">
                                Completed on: <?php echo $completed_datetime; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="status-badge <?php echo $status_class; ?>">
                        <?php echo htmlspecialchars($order['order_status']); ?>
                    </div>
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
                        <?php foreach ($order_items as $item): ?>
                            <div class="summary-item">
                                <span><?php echo htmlspecialchars($item['menuName']); ?> x<?php echo $item['om_quantity']; ?></span>
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
                            <div>
                                <strong>Pickup Time</strong>
                                <p><?php echo $pickup_time_display; ?></p>
                            </div>
                        </div>
                        <div class="info-row">
                            <span class="material-symbols-outlined">location_on</span>
                            <div>
                                <strong>Location</strong>
                                <p>Dataran Cendekia</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>