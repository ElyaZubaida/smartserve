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
    
    <style>
        body {
            overflow-x: hidden;
        }

        .main-content {
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin-left: 250px;
        }

        /* Report Header - CENTERED */
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            width: 100%;
            max-width: 850px;
        }

        .report-title-box {
            background-color: white;
            padding: 12px 25px;
            border-radius: 16px;
            display: inline-block;
            margin-bottom: 12px;
        }

        .report-title-box h2 {
            font-size: 22px;
            font-weight: bold;
            margin: 0 0 3px 0;
        }

        .report-title-box .report-date {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        /* Export Button - POSITIONED */
        .export-btn {
            position: absolute;
            right: 0;
            top: 0;
            background-color: #000;
            color: white;
            padding: 8px 28px;
            border-radius: 20px;
            border: none;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .export-btn:hover {
            background-color: #333;
        }

        /* Filters Section - CENTERED */
        .filters-section {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            width: 100%;
            max-width: 850px;
        }

        .filter-dropdown {
            padding: 8px 20px;
            border-radius: 20px;
            border: none;
            background-color: white;
            font-size: 13px;
            cursor: pointer;
            min-width: 150px;
        }

        /* Report Table Container - CENTERED */
        .report-table-container {
            background-color: #d3d3d3;
            padding: 15px;
            border-radius: 8px;
            width: 100%;
            max-width: 850px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            table-layout: fixed;
        }

        .report-table thead {
            background-color: #888;
        }

        .report-table th {
            padding: 10px 6px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            color: #000;
            border: 1px solid #666;
            word-wrap: break-word;
        }

        /* Column Widths */
        .report-table th:nth-child(1) { width: 8%; }
        .report-table th:nth-child(2) { width: 10%; }
        .report-table th:nth-child(3) { width: 16%; }
        .report-table th:nth-child(4) { width: 12%; }
        .report-table th:nth-child(5) { width: 36%; }
        .report-table th:nth-child(6) { width: 12%; }

        .report-table tbody tr {
            background-color: #b8b8b8;
        }

        .report-table tbody tr:nth-child(even) {
            background-color: #c8c8c8;
        }

        .report-table td {
            padding: 10px 6px;
            text-align: center;
            font-size: 12px;
            color: #000;
            border: 1px solid #666;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Menu Image */
        .menu-picture {
            width: 45px;
            height: 45px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 50%;
            background-color: white;
        }

        .menu-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .menu-description {
            font-size: 10px;
            line-height: 1.3;
            text-align: left;
            padding: 6px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 25px;
            color: #666;
            font-size: 13px;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .report-table {
                font-size: 11px;
            }
            
            .menu-picture {
                width: 38px;
                height: 38px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 10px;
                margin-left: 0;
            }

            .export-btn {
                position: static;
                display: block;
                margin: 12px auto 0;
            }

            .filters-section {
                flex-direction: column;
            }

            .report-table th:nth-child(5),
            .report-table td:nth-child(5) {
                display: none;
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