<?php
session_start();

/**
 * export_report.php (PRINT / EXPORT PAGE)
 *
 * Supports 3 categories + multiple report types:
 * - category=orders
 *    - type=sales
 *    - type=peak_time
 *    - type=status_performance
 * - category=menu
 *    - type=popular_item
 *    - type=item_performance
 * - category=users   (ADMIN ONLY)
 *    - type=student_ranking
 *    - type=staff_reporting
 *
 * Filters (optional):
 * - date_from=YYYY-MM-DD
 * - date_to=YYYY-MM-DD
 *
 * Legacy support:
 * - report_type=all|completed|pending|cancelled (only applied to Orders->Sales table)
 */

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['staff', 'admin'])) {
    header("Location: ../login.php");
    exit;
}

$isAdmin = ($_SESSION['role'] === 'admin');

include('../config/db_connect.php');
if (!$conn) die('Database connection failed');

$category = isset($_GET['category']) ? $_GET['category'] : 'orders';
$type = isset($_GET['type']) ? $_GET['type'] : 'sales';

$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'all';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

if ($category === 'users' && !$isAdmin) {
    header("Location: export_report.php?category=orders&type=sales");
    exit;
}

$allowed_categories = ['orders', 'menu', 'users'];
if (!in_array($category, $allowed_categories)) $category = 'orders';

function buildDateFilters($conn, $alias, $date_from, $date_to) {
    $sql = "";
    if (!empty($date_from)) {
        $df = mysqli_real_escape_string($conn, $date_from);
        $sql .= " AND DATE($alias.order_date) >= '$df' ";
    }
    if (!empty($date_to)) {
        $dt = mysqli_real_escape_string($conn, $date_to);
        $sql .= " AND DATE($alias.order_date) <= '$dt' ";
    }
    return $sql;
}

function makeDateRangeText($date_from, $date_to) {
    if (!empty($date_from) && !empty($date_to)) {
        return date('d/m/Y', strtotime($date_from)) . " - " . date('d/m/Y', strtotime($date_to));
    } elseif (!empty($date_from)) {
        return "From " . date('d/m/Y', strtotime($date_from));
    } elseif (!empty($date_to)) {
        return "Until " . date('d/m/Y', strtotime($date_to));
    }
    return "";
}

$title = "Report";
$subTitle = "";
$filtersTextParts = [];

$date_range = makeDateRangeText($date_from, $date_to);
if (!empty($date_range)) $filtersTextParts[] = "<strong>Date Range:</strong> " . htmlspecialchars($date_range);
else $filtersTextParts[] = "<strong>Date Range:</strong> All Dates";

$chartEnabled = true;
$chartType = 'bar';
$chartLabels = [];
$chartValues = [];
$chartDatasetLabel = '';

$tableHeaders = [];
$tableRows = [];
$emptyMessage = "No records found for the selected filters.";

$summaryBoxes = [];

