<?php
// Include database connection
include '../config/db_connect.php';

// Fetch orders from database
$query = "
    SELECT 
        `ORDER`.`ORDER_ID` AS order_id, 
        `ORDER`.`ORDER_DATE` AS order_date,
        `ORDER`.`ORDER_STATUS` AS status, 
        `STUDENT`.`STUDENT_NAME` AS student_name,
        `ORDER`.`ORDER_TOTAMOUNT` AS total_amount
    FROM 
        `ORDER`
    JOIN 
        `STUDENT` ON `ORDER`.`STUDENT_ID` = `STUDENT`.`STUDENT_ID`
    ORDER BY 
        `ORDER`.`ORDER_DATE` DESC
";

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
        /* ============================
        ORDERS PAGE
   ============================ */

/* Main Content Wrapper */
.orders-menu-content {
    margin-left: 260px; /* Aligned with sidebar */
    padding: 40px;
    background-color: #f8faf8;
    min-height: 100vh;
}

/* Table Container */
.orders-container {
    width: 100%;
    max-width: 900px;
    background: white;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); /* Soft shadow */
    border: 1px solid #e0ece0;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
    background-color: transparent;
    box-shadow: none; /* Shadow moved to container */
    table-layout: fixed;
}

/* Table Head */
.orders-table thead {
    background-color: #f0f4f0; /* Very light green tint */
}

.orders-table th {
    padding: 15px 10px;
    text-align: center;
    font-size: 14px;
    font-weight: 600;
    color: #1b5e20;
    border-bottom: 2px solid #e8f5e9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Table Body Rows */
.orders-table tbody tr {
    background-color: transparent; /* Removed blue background */
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.3s ease;
}

.orders-table tbody tr:hover {
    background-color: #f9fbf9; /* Subtle green highlight on hover */
}

.orders-table td {
    padding: 18px 10px;
    text-align: center;
    font-size: 15px;
    color: #444;
}

/* --- Status Badges (Themed) --- */
.status-badge {
    padding: 6px 14px;
    border-radius: 30px; /* Modern Pill shape */
    font-weight: 600;
    font-size: 12px;
    display: inline-block;
    text-transform: capitalize;
}

.status-completed {
    color: #2e7d32;
    background-color: #e8f5e9;
}

.status-pending {
    color: #d32f2f;
    background-color: #ffebee;
}

.status-cancelled {
    color: #666;
    background-color: #eeeeee;
}

.status-preparing {
    color: #ef6c00;
    background-color: #fff3e0;
}

.status-ready {
    color: #0056b3;
    background-color: #e3f2fd;
    border: 1px solid #bbdefb;
}

/* --- View Button (Green Theme) --- */
.view-btn {
    background-color: #2e7d32;
    color: white;
    padding: 8px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
    transition: all 0.3s ease;
}

.view-btn:hover {
    background-color: #1b5e20;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(46, 125, 50, 0.2);
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
                            <td>Order No: <?php echo htmlspecialchars($row['order_id']); ?></td>
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
            <?php else: ?>
                <div class="empty-state">
                    <h3>No Orders Found</h3>
                    <p>There are currently no orders to display.</p>
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