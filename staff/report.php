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