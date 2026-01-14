<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Order Details</title>
    <link rel="stylesheet" href="sastyle.css">

    <style>
        body {
            overflow-x: hidden;
        }

        .main-content {
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin-left: 250px;
        }

        /* Order Details Container - CENTERED */
        .order-details-wrapper {
            max-width: 650px;
            width: 100%;
            margin: 20px auto;
            background-color: #d3d3d3;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Order Number Header - CENTERED */
        .order-number-header {
            background-color: white;
            padding: 12px 25px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 18px;
        }

        .order-number-header h2 {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
        }

        /* Order Items Box */
        .order-items-box {
            background-color: #b8b8b8;
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #888;
        }

        .order-item:last-child {
            border-bottom: none;
            margin-bottom: 18px;
        }

        .item-image {
            width: 55px;
            height: 55px;
            min-width: 55px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            overflow: hidden;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .item-quantity {
            font-size: 14px;
            margin-left: 8px;
        }

        .item-request {
            font-size: 12px;
            color: #333;
        }

        /* Status Update - ALIGNED */
        .status-update-box {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-top: 15px;
        }

        /* Dropdown - ALIGNED WITH BUTTON */
        .status-dropdown {
            width: 100%;
            max-width: 380px;
            height: 42px;
            padding: 0 18px;
            border-radius: 22px;
            border: 1px solid #ccc;
            background-color: #fff;
            font-size: 14px;
            cursor: pointer;
            appearance: menulist;
            -webkit-appearance: menulist;
            -moz-appearance: menulist;
        }

        /* Update Button - ALIGNED WITH DROPDOWN */
        .update-btn {
            padding: 0 32px;
            border-radius: 22px;
            border: none;
            background-color: #000;
            color: white;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            height: 42px;
            white-space: nowrap;
        }

        .update-btn:hover {
            background-color: #333;
        }

        /* Messages */
        .success-message {
            background-color: #e8f5e9;
            color: #228B22;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            max-width: 650px;
            width: 100%;
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            max-width: 650px;
            width: 100%;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 10px;
            }

            .status-update-box {
                flex-direction: column;
            }

            .status-dropdown,
            .update-btn {
                width: 100%;
                max-width: 100%;
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

    <!-- Main content -->
    <div class="main-content">

        <?php
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo '<div class="success-message">✓ Order status updated successfully!</div>';
        }

        if (isset($_GET['error']) && $_GET['error'] == 1) {
            echo '<div class="error-message">❌ Failed to update order status.</div>';
        }

        include('../databaseconnect.php');

        $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $query = "SELECT * FROM orders WHERE order_id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        ?>

        <div class="order-details-wrapper">

            <div class="order-number-header">
                <h2>Order No: <?php echo htmlspecialchars($order['order_number']); ?></h2>
            </div>

            <div class="order-items-box">
                <?php
                $items_query = "SELECT oi.*, m.name, m.image, m.description
                                FROM order_items oi
                                JOIN menu m ON oi.menu_id = m.menu_id
                                WHERE oi.order_id = ?";
                $stmt2 = mysqli_prepare($connection, $items_query);
                mysqli_stmt_bind_param($stmt2, "i", $order_id);
                mysqli_stmt_execute($stmt2);
                $items = mysqli_stmt_get_result($stmt2);

                while ($item = mysqli_fetch_assoc($items)) {
                    $img = !empty($item['image']) ? '../img/'.$item['image'] : '../img/default-food.jpg';
                ?>
                <div class="order-item">
                    <div class="item-image">
                        <img src="<?php echo $img; ?>">
                    </div>
                    <div class="item-details">
                        <div class="item-name">
                            <?php echo $item['name']; ?>
                            <span class="item-quantity">x <?php echo $item['quantity']; ?></span>
                        </div>
                        <div class="item-request">
                            Request: <?php echo $item['description']; ?>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <form method="POST" action="update_order_status.php" class="status-update-box">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

                    <select name="status" class="status-dropdown" required>
                        <option value="" disabled selected>Order Status</option>
                        <option value="Pending" <?php if($order['status']=='Pending') echo 'selected'; ?>>Pending</option>
                        <option value="Preparing" <?php if($order['status']=='Preparing') echo 'selected'; ?>>Preparing</option>
                        <option value="Ready for pickup" <?php if($order['status']=='Ready for pickup') echo 'selected'; ?>>Ready for Pickup</option>
                        <option value="Completed" <?php if($order['status']=='Completed') echo 'selected'; ?>>Completed</option>
                        <option value="Cancelled" <?php if($order['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>

                    <button type="submit" class="update-btn">Update</button>
                </form>

            </div>
        </div>
    </div>

</body>
</html>