<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Reports</title>
    <link rel="stylesheet" href="sastyle.css">
    
    <style>
        /* ADDITIONAL CSS - Report Page */
        
        /* Report Header */
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .report-title-box {
            background-color: white;
            padding: 20px 40px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .report-title-box h2 {
            font-size: 32px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }

        .report-title-box .report-date {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        /* Export Button - Fixed position */
        .export-btn {
            position: absolute;
            right: 40px;
            top: 0;
            background-color: #000;
            color: white;
            padding: 12px 40px;
            border-radius: 25px;
            border: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .export-btn:hover {
            background-color: #333;
        }

        /* Filters Section - IN ONE LINE */
        .filters-section {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .filter-dropdown {
            padding: 12px 30px;
            border-radius: 25px;
            border: none;
            background-color: white;
            font-size: 16px;
            cursor: pointer;
            min-width: 200px;
        }

        /* Report Table */
        .report-table-container {
            background-color: #d3d3d3;
            padding: 20px;
            border-radius: 10px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        .report-table thead {
            background-color: #888;
        }

        .report-table th {
            padding: 15px 10px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #000;
            border: 1px solid #666;
        }

        .report-table tbody tr {
            background-color: #b8b8b8;
        }

        .report-table tbody tr:nth-child(even) {
            background-color: #c8c8c8;
        }

        .report-table td {
            padding: 15px 10px;
            text-align: center;
            font-size: 14px;
            color: #000;
            border: 1px solid #666;
        }

        .menu-picture {
            width: 60px;
            height: 60px;
            font-size: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .menu-description {
            font-size: 12px;
            line-height: 1.4;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .export-btn {
                position: static;
                display: block;
                margin: 20px auto 0;
            }

            .filters-section {
                flex-direction: column;
            }

            .report-table {
                font-size: 12px;
            }

            .report-table th,
            .report-table td {
                padding: 8px 5px;
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

        // Check connection
        if (!$connection) {
            echo '<div style="color: red; text-align: center;">❌ Database connection failed!</div>';
            exit;
        }

        // Get filter values
        $filter_type = isset($_GET['type']) ? $_GET['type'] : '';
        $filter_category = isset($_GET['category']) ? $_GET['category'] : '';
        $filter_order_type = isset($_GET['order_type']) ? $_GET['order_type'] : '';

        // Build query
        $query = "SELECT menu_id, name, price, description, image FROM menu WHERE 1=1";
        $result = mysqli_query($connection, $query);
        ?>

        <!-- Report Header -->
        <div class="report-header">
            <div class="report-title-box">
                <h2>Report</h2>
                <p class="report-date">Date: <?php echo date('d/m/Y'); ?></p>
            </div>
            
            <!-- Export Button -->
            <a href="export_report.php" class="export-btn">Export</a>
        </div>

        <!-- Filters Section - IN ONE LINE -->
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

        <!-- Report Table -->
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
                    // Menu emojis mapping
                    $item_emojis = [
                        'nasi lemak' => '🍚',
                        'roti canai' => '🫓',
                        'mee goreng' => '🍜',
                        'teh tarik' => '🧋',
                        'teh o ais' => '🧋',
                        'nasi goreng' => '🍛',
                        'default' => '🍽️'
                    ];

                    // Determine menu type
                    function getMenuType($name) {
                        $name_lower = strtolower($name);
                        if (strpos($name_lower, 'teh') !== false || strpos($name_lower, 'ais') !== false) {
                            return 'Beverages';
                        }
                        return 'Breakfast';
                    }

                    if (!$result || mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='6'>";
                        echo "<div class='empty-state'>";
                        echo "<h3>No menu items found</h3>";
                        echo "<p>Add menu items to see reports.</p>";
                        echo "</div>";
                        echo "</td></tr>";
                    } else {
                        $menu_id_counter = 1;
                        while($row = mysqli_fetch_assoc($result)) {
                            $item_name_lower = strtolower($row['name']);
                            $emoji = $item_emojis[$item_name_lower] ?? $item_emojis['default'];
                            $menu_type = getMenuType($row['name']);
                    ?>
                    <tr>
                        <td><?php echo str_pad($menu_id_counter++, 3, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="menu-picture"><?php echo $emoji; ?></div>
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