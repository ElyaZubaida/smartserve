<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Order Details</title>
    <link rel="stylesheet" href="sastyle.css">
    
    <style>
        /* ADDITIONAL CSS - Following mockup design */
        
        /* Order Details Container */
        .order-details-wrapper {
            max-width: 800px;
            margin: 50px auto;
            background-color: #d3d3d3;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Order Number Header */
        .order-number-header {
            background-color: white;
            padding: 15px 30px;
            border-radius: 25px;
            text-align: center;
            margin-bottom: 30px;
            display: inline-block;
            margin-left: 50%;
            transform: translateX(-50%);
        }

        .order-number-header h2 {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }

        /* Order Items Section */
        .order-items-box {
            background-color: #b8b8b8;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #888;
        }

        .order-item:last-child {
            border-bottom: none;
            margin-bottom: 30px;
        }

        .item-image {
            width: 80px;
            height: 80px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 40px;
            flex-shrink: 0;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .item-quantity {
            font-size: 16px;
            margin-left: 10px;
        }

        .item-request {
            font-size: 14px;
            color: #333;
            margin-top: 5px;
        }

        /* Status Update Section */
        .status-update-box {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
        }

        .status-dropdown {
            padding: 12px 20px;
            border-radius: 25px;
            border: none;
            background-color: white;
            font-size: 16px;
            cursor: pointer;
            min-width: 200px;
        }

        .update-btn {
            padding: 12px 40px;
            border-radius: 25px;
            border: none;
            background-color: #000;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .update-btn:hover {
            background-color: #333;
        }

        /* Back Button */
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #666;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #333;
        }

        /* Success/Error Messages */
        .success-message {
            background-color: #e8f5e9;
            color: #228B22;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .order-details-wrapper {
                padding: 20px;
            }

            .status-update-box {
                flex-direction: column;
            }

            .status-dropdown,
            .update-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2 style="color: white;">SmartServe</h2>
        </div>

        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="menu_management.php">Menu Management</a></li>
                <li><a href="order_management.php">Order Management</a></li>
                <li><a href="report.php">Reports</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="../logout.php">Log Out</a></li>
            </ul>
        </nav>
    </div>

    <!-- Main content area -->
    <div class="main-content">
        <a href="order_management.php" class="back-btn">← Back to Orders</a>

        <?php
        // Success message
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo '<div class="success-message">✓ Order status updated successfully!</div>';
        }

        // Error message
        if (isset($_GET['error']) && $_GET['error'] == 1) {
            echo '<div class="error-message">❌ Failed to update order status. Please try again.</div>';
        }

        // Database connection
        include('../databaseconnect.php');

        // Check connection
        if (!$connection) {
            echo '<div class="error-message">❌ Database connection failed!</div>';
            exit;
        }

        // Get order ID from URL
        $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($order_id == 0) {
            echo '<div class="error-message">❌ Invalid order ID!</div>';
            exit;
        }

        // Fetch order details
        $query = "SELECT * FROM orders WHERE order_id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $order = mysqli_fetch_assoc($result);

        if (!$order) {
            echo '<div class="error-message">❌ Order not found!</div>';
            exit;
        }
        ?>

        <!-- Order Details Container -->
        <div class="order-details-wrapper">
            <!-- Order Number Header -->
            <div class="order-number-header">
                <h2>Order No: <?php echo htmlspecialchars($order['order_number']); ?></h2>
            </div>

            <!-- Order Items Box -->
            <div class="order-items-box">
                <?php
                // Fetch order items
                $items_query = "SELECT oi.*, m.name as item_name, m.price, m.description 
                              FROM order_items oi 
                              JOIN menu m ON oi.menu_id = m.menu_id 
                              WHERE oi.order_id = ?";
                $items_stmt = mysqli_prepare($connection, $items_query);
                mysqli_stmt_bind_param($items_stmt, "i", $order_id);
                mysqli_stmt_execute($items_stmt);
                $items_result = mysqli_stmt_get_result($items_stmt);

                // Map menu items to emojis
                $item_emojis = [
                    'nasi lemak' => '🍚',
                    'roti canai' => '🫓',
                    'mee goreng' => '🍜',
                    'teh tarik' => '🧋',
                    'nasi goreng' => '🍛',
                    'default' => '🍽️'
                ];

                if (mysqli_num_rows($items_result) == 0) {
                    echo "<p style='text-align: center; color: #666;'>No items in this order</p>";
                } else {
                    while($item = mysqli_fetch_assoc($items_result)) {
                        // Get emoji for item
                        $item_name_lower = strtolower($item['item_name']);
                        $emoji = $item_emojis[$item_name_lower] ?? $item_emojis['default'];
                        
                        // Get description as request (if exists)
                        $request = !empty($item['description']) ? $item['description'] : '-';
                ?>
                <div class="order-item">
                    <div class="item-image">
                        <?php echo $emoji; ?>
                    </div>
                    <div class="item-details">
                        <div class="item-name">
                            <?php echo htmlspecialchars($item['item_name']); ?>
                            <span class="item-quantity">x <?php echo $item['quantity']; ?></span>
                        </div>
                        <div class="item-request">
                            Request: <?php echo htmlspecialchars($request); ?>
                        </div>
                    </div>
                </div>
                <?php 
                    }
                }
                ?>

                <!-- Status Update Form -->
                <form method="POST" action="update_order_status.php" class="status-update-box">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    
                    <select name="status" class="status-dropdown" required>
                        <option value="">Order Status ▼</option>
                        <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="Preparing" <?php echo ($order['status'] == 'Preparing') ? 'selected' : ''; ?>>Preparing</option>
                        <option value="Ready for pickup" <?php echo ($order['status'] == 'Ready for pickup') ? 'selected' : ''; ?>>Ready for Pickup</option>
                        <option value="Completed" <?php echo ($order['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="Cancelled" <?php echo ($order['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    
                    <button type="submit" class="update-btn">Update</button>
                </form>
            </div>
        </div>

        <?php
        // Close connection
        mysqli_close($connection);
        ?>
    </div>
    
</body>
</html>