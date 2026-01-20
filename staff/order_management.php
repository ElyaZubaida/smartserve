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

// Fetch orders from database
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
    ORDER BY 
        o.order_ID DESC
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