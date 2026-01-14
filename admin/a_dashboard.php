<!-- 
 Frontend: Insyirah 
 Backend: Amirah 
 -->
<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: loginadmin.php');
    exit();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="../staff/sastyle.css">
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
                <div class="value">RM 1,000.00</div>
            </div>
        </div>

        <div class="dash-stat-card items">
            <div class="stat-icon">
                <span class="material-symbols-outlined">inventory_2</span>
            </div>
            <div class="stat-details">
                <h3>Total Menu</h3>
                <div class="value">30</div>
            </div>
        </div>

        <div class="dash-stat-card orders">
            <div class="stat-icon">
                <span class="material-symbols-outlined">shopping_cart</span>
            </div>
            <div class="stat-details">
                <h3>New Orders</h3>
                <div class="value">3</div>
            </div>
        </div>

        <div class="dash-stat-card pending">
            <div class="stat-icon">
                <span class="material-symbols-outlined">pending_actions</span>
            </div>
            <div class="stat-details">
                <h3>Incomplete</h3>
                <div class="value">2</div>
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
        <p style="font-size: 12px; color: #777; margin-bottom: 15px;">Distribution of total sales by Menu.</p>
        <div style="max-width: 280px; margin: 0 auto;"> <canvas id="popularPieChart"></canvas>
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
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Revenue (RM)',
            data: [850, 920, 1100, 780, 1050, 400, 300], // Mock Data
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

// 2. Popular Menu - Doughnut Chart
const ctxPie = document.getElementById('popularPieChart').getContext('2d');
new Chart(ctxPie, {
    type: 'doughnut',
    data: {
        labels: ['Nasi Lemak', 'Mee Goreng', 'Teh Tarik', 'Chicken Rice', 'Iced Milo'],
        datasets: [{
            data: [450, 320, 280, 210, 150], 
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
