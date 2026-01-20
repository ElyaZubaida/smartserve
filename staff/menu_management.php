<!-- 
 Frontend: Elya 
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

// Fetch menu items from database
$query = "
    SELECT 
        `menuID`, 
        `menuName`, 
        `menuImage`, 
        `menuCategory`, 
        `menuDescription`, 
        `menuPrice`, 
        `menuAvailability`
    FROM `menus`
    WHERE `is_deleted` = 0
    ORDER BY `created_at` DESC
";

$result = mysqli_query($conn, $query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Menu Management</title>
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
                    <li class="active"><a href="menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li><a href="order_management.php"><span class="material-symbols-outlined">order_approve</span> Orders</a></li>
                    <li><a href="report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
                    <li class="nav-divider"></li>
                    <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="../logout.php" class="logout-link"><span class="material-symbols-outlined">logout</span> Log Out</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content staff-menu-content">
    <main>
        <div class="header">
            <div class="title">
                <h2>Menu Management</h2>
                <p>Manage menu items and availability</p>
            </div>
            <a href="addmenu.php" class="staff-menu-add-btn">
                <span class="material-symbols-outlined">add_box</span>
                Add Menu
            </a>
        </div>

        <div class="staff-menu-grid">
            <?php 
            // Check if there are any menu items
            if (mysqli_num_rows($result) > 0) {
                // Loop through menu items
                while ($menu_item = mysqli_fetch_assoc($result)) {
            ?>
            <div class="staff-menu-card">
                <div class="staff-menu-img">
                    <img src="<?php 
                        echo !empty($menu_item['menuImage']) 
                            ? '../img/' . htmlspecialchars($menu_item['menuImage']) 
                            : '../img/placeholder.jpg'; 
                    ?>" alt="<?php echo htmlspecialchars($menu_item['menuName']); ?>">
                </div>
                <div class="staff-menu-details">
                    <span class="staff-menu-category">
                        <?php echo htmlspecialchars($menu_item['menuCategory'] ?? 'Uncategorized'); ?>
                    </span>
                    <h3><?php echo htmlspecialchars($menu_item['menuName']); ?></h3>
                    <p><?php echo htmlspecialchars($menu_item['menuDescription'] ?? 'No description'); ?></p>
                    <div class="staff-menu-footer">
                        <span class="staff-menu-price">RM <?php echo number_format($menu_item['menuPrice'], 2); ?></span>
                        <div class="staff-menu-actions">
                            <a href="updatemenu.php?id=<?php echo $menu_item['menuID']; ?>" class="staff-menu-edit">
                                <span class="material-symbols-outlined">edit</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
                // Display message if no menu items found
                echo '<div class="empty-state">
                        <h3>No Menu Items Found</h3>
                        <p>Click "Add Menu" to create your first menu item.</p>
                      </div>';
            }
            ?>
        </div>
    </main>
</div>

<?php
// Close database connection
mysqli_close($conn);
?>
</body>
</html>