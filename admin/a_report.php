<!-- 
 Frontend: Qai 
 Backend: Amirah, Qis
 -->
<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginadmin.php");
    exit;
}


include '../config/db_connect.php';

// Get filter values
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'all';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Calculate TOTAL REVENUE (All-time, completed orders only - NOT affected by filters)
$total_revenue_query = "SELECT SUM(order_totalAmount) as total_revenue FROM orders WHERE order_status = 'Completed'";
$total_revenue_result = mysqli_query($conn, $total_revenue_query);
$total_revenue = mysqli_fetch_assoc($total_revenue_result)['total_revenue'] ?? 0;

// Build query based on filters
$query = "
    SELECT 
        o.order_ID,
        o.order_date,
        o.order_totalAmount,
        o.order_amountPaid,
        o.order_status,
        s.student_name,
        CASE 
            WHEN o.staffID IS NOT NULL THEN CONCAT(st.staffName, ' (Staff)')
            WHEN o.admin_ID IS NOT NULL THEN CONCAT(a.admin_name, ' (Admin)')
            ELSE '-'
        END AS updated_by,
        GROUP_CONCAT(m.menuName SEPARATOR ', ') AS menu_items,
        SUM(om.om_quantity) AS total_qty
    FROM 
        orders o
    JOIN 
        students s ON o.student_ID = s.student_ID
    LEFT JOIN 
        staff st ON o.staffID = st.staffID
    LEFT JOIN
        admins a ON o.admin_ID = a.admin_ID
    JOIN 
        order_menu om ON o.order_ID = om.order_ID
    JOIN 
        menus m ON om.menuID = m.menuID
    WHERE 1=1
";

// Add date filters if provided
if (!empty($date_from)) {
    $query .= " AND DATE(o.order_date) >= '" . mysqli_real_escape_string($conn, $date_from) . "'";
}
if (!empty($date_to)) {
    $query .= " AND DATE(o.order_date) <= '" . mysqli_real_escape_string($conn, $date_to) . "'";
}

// Filter by report type
if ($report_type == 'completed') {
    $query .= " AND o.order_status = 'Completed'";
} elseif ($report_type == 'cancelled') {
    $query .= " AND o.order_status = 'Cancelled'";
} elseif ($report_type == 'pending') {
    $query .= " AND o.order_status = 'Pending'";
}

$query .= " GROUP BY o.order_ID ORDER BY o.order_ID DESC";

$result = mysqli_query($conn, $query);

// Calculate TOTAL SALES (Affected by filters - completed orders only)
$sales_query = "SELECT SUM(order_totalAmount) as total_sales FROM orders WHERE order_status = 'Completed'";

if (!empty($date_from)) {
    $sales_query .= " AND DATE(order_date) >= '" . mysqli_real_escape_string($conn, $date_from) . "'";
}
if (!empty($date_to)) {
    $sales_query .= " AND DATE(order_date) <= '" . mysqli_real_escape_string($conn, $date_to) . "'";
}

$sales_result = mysqli_query($conn, $sales_query);
$total_sales = mysqli_fetch_assoc($sales_result)['total_sales'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Report</title>
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
                    <li><a href="a_dashboard.php"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                    <li><a href="a_menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li><a href="a_order_management.php"><span class="material-symbols-outlined">order_approve</span> Orders</a></li>
                    <li><a href="user_management.php"><span class="material-symbols-outlined">manage_accounts</span> User Management</a></li>
                    <li class="active"><a href="a_report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
                    <li class="nav-divider"></li>
                    <li><a href="a_profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="logoutadmin.php" class="logout-link"><span class="material-symbols-outlined">logout</span> Log Out</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Main content area -->
    <div class="main-content staff-report-content">
        <div class="header">
            <div class="title">
                <h2>Sales Report</h2>
                <p>Generated: <?php echo date('d M Y, h:i A'); ?></p>
            </div>
            <a href="export_report.php?report_type=<?php echo $report_type; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>" target="_blank" class="export-btn">
                <span class="material-symbols-outlined" style="font-size: 16px;">print</span> Print Report
            </a>
        </div>

        <!-- Total Revenue (All-time - NOT affected by filters) -->
        <div class="total-revenue-box revenue-alltime">
            <span class="material-symbols-outlined">account_balance</span>
            <div class="revenue-info">
                <span class="revenue-label">Total Revenue (All-Time)</span>
                <span class="revenue-amount">RM <?php echo number_format($total_revenue, 2); ?></span>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="" class="filters-container">
            <div class="filters-row">
                <div class="filter-group">
                    <label>Report Type</label>
                    <select name="report_type" class="filter-dropdown">
                        <option value="all" <?php echo ($report_type == 'all') ? 'selected' : ''; ?>>All Orders</option>
                        <option value="completed" <?php echo ($report_type == 'completed') ? 'selected' : ''; ?>>Completed Only</option>
                        <option value="pending" <?php echo ($report_type == 'pending') ? 'selected' : ''; ?>>Pending Only</option>
                        <option value="cancelled" <?php echo ($report_type == 'cancelled') ? 'selected' : ''; ?>>Cancelled Only</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>From Date</label>
                    <input type="date" name="date_from" class="filter-input" value="<?php echo $date_from; ?>">
                </div>

                <div class="filter-group">
                    <label>To Date</label>
                    <input type="date" name="date_to" class="filter-input" value="<?php echo $date_to; ?>">
                </div>

                <div class="filter-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="filter-submit-btn">Apply Filter</button>
                </div>

                <?php if (!empty($date_from) || !empty($date_to) || $report_type != 'all'): ?>
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <a href="report.php" class="filter-reset-btn">Reset</a>
                </div>
                <?php endif; ?>
            </div>
        </form>

        <!-- Report Table -->
        <div class="report-table-container">
            <!-- Total Sales (Affected by filters) -->
            <div class="total-revenue-box revenue-filtered">
                <span class="material-symbols-outlined">payments</span>
                <div class="revenue-info">
                    <span class="revenue-label">
                        <?php 
                        if (!empty($date_from) || !empty($date_to)) {
                            echo "Sales for Period";
                        } else {
                            echo "Total Sales";
                        }
                        ?>
                    </span>
                    <span class="revenue-amount">RM <?php echo number_format($total_sales, 2); ?></span>
                </div>
            </div>

            <div class="table-scroll-wrapper">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Menu Item(s)</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>UPDATED BY</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>#<?php echo $row['order_ID']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['order_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['menu_items']); ?></strong></td>
                                <td><?php echo $row['total_qty']; ?></td>
                                <td>RM <?php echo number_format($row['order_totalAmount'], 2); ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    switch($row['order_status']) {
                                        case 'Completed': $status_class = 'status-completed'; break;
                                        case 'Pending': $status_class = 'status-pending'; break;
                                        case 'Preparing': $status_class = 'status-preparing'; break;
                                        case 'Ready for Pickup': $status_class = 'status-ready'; break;
                                        case 'Cancelled': $status_class = 'status-cancelled'; break;
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>"><?php echo $row['order_status']; ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($row['updated_by']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <h3>No orders found</h3>
                                    <p>Try adjusting your filters or date range.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>

<?php
mysqli_close($conn);
?>