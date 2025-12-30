<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Order Management</title>
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

        /* Header - CENTERED */
        header {
            background-color: #f0f0f0;
            padding: 15px;
            text-align: center;
            margin-bottom: 30px;
            width: 100%;
            max-width: 900px;
            border-radius: 8px;
        }

        header h1 {
            font-size: 32px;
            color: #333;
            margin: 0;
        }

        /* Orders Container - CENTERED */
        .orders-container {
            margin-top: 15px;
            width: 100%;
            max-width: 900px;
            display: flex;
            justify-content: center;
        }

        .orders-table {
            width: 100%;
            max-width: 900px;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            table-layout: fixed;
        }

        .orders-table thead {
            background-color: #f0f0f0;
        }

        .orders-table th {
            padding: 12px 10px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #ddd;
        }

        /* Column widths - BALANCED */
        .orders-table th:nth-child(1) { width: 10%; }
        .orders-table th:nth-child(2) { width: 40%; }
        .orders-table th:nth-child(3) { width: 28%; }
        .orders-table th:nth-child(4) { width: 22%; }

        .orders-table tbody tr {
            background-color: #b8c5d6;
            border-bottom: 1px solid #999;
        }

        .orders-table tbody tr:hover {
            background-color: #a3b4c9;
        }

        .orders-table td {
            padding: 12px 10px;
            text-align: center;
            font-size: 14px;
            color: #333;
            word-wrap: break-word;
        }

        /* Status Badge */
        .status-badge {
            padding: 5px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 13px;
            display: inline-block;
        }

        .status-completed {
            color: #228B22;
            background-color: #e8f5e9;
        }

        .status-pending {
            color: #ff0000;
            background-color: #ffebee;
        }

        .status-cancelled {
            color: #333;
            background-color: #e0e0e0;
        }

        .status-preparing {
            color: #ff9800;
            background-color: #fff3e0;
        }

        .status-ready {
            color: #4caf50;
            background-color: #e8f5e9;
        }

        /* View Button */
        .view-btn {
            background-color: #333;
            color: white;
            padding: 7px 18px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .view-btn:hover {
            background-color: #555;
        }

        /* Error Message */
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 6px;
            margin: 15px 0;
            text-align: center;
            font-size: 13px;
            max-width: 900px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 30px 15px;
            color: #666;
        }

        .empty-state h3 {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 13px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                padding: 10px;
                margin-left: 0;
            }

            .orders-table {
                font-size: 12px;
            }
            
            .orders-table th,
            .orders-table td {
                padding: 8px 5px;
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
        <header>
            <h1>Orders</h1>
        </header>

        <!-- Orders Table -->
        <div class="orders-container">
            <?php
            // Database connection
            include('../databaseconnect.php');
            
            // Check connection
            if (!$connection) {
                echo '<div class="error-message">';
                echo '❌ Database connection failed: ' . mysqli_connect_error();
                echo '<br>Please check database settings.';
                echo '</div>';
                exit;
            }
            ?>

            <table class="orders-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Orders</th>
                        <th>Status</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch orders from database
                    $query = "SELECT order_id, order_number, status FROM orders ORDER BY order_id DESC";
                    $result = mysqli_query($connection, $query);
                    
                    // Check if query successful
                    if (!$result) {
                        echo "<tr><td colspan='4' style='color: red;'>";
                        echo "Error fetching orders: " . mysqli_error($connection);
                        echo "</td></tr>";
                    } else if (mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='4'>";
                        echo "<div class='empty-state'>";
                        echo "<h3>No orders found</h3>";
                        echo "<p>There are no orders in the system yet.</p>";
                        echo "</div>";
                        echo "</td></tr>";
                    } else {
                        $no = 1;
                        while($row = mysqli_fetch_assoc($result)) {
                            // Determine status class for styling
                            $statusClass = '';
                            switch(strtolower($row['status'])) {
                                case 'completed':
                                    $statusClass = 'status-completed';
                                    break;
                                case 'pending':
                                    $statusClass = 'status-pending';
                                    break;
                                case 'cancelled':
                                    $statusClass = 'status-cancelled';
                                    break;
                                case 'preparing':
                                    $statusClass = 'status-preparing';
                                    break;
                                case 'ready for pickup':
                                    $statusClass = 'status-ready';
                                    break;
                                default:
                                    $statusClass = 'status-pending';
                            }
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>Order No: <?php echo htmlspecialchars($row['order_number']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="order_details.php?id=<?php echo $row['order_id']; ?>" class="view-btn">View</a>
                        </td>
                    </tr>
                    <?php 
                        }
                    }
                    
                    // Close connection
                    mysqli_close($connection);
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
</body>
</html>