if ($category === 'orders') {
    if (!in_array($type, ['sales', 'peak_time', 'status_performance'])) $type = 'sales';

    if ($type === 'sales') {
        $title = "Sales Report";
        $subTitle = "Orders -> Sales";

        $filter_desc = "All Orders";
        if ($report_type === 'completed') $filter_desc = "Completed Orders";
        elseif ($report_type === 'pending') $filter_desc = "Pending Orders";
        elseif ($report_type === 'cancelled') $filter_desc = "Cancelled Orders";

        $filtersTextParts[] = "<strong>Order Filter:</strong> " . htmlspecialchars($filter_desc);

        $total_revenue_query = "SELECT SUM(order_totalAmount) as total_revenue FROM orders WHERE order_status='Completed'";
        $total_revenue_result = mysqli_query($conn, $total_revenue_query);
        $total_revenue = mysqli_fetch_assoc($total_revenue_result)['total_revenue'] ?? 0;

        $sales_query = "SELECT SUM(order_totalAmount) as total_sales FROM orders WHERE order_status='Completed'";
        if (!empty($date_from)) {
            $sales_query .= " AND DATE(order_date) >= '" . mysqli_real_escape_string($conn, $date_from) . "'";
        }
        if (!empty($date_to)) {
            $sales_query .= " AND DATE(order_date) <= '" . mysqli_real_escape_string($conn, $date_to) . "'";
        }
        $sales_result = mysqli_query($conn, $sales_query);
        $total_sales = mysqli_fetch_assoc($sales_result)['total_sales'] ?? 0;

        $summaryBoxes[] = [
            'label' => 'Total Revenue (All-Time)',
            'value' => 'RM ' . number_format((float)$total_revenue, 2),
            'class' => 'revenue'
        ];
        $summaryBoxes[] = [
            'label' => (!empty($date_from) || !empty($date_to)) ? 'Sales for Period (Completed)' : 'Total Sales (Completed)',
            'value' => 'RM ' . number_format((float)$total_sales, 2),
            'class' => 'sales'
        ];

        $query = "
            SELECT 
                o.order_ID,
                o.order_date,
                o.order_totalAmount,
                o.order_status,
                s.student_name,
                CASE 
                    WHEN o.staffID IS NOT NULL THEN CONCAT(st.staffName, ' (Staff)')
                    WHEN o.admin_ID IS NOT NULL THEN CONCAT(a.admin_name, ' (Admin)')
                    ELSE '-'
                END AS updated_by,
                GROUP_CONCAT(m.menuName SEPARATOR ', ') AS menu_items,
                SUM(om.om_quantity) AS total_qty
            FROM orders o
            JOIN students s ON o.student_ID = s.student_ID
            LEFT JOIN staff st ON o.staffID = st.staffID
            LEFT JOIN admins a ON o.admin_ID = a.admin_ID
            JOIN order_menu om ON o.order_ID = om.order_ID
            JOIN menus m ON om.menuID = m.menuID
            WHERE 1=1
        ";

        if (!empty($date_from)) {
            $query .= " AND DATE(o.order_date) >= '" . mysqli_real_escape_string($conn, $date_from) . "'";
        }
        if (!empty($date_to)) {
            $query .= " AND DATE(o.order_date) <= '" . mysqli_real_escape_string($conn, $date_to) . "'";
        }

        if ($report_type === 'completed') $query .= " AND o.order_status='Completed'";
        elseif ($report_type === 'pending') $query .= " AND o.order_status='Pending'";
        elseif ($report_type === 'cancelled') $query .= " AND o.order_status='Cancelled'";

        $query .= " GROUP BY o.order_ID ORDER BY o.order_ID DESC";

        $result = mysqli_query($conn, $query);

        $tableHeaders = ['Order ID', 'Date', 'Customer', 'Menu Item(s)', 'Qty', 'Total', 'Status', 'Updated By'];

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $tableRows[] = [
                    '#' . $row['order_ID'],
                    date('d/m/Y', strtotime($row['order_date'])),
                    htmlspecialchars($row['student_name']),
                    htmlspecialchars($row['menu_items']),
                    (int)$row['total_qty'],
                    'RM ' . number_format((float)$row['order_totalAmount'], 2),
                    $row['order_status'],
                    htmlspecialchars($row['updated_by'])
                ];
            }
        }

        $statusChartQuery = "SELECT order_status, COUNT(*) AS total
                             FROM orders
                             WHERE 1=1";
        if (!empty($date_from)) {
            $statusChartQuery .= " AND DATE(order_date) >= '" . mysqli_real_escape_string($conn, $date_from) . "'";
        }
        if (!empty($date_to)) {
            $statusChartQuery .= " AND DATE(order_date) <= '" . mysqli_real_escape_string($conn, $date_to) . "'";
        }
        if ($report_type === 'completed') $statusChartQuery .= " AND order_status='Completed'";
        elseif ($report_type === 'pending') $statusChartQuery .= " AND order_status='Pending'";
        elseif ($report_type === 'cancelled') $statusChartQuery .= " AND order_status='Cancelled'";

        $statusChartQuery .= " GROUP BY order_status";

        $sc = mysqli_query($conn, $statusChartQuery);
        $chartType = 'pie';
        $chartDatasetLabel = 'Orders by Status';
        if ($sc && mysqli_num_rows($sc) > 0) {
            while ($r = mysqli_fetch_assoc($sc)) {
                $chartLabels[] = $r['order_status'];
                $chartValues[] = (int)$r['total'];
            }
        } else {
            $chartEnabled = false;
        }
    } elseif ($type === 'peak_time') {
        $title = "Peak Order Time Report";
        $subTitle = "Orders -> Peak Order Time";
        $filtersTextParts[] = "<strong>Metric:</strong> Total Orders by Hour";

        $query = "SELECT HOUR(o.order_date) AS hour, COUNT(*) AS total_orders
                  FROM orders o
                  WHERE 1=1";
        $query .= buildDateFilters($conn, 'o', $date_from, $date_to);
        $query .= " GROUP BY HOUR(o.order_date) ORDER BY hour ASC";

        $result = mysqli_query($conn, $query);

        $tableHeaders = ['Hour', 'Total Orders'];
        $chartType = 'bar';
        $chartDatasetLabel = 'Total Orders';

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $hourLabel = str_pad($row['hour'], 2, '0', STR_PAD_LEFT) . ":00";
                $tableRows[] = [$hourLabel, (int)$row['total_orders']];
                $chartLabels[] = $hourLabel;
                $chartValues[] = (int)$row['total_orders'];
            }
        } else {
            $chartEnabled = false;
        }
    } elseif ($type === 'status_performance') {
        $title = "Order Status Performance Report";
        $subTitle = "Orders -> Status Performance";
        $filtersTextParts[] = "<strong>Metric:</strong> Status Distribution";

        $query = "SELECT o.order_status, COUNT(*) AS total
                  FROM orders o
                  WHERE 1=1";
        $query .= buildDateFilters($conn, 'o', $date_from, $date_to);
        $query .= " GROUP BY o.order_status ORDER BY total DESC";

        $result = mysqli_query($conn, $query);

        $tableHeaders = ['Status', 'Total Orders'];
        $chartType = 'doughnut';
        $chartDatasetLabel = 'Total Orders';

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $tableRows[] = [htmlspecialchars($row['order_status']), (int)$row['total']];
                $chartLabels[] = $row['order_status'];
                $chartValues[] = (int)$row['total'];
            }
        } else {
            $chartEnabled = false;
        }
    }
} elseif ($category === 'menu') {
    if (!in_array($type, ['popular_item', 'item_performance'])) $type = 'popular_item';

    if ($type === 'popular_item') {
        $title = "Popular Menu Items Report";
        $subTitle = "Menu -> Popular Items";
        $filtersTextParts[] = "<strong>Metric:</strong> Top 10 Items by Quantity Sold";

        $query = "SELECT m.menuName, SUM(om.om_quantity) AS total_sold
                  FROM order_menu om
                  JOIN menus m ON m.menuID = om.menuID
                  JOIN orders o ON o.order_ID = om.order_ID
                  WHERE o.order_status='Completed'";
        $query .= buildDateFilters($conn, 'o', $date_from, $date_to);
        $query .= " GROUP BY m.menuID
                    ORDER BY total_sold DESC
                    LIMIT 10";

        $result = mysqli_query($conn, $query);

        $tableHeaders = ['Menu Item', 'Total Sold'];
        $chartType = 'bar';
        $chartDatasetLabel = 'Total Sold';

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $tableRows[] = [htmlspecialchars($row['menuName']), (int)$row['total_sold']];
                $chartLabels[] = $row['menuName'];
                $chartValues[] = (int)$row['total_sold'];
            }
        } else {
            $chartEnabled = false;
        }
    } elseif ($type === 'item_performance') {
        $title = "Menu Item Performance Report";
        $subTitle = "Menu -> Item Performance";
        $filtersTextParts[] = "<strong>Metric:</strong> Total Sold + Revenue (Completed Orders)";

        $query = "SELECT m.menuName,
                         SUM(om.om_quantity) AS total_sold,
                         SUM(om.om_quantity * m.menuPrice) AS total_revenue
                  FROM order_menu om
                  JOIN menus m ON m.menuID = om.menuID
                  JOIN orders o ON o.order_ID = om.order_ID
                  WHERE o.order_status='Completed'";

        $query .= buildDateFilters($conn, 'o', $date_from, $date_to);

        $query .= " GROUP BY m.menuID
                    ORDER BY total_revenue DESC
                    LIMIT 20";

        $result = mysqli_query($conn, $query);

        $tableHeaders = ['Menu Item', 'Total Sold', 'Total Revenue'];
        $chartType = 'bar';
        $chartDatasetLabel = 'Total Revenue (RM)';

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $tableRows[] = [
                    htmlspecialchars($row['menuName']),
                    (int)$row['total_sold'],
                    'RM ' . number_format((float)$row['total_revenue'], 2)
                ];
                $chartLabels[] = $row['menuName'];
                $chartValues[] = (float)$row['total_revenue'];
            }
        } else {
            $chartEnabled = false;
        }
    }
} elseif ($category === 'users') {
    if (!in_array($type, ['student_ranking', 'staff_reporting'])) $type = 'student_ranking';

    if ($type === 'student_ranking') {
        $title = "Student Ranking Report";
        $subTitle = "Users -> Students by Orders";
        $filtersTextParts[] = "<strong>Metric:</strong> Top 10 Students (Completed Orders)";

        $query = "SELECT s.student_name,
                         COUNT(o.order_ID) AS total_orders,
                         SUM(o.order_totalAmount) AS total_spent
                  FROM orders o
                  JOIN students s ON s.student_ID = o.student_ID
                  WHERE o.order_status='Completed'";
        $query .= buildDateFilters($conn, 'o', $date_from, $date_to);
        $query .= " GROUP BY s.student_ID
                    ORDER BY total_orders DESC
                    LIMIT 10";

        $result = mysqli_query($conn, $query);

        $tableHeaders = ['Student', 'Total Orders', 'Total Spent'];
        $chartType = 'bar';
        $chartDatasetLabel = 'Total Orders';

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $tableRows[] = [
                    htmlspecialchars($row['student_name']),
                    (int)$row['total_orders'],
                    'RM ' . number_format((float)$row['total_spent'], 2)
                ];
                $chartLabels[] = $row['student_name'];
                $chartValues[] = (int)$row['total_orders'];
            }
        } else {
            $chartEnabled = false;
        }
    } elseif ($type === 'staff_reporting') {
        $title = "Staff/Admin Activity Report";
        $subTitle = "Users -> Staff Reporting";
        $filtersTextParts[] = "<strong>Metric:</strong> Who Updated Orders Most";

        $query = "SELECT 
                    COALESCE(st.staffName, a.admin_name, 'Unknown') AS updated_by,
                    COUNT(*) AS total_updates
                  FROM orders o
                  LEFT JOIN staff st ON o.staffID = st.staffID
                  LEFT JOIN admins a ON o.admin_ID = a.admin_ID
                  WHERE (o.staffID IS NOT NULL OR o.admin_ID IS NOT NULL)";
        $query .= buildDateFilters($conn, 'o', $date_from, $date_to);
        $query .= " GROUP BY updated_by
                    ORDER BY total_updates DESC";

        $result = mysqli_query($conn, $query);

        $tableHeaders = ['Updated By', 'Total Updates'];
        $chartType = 'bar';
        $chartDatasetLabel = 'Total Updates';

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $tableRows[] = [htmlspecialchars($row['updated_by']), (int)$row['total_updates']];
                $chartLabels[] = $row['updated_by'];
                $chartValues[] = (int)$row['total_updates'];
            }
        } else {
            $chartEnabled = false;
        }
    }
}

