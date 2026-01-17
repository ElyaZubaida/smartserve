<?php
session_start();

// Check if staff is logged in
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}

// Database connection
include('../config/db_connect.php');

// Check connection
if (!$conn) {
    die('Database connection failed');
}

// Get filter values (same as report.php)
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'all';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Calculate Total Revenue (All-time)
$total_revenue_query = "SELECT SUM(order_totalAmount) as total_revenue FROM orders WHERE order_status = 'Completed'";
$total_revenue_result = mysqli_query($conn, $total_revenue_query);
$total_revenue = mysqli_fetch_assoc($total_revenue_result)['total_revenue'] ?? 0;

// Calculate Total Sales (Filtered)
$sales_query = "SELECT SUM(order_totalAmount) as total_sales FROM orders WHERE order_status = 'Completed'";
if (!empty($date_from)) {
    $sales_query .= " AND DATE(order_date) >= '" . mysqli_real_escape_string($conn, $date_from) . "'";
}
if (!empty($date_to)) {
    $sales_query .= " AND DATE(order_date) <= '" . mysqli_real_escape_string($conn, $date_to) . "'";
}
$sales_result = mysqli_query($conn, $sales_query);
$total_sales = mysqli_fetch_assoc($sales_result)['total_sales'] ?? 0;

// Build query for orders
$query = "
    SELECT 
        o.order_ID,
        o.order_date,
        o.order_totalAmount,
        o.order_status,
        s.student_name,
        st.staffName,
        GROUP_CONCAT(m.menuName SEPARATOR ', ') AS menu_items,
        SUM(om.om_quantity) AS total_qty
    FROM 
        orders o
    JOIN 
        students s ON o.student_ID = s.student_ID
    LEFT JOIN 
        staff st ON o.staffID = st.staffID
    JOIN 
        order_menu om ON o.order_ID = om.order_ID
    JOIN 
        menus m ON om.menuID = m.menuID
    WHERE 1=1
";

if (!empty($date_from)) {
    $query .= " AND DATE(o.order_date) >= '" . mysqli_real_escape_string($conn, $date_from) . "'";
}
if (!empty($date_to)) {
    $query .= " AND DATE(o.order_date) <= '" . mysqli_real_escape_string($conn, $date_to) . "'";
}

if ($report_type == 'completed') {
    $query .= " AND o.order_status = 'Completed'";
} elseif ($report_type == 'cancelled') {
    $query .= " AND o.order_status = 'Cancelled'";
} elseif ($report_type == 'pending') {
    $query .= " AND o.order_status = 'Pending'";
}

$query .= " GROUP BY o.order_ID ORDER BY o.order_ID DESC";
$result = mysqli_query($conn, $query);

// Build filter description
$filter_desc = "All Orders";
if ($report_type == 'completed') $filter_desc = "Completed Orders";
elseif ($report_type == 'pending') $filter_desc = "Pending Orders";
elseif ($report_type == 'cancelled') $filter_desc = "Cancelled Orders";

$date_range = "";
if (!empty($date_from) && !empty($date_to)) {
    $date_range = date('d/m/Y', strtotime($date_from)) . " - " . date('d/m/Y', strtotime($date_to));
} elseif (!empty($date_from)) {
    $date_range = "From " . date('d/m/Y', strtotime($date_from));
} elseif (!empty($date_to)) {
    $date_range = "Until " . date('d/m/Y', strtotime($date_to));
}

// Generate HTML for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SmartServe Sales Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1b5e20;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1b5e20;
            margin-bottom: 5px;
        }
        .logo span {
            color: #666;
        }
        h1 {
            font-size: 20px;
            color: #333;
            margin: 10px 0;
        }
        .report-info {
            font-size: 11px;
            color: #666;
        }
        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }
        .summary-item {
            flex: 1;
            background: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .summary-item.revenue {
            background: #e3f2fd;
            border-color: #90caf9;
        }
        .summary-item.sales {
            background: #e8f5e9;
            border-color: #a5d6a7;
        }
        .summary-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #1b5e20;
        }
        .summary-item.revenue .summary-value {
            color: #1565c0;
        }
        .filters-info {
            background: #f5f5f5;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .filters-info strong {
            color: #1b5e20;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11px;
        }
        th {
            background-color: #1b5e20;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .status {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-completed {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .status-pending {
            background: #ffebee;
            color: #c62828;
        }
        .status-preparing {
            background: #fff3e0;
            color: #ef6c00;
        }
        .status-ready {
            background: #e3f2fd;
            color: #1565c0;
        }
        .status-cancelled {
            background: #eeeeee;
            color: #666;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #888;
            text-align: center;
        }
        .menu-items {
            max-width: 200px;
            word-wrap: break-word;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Smart<span>Serve</span></div>
        <h1>Sales Report</h1>
        <div class="report-info">Generated on: ' . date('d M Y, h:i A') . '</div>
    </div>

    <div class="summary-box">
        <div class="summary-item revenue">
            <div class="summary-label">Total Revenue (All-Time)</div>
            <div class="summary-value">RM ' . number_format($total_revenue, 2) . '</div>
        </div>
        <div class="summary-item sales">
            <div class="summary-label">' . (!empty($date_from) || !empty($date_to) ? 'Sales for Period' : 'Total Sales') . '</div>
            <div class="summary-value">RM ' . number_format($total_sales, 2) . '</div>
        </div>
    </div>

    <div class="filters-info">
        <strong>Filter:</strong> ' . $filter_desc . 
        (!empty($date_range) ? ' &nbsp;|&nbsp; <strong>Date Range:</strong> ' . $date_range : ' (All Dates)') . '
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Order ID</th>
                <th style="width: 10%;">Date</th>
                <th style="width: 14%;">Customer</th>
                <th style="width: 28%;">Menu Item(s)</th>
                <th style="width: 6%;" class="text-center">Qty</th>
                <th style="width: 10%;" class="text-right">Total</th>
                <th style="width: 12%;" class="text-center">Status</th>
                <th style="width: 12%;">Updated By</th>
            </tr>
        </thead>
        <tbody>';

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $status_class = '';
        switch($row['order_status']) {
            case 'Completed': $status_class = 'status-completed'; break;
            case 'Pending': $status_class = 'status-pending'; break;
            case 'Preparing': $status_class = 'status-preparing'; break;
            case 'Ready for Pickup': $status_class = 'status-ready'; break;
            case 'Cancelled': $status_class = 'status-cancelled'; break;
        }
        
        $html .= '<tr>
                    <td>#' . $row['order_ID'] . '</td>
                    <td>' . date('d/m/Y', strtotime($row['order_date'])) . '</td>
                    <td>' . htmlspecialchars($row['student_name']) . '</td>
                    <td class="menu-items">' . htmlspecialchars($row['menu_items']) . '</td>
                    <td class="text-center">' . $row['total_qty'] . '</td>
                    <td class="text-right">RM ' . number_format($row['order_totalAmount'], 2) . '</td>
                    <td class="text-center"><span class="status ' . $status_class . '">' . $row['order_status'] . '</span></td>
                    <td>' . ($row['staffName'] ? htmlspecialchars($row['staffName']) : '-') . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="8" class="text-center" style="padding: 30px; color: #666;">No orders found for the selected filters.</td></tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="footer">
        SmartServe - Student Canteen Food Ordering System<br>
        This report was generated automatically. For any inquiries, please contact the administrator.
    </div>
</body>
</html>';

mysqli_close($conn);

// Output as HTML (browser will print to PDF)
header('Content-Type: text/html; charset=utf-8');
echo $html;
echo '
<script>
    window.onload = function() {
        window.print();
    }
</script>';
?>