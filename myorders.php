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

    // Fetch all orders for this student
    $query = "SELECT * FROM orders 
            WHERE student_ID = ? 
            ORDER BY order_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SmartServe - My Orders</title>
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

        <div class="orders-container">
            <div class="orders-title">
                <h1>My Orders</h1>
            </div>

            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <?php
                        $status_class = strtolower($order['order_status']); // e.g., pending, preparing, ready, completed
                        $order_date = date('d/m/Y, h:i A', strtotime($order['order_date']));
                        $cancel_disabled = strtolower($order['order_status']) === 'completed' ? 'disabled btn-dimmed' : '';
                        $cancel_title = strtolower($order['order_status']) === 'completed' ? 'Completed orders cannot be cancelled' : '';
                    ?>
                    <div class="order-card <?php echo ($status_class === 'completed') ? 'order-completed-style' : ''; ?>">
                        <div class="order-left">
                            <div class="order-info-group">
                                <span class="order-number">Order #<?php echo $order['order_ID']; ?></span>
                                <div class="status-badge <?php echo $status_class; ?>">
                                    <?php
                                        switch ($status_class) {
                                            case 'pending': echo '<span class="material-symbols-outlined">restaurant</span>'; break;
                                            case 'preparing': echo '<span class="material-symbols-outlined">kitchen</span>'; break;
                                            case 'ready': echo '<span class="material-symbols-outlined">shopping_bag</span>'; break;
                                            case 'completed': echo '<span class="material-symbols-outlined">task_alt</span>'; break;
                                        }
                                    ?>
                                    <?php echo htmlspecialchars($order['order_status']); ?>
                                </div>
                            </div>
                            <span class="order-date"><?php echo $order_date; ?></span>
                        </div>
                        
                        <div class="order-right-stack">
                            <button class="order-details-btn" onclick="location.href='orderdetails.php?order_id=<?php echo $order['order_ID']; ?>'">
                                <span class="material-symbols-outlined">visibility</span> Details
                            </button>
                            <button class="cancel-order-btn <?php echo $cancel_disabled; ?>" 
                                    <?php echo $cancel_disabled ? 'disabled' : ''; ?> 
                                    title="<?php echo $cancel_title; ?>"
                                    onclick="if(confirm('Are you sure you want to cancel this order?')) location.href='cancelorder.php?order_id=<?php echo $order['order_ID']; ?>';">
                                <span class="material-symbols-outlined"><?php echo $cancel_disabled ? 'block' : 'delete'; ?></span> Cancel
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have no orders yet. <a href="menu.php">Start ordering!</a></p>
            <?php endif; ?>
        </div>
    </body>
</html>
