<?php
session_start();

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['staff', 'admin'])) {
    header("Location: ../login.php");
    exit;
}

$isAdmin = ($_SESSION['role'] === 'admin');

include('../config/db_connect.php');
if (!$conn) {
    die('Database connection failed');
}

$category = isset($_GET['category']) ? $_GET['category'] : 'orders';
$type = isset($_GET['type']) ? $_GET['type'] : 'sales';
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'all';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

if ($category === 'users' && !$isAdmin) {
    $category = 'orders';
    $type = 'sales';
}

$allowed_categories = ['orders', 'menu', 'users'];
$types_by_category = [
    'orders' => ['sales', 'peak_time', 'status_performance'],
    'menu' => ['popular_item', 'item_performance'],
    'users' => ['student_ranking', 'staff_reporting']
];

if (!in_array($category, $allowed_categories)) {
    $category = 'orders';
}
if (!in_array($type, $types_by_category[$category])) {
    $type = $types_by_category[$category][0];
}

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
    return "All Dates";
}

function buildQueryString($base, $overrides = []) {
    return http_build_query(array_merge($base, $overrides));
}

$title = "Report";
$subTitle = "";
$filtersTextParts = [];
$summaryBoxes = [];
$tableHeaders = [];
$tableRows = [];
$emptyMessage = "No records found for the selected filters.";

$chartEnabled = true;
$chartType = 'bar';
$chartLabels = [];
$chartValues = [];
$chartDatasetLabel = '';

$date_range = makeDateRangeText($date_from, $date_to);
$filtersTextParts[] = "<strong>Date Range:</strong> " . htmlspecialchars($date_range);

