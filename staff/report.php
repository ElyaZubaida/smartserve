<!-- 
 Frontend: Qai 
 Backend: Amirah
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Report</title>
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
    <div class="main-content staff-report-content">
    <?php
    // Mock Data Array for Preview
    $mock_menu = [
        ['id' => 1, 'name' => 'Nasi Lemak Special', 'price' => 5.00, 'description' => 'Fragrant coconut rice with sambal, anchovies, and egg.', 'image' => 'nasilemak.jpg'],
        ['id' => 2, 'name' => 'Mee Goreng Mamak', 'price' => 4.50, 'description' => 'Spicy stir-fried noodles with tofu and vegetables.', 'image' => 'meegoreng.jpg'],
        ['id' => 3, 'name' => 'Teh Tarik Ais', 'price' => 2.50, 'description' => 'Traditional Malaysian pulled milk tea with ice.', 'image' => 'tehtarik.jpg'],
        ['id' => 4, 'name' => 'Hainanese Chicken Rice', 'price' => 7.50, 'description' => 'Steamed chicken served with seasoned rice and ginger chili.', 'image' => 'chickenrice.jpg']
    ];

    function getMenuType($name) {
        $name_lower = strtolower($name);
        if (strpos($name_lower, 'teh') !== false || strpos($name_lower, 'ais') !== false) {
            return 'Beverages';
        }
        return 'Main Course';
    }
    ?>

    <div class="report-header">
        <div class="report-title-box">
            <h2>System Inventory Report</h2>
            <p class="report-date">Generated: <?php echo date('d/m/Y'); ?></p>
        </div>
        <a href="#" class="export-btn">Export PDF</a>
    </div>

    <div class="filters-section">
        <select class="filter-dropdown"><option>All Types</option></select>
        <select class="filter-dropdown"><option>All Categories</option></select>
        <select class="filter-dropdown"><option>Order Type</option></select>
    </div>

    <div class="report-table-container">
        <table class="report-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Menu Name</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($mock_menu as $row): ?>
                <tr>
                    <td><?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></td>
                    <td>
                        <div class="menu-picture">
                            <img src="../img/<?php echo $row['image']; ?>" alt="food" onerror="this.src='../img/logo.png'">
                        </div>
                    </td>
                    <td><strong><?php echo $row['name']; ?></strong></td>
                    <td><?php echo getMenuType($row['name']); ?></td>
                    <td class="menu-description"><?php echo $row['description']; ?></td>
                    <td>RM <?php echo number_format($row['price'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>