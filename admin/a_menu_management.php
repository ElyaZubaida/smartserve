<!-- 
 Frontend: Elya 
 Backend: Amirah 
 -->
<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginadmin.php");
    exit;
}
// Include database connection
include '../config/db_connect.php';

// Get filter and search parameters
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Build query based on filters
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
";

// Add category filter
if ($category_filter != 'all') {
    $query .= " AND `menuCategory` = '" . mysqli_real_escape_string($conn, $category_filter) . "'";
}

// Add search filter
if (!empty($search_query)) {
    $search_escaped = mysqli_real_escape_string($conn, $search_query);
    $query .= " AND (`menuName` LIKE '%$search_escaped%' OR `menuDescription` LIKE '%$search_escaped%')";
}

$query .= " ORDER BY `created_at` DESC";

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
    <title>SmartServe - Menu Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="../staff/sastyle.css">
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
                    <li><a href="a_dashboard.php"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                    <li class="active"><a href="a_menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li><a href="a_order_management.php"><span class="material-symbols-outlined">order_approve</span> Orders</a></li>
                    <li><a href="user_management.php"><span class="material-symbols-outlined">manage_accounts</span> User Management</a></li>
                    <li><a href="a_report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
                    <li class="nav-divider"></li>
                    <li><a href="a_profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="logoutadmin.php" class="logout-link"><span class="material-symbols-outlined">logout</span> Log Out</a></li>
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
            <a href="a_addmenu.php" class="staff-menu-add-btn">
                <span class="material-symbols-outlined">add_box</span>
                Add Menu
            </a>
        </div>

        <!-- Filter and Search Bar -->
        <div class="filter-search-wrapper">
            <!-- Search Form (Full Width) -->
            <form method="GET" action="a_menu_management.php" class="search-form" id="searchForm">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    id="searchInput"
                    placeholder="Search menu items..." 
                    value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="search-btn">
                    <span class="material-symbols-outlined" style="font-size: 18px;">search</span>
                    Search
                </button>
            </form>

            <!-- Filter Row (Right-aligned) -->
            <div class="filter-row">
                <div class="category-filter">
                    <select name="category" class="filter-select" onchange="window.location.href='a_menu_management.php?category=' + this.value + '<?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>'">
                        <option value="all" <?php echo ($category_filter == 'all') ? 'selected' : ''; ?>>All Categories</option>
                        <option value="rice" <?php echo ($category_filter == 'rice') ? 'selected' : ''; ?>>Rice</option>
                        <option value="noodles" <?php echo ($category_filter == 'noodles') ? 'selected' : ''; ?>>Noodles</option>
                        <option value="soup" <?php echo ($category_filter == 'soup') ? 'selected' : ''; ?>>Soup</option>
                        <option value="wrapnbuns" <?php echo ($category_filter == 'wrapnbuns') ? 'selected' : ''; ?>>Wrap & Buns</option>
                        <option value="snacks" <?php echo ($category_filter == 'snacks') ? 'selected' : ''; ?>>Snacks</option>
                        <option value="dessert" <?php echo ($category_filter == 'dessert') ? 'selected' : ''; ?>>Dessert</option>
                        <option value="drinks" <?php echo ($category_filter == 'drinks') ? 'selected' : ''; ?>>Drinks</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="staff-menu-grid">
            <?php 
            // Check if there are any menu items
            if (mysqli_num_rows($result) > 0) {
                // Loop through menu items
                while ($menu_item = mysqli_fetch_assoc($result)) {
                    $is_out_of_stock = (isset($menu_item['menuAvailability']) && (int)$menu_item['menuAvailability'] === 0);
            ?>
            <div class="staff-menu-card <?php echo $is_out_of_stock ? 'out-of-stock' : ''; ?>">
                <div class="staff-menu-img">
                    <img src="<?php 
                        echo !empty($menu_item['menuImage']) 
                            ? '../img/' . htmlspecialchars($menu_item['menuImage']) 
                            : '../img/placeholder.jpg'; 
                    ?>" alt="<?php echo htmlspecialchars($menu_item['menuName']); ?>">
                    <?php if ($is_out_of_stock): ?>
                        <span class="stock-badge">Out of Stock</span>
                    <?php endif; ?>
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
                            <a href="a_updatemenu.php?id=<?php echo $menu_item['menuID']; ?>" class="staff-menu-edit">
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
                        <p>';
                if (!empty($search_query)) {
                    echo 'No results found for "<strong>' . htmlspecialchars($search_query) . '</strong>"';
                } else if ($category_filter != 'all') {
                    echo 'No items found in category: <strong>' . htmlspecialchars($category_filter) . '</strong>';
                } else {
                    echo 'Click "Add Menu" to create your first menu item.';
                }
                echo '</p>';
                if (!empty($search_query) || $category_filter != 'all') {
                    echo '<a href="a_menu_management.php" style="display: inline-block; margin-top: 10px; padding: 8px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">Clear Filters</a>';
                }
                echo '</div>';
            }
            ?>
        </div>
    </main>
</div>

<script>
    // Auto-search as user types
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            searchForm.submit();
        }, 500); // Wait 500ms after user stops typing
    });
</script>

<?php
// Close database connection
mysqli_close($conn);
?>
</body>
</html>
