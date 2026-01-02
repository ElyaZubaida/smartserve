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