if ($chartEnabled && (count($chartLabels) === 0 || count($chartValues) === 0)) {
    $chartEnabled = false;
}

mysqli_close($conn);

$filtersHtml = implode(" &nbsp;|&nbsp; ", $filtersTextParts);

$summaryHtml = '';
if (!empty($summaryBoxes)) {
    $summaryHtml .= '<div class="summary-box">';
    foreach ($summaryBoxes as $box) {
        $cls = isset($box['class']) ? $box['class'] : 'neutral';
        $summaryHtml .= '
            <div class="summary-item ' . htmlspecialchars($cls) . '">
                <div class="summary-label">' . $box['label'] . '</div>
                <div class="summary-value">' . $box['value'] . '</div>
            </div>
        ';
    }
    $summaryHtml .= '</div>';
}

$tableHtml = '';
$tableHtml .= '<table><thead><tr>';
foreach ($tableHeaders as $h) {
    $tableHtml .= '<th>' . htmlspecialchars($h) . '</th>';
}
$tableHtml .= '</tr></thead><tbody>';

if (!empty($tableRows)) {
    foreach ($tableRows as $r) {
        $tableHtml .= '<tr>';
        foreach ($r as $cell) {
            $tableHtml .= '<td>' . $cell . '</td>';
        }
        $tableHtml .= '</tr>';
    }
} else {
    $colspan = max(1, count($tableHeaders));
    $tableHtml .= '<tr><td colspan="' . $colspan . '" class="empty-state">' . htmlspecialchars($emptyMessage) . '</td></tr>';
}

