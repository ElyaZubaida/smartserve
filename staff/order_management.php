<!-- 
 Frontend: Qai 
 Backend: Amirah
 -->

<?php
session_start();

if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}
// Include database connection
include '../config/db_connect.php';

// Get filter parameter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Build query based on filter
$query = "
    SELECT 
        o.order_ID, 
        o.order_date,
        o.order_status, 
        s.student_name,
        o.order_totalAmount
    FROM 
        orders o
    JOIN 
        students s ON o.student_ID = s.student_ID
";

// Add WHERE clause if filter is not 'all'
if ($status_filter != 'all') {
    $query .= " WHERE o.order_status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
}

$query .= " ORDER BY o.order_ID DESC";

$result = $conn->query($query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Order Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="sastyle.css">
    <style>
        /* Filter Section Styles */
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filter-container {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 20px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #666;
        }

        .filter-btn:hover {
            border-color: #007bff;
            color: #007bff;
            background: #f0f7ff;
        }

        .filter-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        /* Status-specific filter button colors */
        .filter-btn.filter-all.active {
            background: #6c757d;
            border-color: #6c757d;
        }

        .filter-btn.filter-pending.active {
            background: #ffc107;
            border-color: #ffc107;
            color: #000;
        }

        .filter-btn.filter-preparing.active {
            background: #17a2b8;
            border-color: #17a2b8;
        }

        .filter-btn.filter-ready.active {
            background: #007bff;
            border-color: #007bff;
        }

        .filter-btn.filter-completed.active {
            background: #28a745;
            border-color: #28a745;
        }

        .filter-btn.filter-cancelled.active {
            background: #dc3545;
            border-color: #dc3545;
        }

        .order-count {
            margin-left: auto;
            padding: 8px 16px;
            background: #f8f9fa;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            color: #666;
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
                    <li><a href="dashboard.php"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                    <li><a href="menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li class="active"><a href="order_management.php"><span class="material-symbols-outlined">order_approve</span> Orders</a></li>
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

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-container">
                <span class="filter-label">Filter by Status:</span>
                <div class="filter-buttons">
                    <a href="order_management.php?status=all" 
                       class="filter-btn filter-all <?php echo ($status_filter == 'all') ? 'active' : ''; ?>">
                        All Orders
                    </a>
                    <a href="order_management.php?status=Pending" 
                       class="filter-btn filter-pending <?php echo ($status_filter == 'Pending') ? 'active' : ''; ?>">
                        Pending
                    </a>
                    <a href="order_management.php?status=Preparing" 
                       class="filter-btn filter-preparing <?php echo ($status_filter == 'Preparing') ? 'active' : ''; ?>">
                        Preparing
                    </a>
                    <a href="order_management.php?status=Ready for Pickup" 
                       class="filter-btn filter-ready <?php echo ($status_filter == 'Ready for Pickup') ? 'active' : ''; ?>">
                        Ready for Pickup
                    </a>
                    <a href="order_management.php?status=Completed" 
                       class="filter-btn filter-completed <?php echo ($status_filter == 'Completed') ? 'active' : ''; ?>">
                        Completed
                    </a>
                    <a href="order_management.php?status=Cancelled" 
                       class="filter-btn filter-cancelled <?php echo ($status_filter == 'Cancelled') ? 'active' : ''; ?>">
                        Cancelled
                    </a>
                </div>
                <span class="order-count">
                    <?php echo $result->num_rows; ?> Order<?php echo ($result->num_rows != 1) ? 's' : ''; ?>
                </span>
            </div>
        </div>

        <div class="orders-container">
            <?php if ($result->num_rows > 0): ?>
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
                        while ($row = $result->fetch_assoc()) {
                            // Determine status class
                            $statusClass = '';
                            switch(strtolower($row['order_status'])) {
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
                            <td>
                                Order No: <?php echo htmlspecialchars($row['order_ID']); ?>
                                <br>
    
                            </td>
                            <td>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($row['order_status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="s_orderdetails.php?id=<?php echo $row['order_ID']; ?>" class="view-btn">View</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No Orders Found</h3>
                    <p>
                        <?php 
                        if ($status_filter == 'all') {
                            echo 'There are currently no orders to display.';
                        } else {
                            echo 'No orders found with status: <strong>' . htmlspecialchars($status_filter) . '</strong>';
                        }
                        ?>
                    </p>
                    <?php if ($status_filter != 'all'): ?>
                        <a href="order_management.php?status=all" class="filter-btn" style="margin-top: 10px; display: inline-block;">
                            View All Orders
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
</body>
</html>

<?php
// Close database connection
$conn->close();
?>