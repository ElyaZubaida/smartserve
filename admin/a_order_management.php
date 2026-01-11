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
    <link rel="stylesheet" href="../staff/sastyle.css">
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
                    <li class="active"><a href="a_dashboard.php"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                    <li><a href="a_menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li><a href="a_order_management.php"><span class="material-symbols-outlined">order_approve</span> Order Management</a></li>
                    <li><a href="user_management.php"><span class="material-symbols-outlined">manage_accounts</span> User Management</a></li>
                    <li><a href="a_report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
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
                    <a href="a_orderdetails.php?id=<?php echo $row['order_id']; ?>" class="view-btn">View</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
    
</body>


</html>