$tableHtml .= '</tbody></table>';

$chartHtml = '';
$chartJs = '';
if ($chartEnabled) {
        $chartHtml = '
        <div class="chart-wrap">
            <h3 class="section-title">Chart</h3>
            <div class="chart-card">
                <canvas id="reportChart" height="60"></canvas>
            </div>
        </div>
    ';

    $chartJs = '
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = ' . json_encode($chartLabels) . ';
        const values = ' . json_encode($chartValues) . ';

        function genColors(n){
            const arr = [];
            for (let i=0;i<n;i++){
                const hue = Math.floor((360/n)*i);
                arr.push(`hsl(${hue}, 70%, 55%)`);
            }
            return arr;
        }

        const colors = genColors(values.length);

        new Chart(document.getElementById("reportChart"), {
            type: ' . json_encode($chartType) . ',
            data: {
                labels: labels,
                datasets: [{
                    label: ' . json_encode($chartDatasetLabel) . ',
                    data: values,
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: ' . (($chartType === 'bar') ? 'false' : 'true') . ' }
                },
                scales: ' . (($chartType === 'bar') ? '{
                    y: { beginAtZero: true }
                }' : 'undefined') . '
            }
        });

        window.onload = function() {
            setTimeout(function(){ window.print(); }, 600);
        };
    </script>';
} else {
    $chartJs = '
    <script>
        window.onload = function() {
            window.print();
        };
    </script>';
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SmartServe - <?php echo htmlspecialchars($title); ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; color:#222; }

        .header {
            text-align:center;
            margin-bottom: 18px;
            border-bottom: 2px solid #1b5e20;
            padding-bottom: 14px;
        }
        .logo { font-size: 24px; font-weight: 800; color: #1b5e20; margin-bottom: 4px; }
        .logo span { color:#666; }
        h1 { font-size: 20px; margin: 8px 0 4px; }
        .subtitle { font-size: 12px; color:#666; margin-bottom: 6px; }
        .report-info { font-size: 11px; color:#666; }

        .summary-box { display:flex; gap: 14px; margin: 18px 0 14px; }
        .summary-item { flex:1; background:#f8f8f8; border:1px solid #ddd; border-radius:10px; padding:14px; text-align:center; }
        .summary-item.revenue { background:#e3f2fd; border-color:#90caf9; }
        .summary-item.sales { background:#e8f5e9; border-color:#a5d6a7; }
        .summary-label { font-size: 10px; color:#666; text-transform: uppercase; margin-bottom: 6px; }
        .summary-value { font-size: 18px; font-weight: 800; color:#1b5e20; }
        .summary-item.revenue .summary-value { color:#1565c0; }

        .filters-info {
            background:#f5f5f5;
            padding: 10px 14px;
            border-radius: 8px;
            margin: 10px 0 16px;
            font-size: 11px;
        }
        .filters-info strong { color:#1b5e20; }

        .section-title { margin: 6px 0 10px; font-size: 13px; color:#1b5e20; }

        .chart-wrap { margin: 0 0 12px; }
        .chart-card { border:1px solid #e5e5e5; border-radius: 10px; padding: 6px; background:#fff; height: 160px; }
        .chart-card canvas { height: 100% !important; width: 100% !important; }

        table { width:100%; border-collapse: collapse; margin-top: 10px; font-size: 11px; }
        th {
            background:#1b5e20;
            color:#fff;
            padding: 10px 8px;
            text-align:left;
            font-weight:700;
            font-size:10px;
            text-transform: uppercase;
        }
        td { padding: 10px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        tr:nth-child(even) { background:#fafafa; }

        .empty-state { text-align:center; padding: 26px; color:#666; }

        .footer {
            margin-top: 22px;
            padding-top: 12px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #888;
            text-align:center;
            line-height: 1.4;
        }

        @media print {
            body { margin: 0; padding: 14px; }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="logo">Smart<span>Serve</span></div>
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <?php if ($subTitle !== ""): ?>
        <div class="subtitle"><?php echo htmlspecialchars($subTitle); ?></div>
    <?php endif; ?>
    <div class="report-info">Generated on: <?php echo date('d M Y, h:i A'); ?></div>
</div>

<?php echo $summaryHtml; ?>

<div class="filters-info">
    <?php echo $filtersHtml; ?>
</div>

<?php echo $chartHtml; ?>

<h3 class="section-title">Table</h3>
<?php echo $tableHtml; ?>

<div class="footer">
    SmartServe - Student Canteen Food Ordering System<br>
    This report was generated automatically. For any inquiries, please contact the administrator.
</div>

<?php echo $chartJs; ?>
</body>
</html>
