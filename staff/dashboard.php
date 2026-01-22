<!-- 
 Frontend: Insyirah 
 Backend: Amirah
 -->
<?php
session_start();

// Check if staff is logged in
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}

include '../config/db_connect.php';

// Get today's date
$today = date('Y-m-d');

// Today's Sales (Completed orders only)
$sales_query = "SELECT SUM(order_totalAmount) as total_sales FROM orders WHERE DATE(order_date) = '$today' AND order_status = 'Completed'";
$sales_result = mysqli_query($conn, $sales_query);
$today_sales = mysqli_fetch_assoc($sales_result)['total_sales'] ?? 0;

// Total Menu Items
$menu_query = "SELECT COUNT(*) as total_menu FROM menus";
$menu_result = mysqli_query($conn, $menu_query);
$total_menu = mysqli_fetch_assoc($menu_result)['total_menu'] ?? 0;

// Total Customers (Students only)
$customers_query = "SELECT COUNT(*) as total_customers FROM students";
$customers_result = mysqli_query($conn, $customers_query);
$total_customers = mysqli_fetch_assoc($customers_result)['total_customers'] ?? 0;

// New Orders (Pending orders today)
$new_orders_query = "SELECT COUNT(*) as new_orders FROM orders WHERE DATE(order_date) = '$today' AND order_status = 'Pending'";
$new_orders_result = mysqli_query($conn, $new_orders_query);
$new_orders = mysqli_fetch_assoc($new_orders_result)['new_orders'] ?? 0;

// Incomplete Orders (Pending + Preparing + Ready for Pickup)
$incomplete_query = "SELECT COUNT(*) as incomplete FROM orders WHERE order_status IN ('Pending', 'Preparing', 'Ready for Pickup')";
$incomplete_result = mysqli_query($conn, $incomplete_query);
$incomplete_orders = mysqli_fetch_assoc($incomplete_result)['incomplete'] ?? 0;

// Completed Orders (All-time)
$completed_query = "SELECT COUNT(*) as completed_orders FROM orders WHERE order_status = 'Completed'";
$completed_result = mysqli_query($conn, $completed_query);
$completed_orders = mysqli_fetch_assoc($completed_result)['completed_orders'] ?? 0;

// Weekly Sales Data (Last 7 days)
$weekly_sales = [];
$weekly_labels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_name = date('D', strtotime("-$i days"));
    $weekly_labels[] = $day_name;
    
    $day_query = "SELECT SUM(order_totalAmount) as daily_sales FROM orders WHERE DATE(order_date) = '$date' AND order_status = 'Completed'";
    $day_result = mysqli_query($conn, $day_query);
    $daily_sales = mysqli_fetch_assoc($day_result)['daily_sales'] ?? 0;
    $weekly_sales[] = $daily_sales;
}

// Popular Items (Top 5 by quantity sold)
$popular_query = "
    SELECT 
        m.menuName,
        SUM(om.om_quantity) as total_sold
    FROM 
        order_menu om
    JOIN 
        menus m ON om.menuID = m.menuID
    JOIN 
        orders o ON om.order_ID = o.order_ID
    WHERE 
        o.order_status = 'Completed'
    GROUP BY 
        m.menuID
    ORDER BY 
        total_sold DESC
    LIMIT 5
";
$popular_result = mysqli_query($conn, $popular_query);

$popular_labels = [];
$popular_data = [];
while ($row = mysqli_fetch_assoc($popular_result)) {
    $popular_labels[] = $row['menuName'];
    $popular_data[] = $row['total_sold'];
}

// If no data, provide defaults
if (empty($popular_labels)) {
    $popular_labels = ['No data'];
    $popular_data = [0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="sastyle.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <div class="main-content staff-dash-content">
        <div class="header">
            <div class="title">
                <h2>Dashboard</h2>
                <p>Here is what's happening at the canteen today.</p>
            </div>
        </div>

        <div class="staff-dash-grid">
            <div class="dash-stat-card sales">
                <div class="stat-icon">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <div class="stat-details">
                    <h3>Today's Sale</h3>
                    <div class="value">RM <?php echo number_format($today_sales, 2); ?></div>
                </div>
            </div>

            <div class="dash-stat-card items">
                <div class="stat-icon">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <div class="stat-details">
                    <h3>Total Menu</h3>
                    <div class="value"><?php echo $total_menu; ?></div>
                </div>
            </div>

            <div class="dash-stat-card users">
                <div class="stat-icon">
                    <span class="material-symbols-outlined">group</span>
                </div>
                <div class="stat-details">
                    <h3>Total Customers</h3>
                    <div class="value"><?php echo $total_customers; ?></div>
                </div>
            </div>

            <div class="dash-stat-card orders">
                <div class="stat-icon">
                    <span class="material-symbols-outlined">shopping_cart</span>
                </div>
                <div class="stat-details">
                    <h3>New Orders</h3>
                    <div class="value"><?php echo $new_orders; ?></div>
                </div>
            </div>

            <div class="dash-stat-card pending">
                <div class="stat-icon">
                    <span class="material-symbols-outlined">pending_actions</span>
                </div>
                <div class="stat-details">
                    <h3>Incomplete</h3>
                    <div class="value"><?php echo $incomplete_orders; ?></div>
                </div>
            </div>

            <div class="dash-stat-card completed">
                <div class="stat-icon">
                    <span class="material-symbols-outlined">task_alt</span>
                </div>
                <div class="stat-details">
                    <h3>Completed Orders</h3>
                    <div class="value"><?php echo $completed_orders; ?></div>
                </div>
            </div>
        </div>

        <div class="dashboard-secondary-grid">
            <div class="dashboard-table-card">
                <h3>Weekly Sales Performance</h3>
                <p style="font-size: 12px; color: #777; margin-bottom: 15px;">Sales for the last 7 days.</p>
                <canvas id="weeklySalesChart"></canvas>
            </div>

            <div class="dashboard-alert-card">
                <h3>All-Time Popularity</h3>
                <p style="font-size: 12px; color: #777; margin-bottom: 15px;">Distribution of total sales by item.</p>
                <div style="max-width: 280px; margin: 0 auto;">
                    <canvas id="popularPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
    // 1. Sales by Day - Bar Chart
    const ctxWeekly = document.getElementById('weeklySalesChart').getContext('2d');
    new Chart(ctxWeekly, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($weekly_labels); ?>,
            datasets: [{
                label: 'Revenue (RM)',
                data: <?php echo json_encode($weekly_sales); ?>,
                backgroundColor: '#0056b3', 
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // 2. All-Time Popular Items - Doughnut Chart
    const ctxPie = document.getElementById('popularPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($popular_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($popular_data); ?>, 
                backgroundColor: [
                    '#2e7d32',
                    '#007bff', 
                    '#ffa000', 
                    '#d32f2f', 
                    '#9c27b0'  
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom', 
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 12 }
                    }
                }
            },
            cutout: '70%' 
        }
    });
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>
