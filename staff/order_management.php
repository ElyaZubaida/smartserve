<!-- 
 Frontend: Qai 
 Backend: Amirah
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Order Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
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
        <div class="sidebar-top">
            <div class="logo-container">
                <img src="../img/logo.png" alt="SmartServe Logo">
                <h3>Smart<span>Serve</span></h3>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="active"><a href="dashboard.php"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                    <li><a href="menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li><a href="order_management.php"><span class="material-symbols-outlined">order_approve</span> Orders</a></li>
                    <li><a href="report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
                    <li class="nav-divider"></li>
                    <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="../logout.php" class="logout-link"><span class="material-symbols-outlined">logout</span> Log Out</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Main content area -->
    <div class="main-content orders-menu-content">
        <div class="header">
            <div class="title">
                <h2>Order Management</h2>
                <p>Manage orders and their statuses</p>
            </div>
        </div>
    <div class="orders-container">
    <?php
    $mock_orders = [
        ['order_id' => 1, 'order_number' => '7721', 'status' => 'Preparing'],
        ['order_id' => 2, 'order_number' => '7722', 'status' => 'Ready for Pickup'],
        ['order_id' => 3, 'order_number' => '7723', 'status' => 'Completed'],
        ['order_id' => 4, 'order_number' => '7724', 'status' => 'Cancelled'],
        ['order_id' => 5, 'order_number' => '7725', 'status' => 'Pending']
    ];
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
            $no = 1;
            foreach($mock_orders as $row) {
                $statusClass = '';
                switch(strtolower($row['status'])) {
                    case 'completed': $statusClass = 'status-completed'; break;
                    case 'pending': $statusClass = 'status-pending'; break;
                    case 'cancelled': $statusClass = 'status-cancelled'; break;
                    case 'preparing': $statusClass = 'status-preparing'; break;
                    case 'ready for pickup': $statusClass = 'status-ready'; break;
                    default: $statusClass = 'status-pending';
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
                    <a href="s_orderdetails.php?id=<?php echo $row['order_id']; ?>" class="view-btn">View</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
    
</body>


</html>