if ($category === 'orders') {
    if ($type === 'sales') {
        $title = "Sales Report";
        $subTitle = "Orders";

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
            'value' => 'RM ' . number_format((float)$total_revenue, 2)
        ];
        $summaryBoxes[] = [
            'label' => (!empty($date_from) || !empty($date_to)) ? 'Sales for Period (Completed)' : 'Total Sales (Completed)',
            'value' => 'RM ' . number_format((float)$total_sales, 2)
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
                    $row['student_name'],
                    $row['menu_items'],
                    (int)$row['total_qty'],
                    'RM ' . number_format((float)$row['order_totalAmount'], 2),
                    $row['order_status'],
                    $row['updated_by']
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
        $subTitle = "Orders";
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
        $subTitle = "Orders";
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
                $tableRows[] = [$row['order_status'], (int)$row['total']];
                $chartLabels[] = $row['order_status'];
                $chartValues[] = (int)$row['total'];
            }
        } else {
            $chartEnabled = false;
        }
    }
} elseif ($category === 'menu') {
    if ($type === 'popular_item') {
        $title = "Popular Menu Items Report";
        $subTitle = "Menu";
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
                $tableRows[] = [$row['menuName'], (int)$row['total_sold']];
                $chartLabels[] = $row['menuName'];
                $chartValues[] = (int)$row['total_sold'];
            }
        } else {
            $chartEnabled = false;
        }
    } elseif ($type === 'item_performance') {
        $title = "Menu Item Performance Report";
        $subTitle = "Menu";
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
                    $row['menuName'],
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
    if ($type === 'student_ranking') {
        $title = "Student Ranking Report";
        $subTitle = "Users";
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
                    $row['student_name'],
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
        $subTitle = "Users";
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
                $tableRows[] = [$row['updated_by'], (int)$row['total_updates']];
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

if ($chartEnabled && $chartType === 'bar' && count($chartLabels) > 5) {
    $chartData = [];
    $count = min(count($chartLabels), count($chartValues));
    for ($i = 0; $i < $count; $i++) {
        $chartData[] = ['label' => $chartLabels[$i], 'value' => (float)$chartValues[$i]];
    }
    usort($chartData, function ($a, $b) {
        return $b['value'] <=> $a['value'];
    });
    $chartData = array_slice($chartData, 0, 5);
    $chartLabels = array_column($chartData, 'label');
    $chartValues = array_column($chartData, 'value');
}

$filtersHtml = implode(" &nbsp;|&nbsp; ", $filtersTextParts);

$baseParams = [
    'category' => $category,
    'type' => $type,
    'report_type' => $report_type,
    'date_from' => $date_from,
    'date_to' => $date_to
];

$categoryLabels = [
    'orders' => 'Orders',
    'menu' => 'Menu',
    'users' => 'Users'
];

$typeLabels = [
    'sales' => 'Sales Report',
    'peak_time' => 'Peak Order Times',
    'status_performance' => 'Status Performance',
    'popular_item' => 'Popular Items',
    'item_performance' => 'Item Performance',
    'student_ranking' => 'Student Rankings',
    'staff_reporting' => 'Staff Reporting'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="sastyle.css">
</head>
<body>
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
                    <li><a href="order_management.php"><span class="material-symbols-outlined">order_approve</span> Orders</a></li>
                    <li class="active"><a href="report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
                    <li class="nav-divider"></li>
                    <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="../logout.php" class="logout-link"><span class="material-symbols-outlined">logout</span> Log Out</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="main-content staff-report-content">
        <div class="header">
            <div class="title">
                <h2><?php echo htmlspecialchars($title); ?></h2>
                <?php if ($subTitle !== ""): ?>
                    <p><?php echo htmlspecialchars($subTitle); ?> • Generated: <?php echo date('d M Y, h:i A'); ?></p>
                <?php else: ?>
                    <p>Generated: <?php echo date('d M Y, h:i A'); ?></p>
                <?php endif; ?>
            </div>
            <a href="export_report.php?<?php echo buildQueryString($baseParams); ?>" target="_blank" class="export-btn">
                <span class="material-symbols-outlined" style="font-size: 16px;">print</span> Print Report
            </a>
        </div>

        <div class="report-tabs">
            <a class="report-tab <?php echo ($category === 'orders') ? 'active' : ''; ?>"
               href="?<?php echo buildQueryString($baseParams, ['category' => 'orders', 'type' => $types_by_category['orders'][0]]); ?>">
               Orders
            </a>
            <a class="report-tab <?php echo ($category === 'menu') ? 'active' : ''; ?>"
               href="?<?php echo buildQueryString($baseParams, ['category' => 'menu', 'type' => $types_by_category['menu'][0]]); ?>">
               Menu
            </a>
            <?php if ($isAdmin): ?>
            <a class="report-tab <?php echo ($category === 'users') ? 'active' : ''; ?>"
               href="?<?php echo buildQueryString($baseParams, ['category' => 'users', 'type' => $types_by_category['users'][0]]); ?>">
               Users
            </a>
            <?php endif; ?>
        </div>

        <div class="report-subtabs">
            <?php foreach ($types_by_category[$category] as $t): ?>
                <?php
                $label = isset($typeLabels[$t]) ? $typeLabels[$t] : ucfirst(str_replace('_', ' ', $t));
                ?>
                <a class="report-subtab <?php echo ($type === $t) ? 'active' : ''; ?>"
                   href="?<?php echo buildQueryString($baseParams, ['type' => $t]); ?>">
                   <?php echo htmlspecialchars($label); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($summaryBoxes)): ?>
        <div class="report-summary-bar">
            <?php foreach ($summaryBoxes as $box): ?>
                <div class="summary-item">
                    <span class="label"><?php echo htmlspecialchars($box['label']); ?></span>
                    <span class="count"><?php echo htmlspecialchars($box['value']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="GET" action="" class="filters-container">
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">

            <div class="filters-row">
                <?php if ($category === 'orders' && $type === 'sales'): ?>
                <div class="filter-group">
                    <label>Order Status</label>
                    <select name="report_type" class="filter-dropdown">
                        <option value="all" <?php echo ($report_type == 'all') ? 'selected' : ''; ?>>All Orders</option>
                        <option value="completed" <?php echo ($report_type == 'completed') ? 'selected' : ''; ?>>Completed Only</option>
                        <option value="pending" <?php echo ($report_type == 'pending') ? 'selected' : ''; ?>>Pending Only</option>
                        <option value="cancelled" <?php echo ($report_type == 'cancelled') ? 'selected' : ''; ?>>Cancelled Only</option>
                    </select>
                </div>
                <?php endif; ?>

                <div class="filter-group">
                    <label>From Date</label>
                    <input type="date" name="date_from" class="filter-input" value="<?php echo htmlspecialchars($date_from); ?>">
                </div>

                <div class="filter-group">
                    <label>To Date</label>
                    <input type="date" name="date_to" class="filter-input" value="<?php echo htmlspecialchars($date_to); ?>">
                </div>

                <div class="filter-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="filter-submit-btn">Apply Filter</button>
                </div>

                <?php if (!empty($date_from) || !empty($date_to) || ($category === 'orders' && $type === 'sales' && $report_type !== 'all')): ?>
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <a href="report.php?<?php echo buildQueryString(['category' => $category, 'type' => $type]); ?>" class="filter-reset-btn">Reset</a>
                </div>
                <?php endif; ?>
            </div>
        </form>

        <div class="report-meta">
            <?php echo $filtersHtml; ?>
        </div>

        <?php if ($chartEnabled): ?>
        <div class="chart-wrap">
            <div class="chart-card">
                <canvas id="reportChart" height="120"></canvas>
            </div>
        </div>
        <?php endif; ?>

        <div class="report-table-container">
            <div class="table-scroll-wrapper">
                <table class="report-table">
                    <thead>
                        <tr>
                            <?php foreach ($tableHeaders as $header): ?>
                                <th><?php echo htmlspecialchars($header); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tableRows)): ?>
                            <?php foreach ($tableRows as $row): ?>
                                <tr>
                                    <?php foreach ($row as $cell): ?>
                                        <td><?php echo htmlspecialchars((string)$cell); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?php echo max(1, count($tableHeaders)); ?>" class="empty-state">
                                    <h3>No records found</h3>
                                    <p>Try adjusting your filters or date range.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($chartEnabled): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = <?php echo json_encode($chartLabels); ?>;
        const values = <?php echo json_encode($chartValues); ?>;

        function genColors(n) {
            const arr = [];
            for (let i = 0; i < n; i++) {
                const hue = Math.floor((360 / n) * i);
                arr.push(`hsl(${hue}, 70%, 55%)`);
            }
            return arr;
        }

        const colors = genColors(values.length);
        const ctx = document.getElementById("reportChart");
        if (ctx) {
            new Chart(ctx, {
                type: <?php echo json_encode($chartType); ?>,
                data: {
                    labels: labels,
                    datasets: [{
                        label: <?php echo json_encode($chartDatasetLabel); ?>,
                        data: values,
                        backgroundColor: colors,
                        borderColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: <?php echo ($chartType === 'bar') ? 'false' : 'true'; ?> }
                    },
                    scales: <?php echo ($chartType === 'bar') ? "{ y: { beginAtZero: true } }" : "undefined"; ?>
                }
            });
        }
    </script>
    <?php endif; ?>
</body>
</html>

<?php
mysqli_close($conn);
?>
