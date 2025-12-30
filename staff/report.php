<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Reports</title>
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
        <div class="sidebar-header">
            <h2 style="color: white;">SmartServe</h2>
        </div>

        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="menu_management.php">Menu Management</a></li>
                <li><a href="order_management.php">Order Management</a></li>
                <li><a href="report.php">Reports</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="../logout.php">Log Out</a></li>
            </ul>
        </nav>
    </div>

    <!-- Main content area -->
    <div class="main-content">
        <?php
        // Database connection
        include('../databaseconnect.php');

        if (!$connection) {
            echo '<div style="color: red; text-align: center; font-size: 13px;">❌ Database connection failed!</div>';
            exit;
        }

        $filter_type = isset($_GET['type']) ? $_GET['type'] : '';
        $filter_category = isset($_GET['category']) ? $_GET['category'] : '';
        $filter_order_type = isset($_GET['order_type']) ? $_GET['order_type'] : '';

        $query = "SELECT menu_id, name, price, description, image FROM menu WHERE 1=1";
        $result = mysqli_query($connection, $query);
        ?>

        <!-- Report Header - CENTERED -->
        <div class="report-header">
            <div class="report-title-box">
                <h2>Report</h2>
                <p class="report-date">Date: <?php echo date('d/m/Y'); ?></p>
            </div>
            
            <a href="export_report.php" class="export-btn" target="_blank">Export</a>
        </div>

        <!-- Filters Section - CENTERED -->
        <form method="GET" action="">
            <div class="filters-section">
                <select name="type" class="filter-dropdown" onchange="this.form.submit()">
                    <option value="">Type ▼</option>
                    <option value="breakfast" <?php echo ($filter_type == 'breakfast') ? 'selected' : ''; ?>>Breakfast</option>
                    <option value="lunch" <?php echo ($filter_type == 'lunch') ? 'selected' : ''; ?>>Lunch</option>
                    <option value="dinner" <?php echo ($filter_type == 'dinner') ? 'selected' : ''; ?>>Dinner</option>
                    <option value="beverages" <?php echo ($filter_type == 'beverages') ? 'selected' : ''; ?>>Beverages</option>
                </select>

                <select name="category" class="filter-dropdown" onchange="this.form.submit()">
                    <option value="">Category ▼</option>
                    <option value="malay" <?php echo ($filter_category == 'malay') ? 'selected' : ''; ?>>Malay</option>
                    <option value="chinese" <?php echo ($filter_category == 'chinese') ? 'selected' : ''; ?>>Chinese</option>
                    <option value="indian" <?php echo ($filter_category == 'indian') ? 'selected' : ''; ?>>Indian</option>
                    <option value="western" <?php echo ($filter_category == 'western') ? 'selected' : ''; ?>>Western</option>
                </select>

                <select name="order_type" class="filter-dropdown" onchange="this.form.submit()">
                    <option value="">Order Type ▼</option>
                    <option value="dine_in" <?php echo ($filter_order_type == 'dine_in') ? 'selected' : ''; ?>>Dine In</option>
                    <option value="takeaway" <?php echo ($filter_order_type == 'takeaway') ? 'selected' : ''; ?>>Takeaway</option>
                    <option value="delivery" <?php echo ($filter_order_type == 'delivery') ? 'selected' : ''; ?>>Delivery</option>
                </select>
            </div>
        </form>

        <!-- Report Table - CENTERED -->
        <div class="report-table-container">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Menu ID</th>
                        <th>Menu Picture</th>
                        <th>Menu Name</th>
                        <th>Menu Type</th>
                        <th>Menu Description</th>
                        <th>Menu Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    function getMenuType($name) {
                        $name_lower = strtolower($name);
                        if (strpos($name_lower, 'teh') !== false || strpos($name_lower, 'ais') !== false) {
                            return 'Beverages';
                        }
                        return 'Breakfast';
                    }

                    if (!$result || mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='6'>";
                        echo "<div class='empty-state'>No menu items found</div>";
                        echo "</td></tr>";
                    } else {
                        $menu_id_counter = 1;
                        while($row = mysqli_fetch_assoc($result)) {
                            $menu_type = getMenuType($row['name']);
                            $image_path = !empty($row['image']) ? '../img/' . $row['image'] : '../img/default-food.jpg';
                    ?>
                    <tr>
                        <td><?php echo str_pad($menu_id_counter++, 3, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="menu-picture">
                                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" onerror="this.src='../img/default-food.jpg'">
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo $menu_type; ?></td>
                        <td class="menu-description"><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>RM <?php echo number_format($row['price'], 2); ?></td>
                    </tr>
                    <?php 
                        }
                    }
                    
                    mysqli_close($connection);
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
</body>
</html>