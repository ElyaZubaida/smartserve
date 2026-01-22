<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: loginadmin.php');
    exit();
}

include '../config/db_connect.php';

$current_page = basename($_SERVER['PHP_SELF']);

// Get filter and search parameters
$role_filter = isset($_GET['role']) ? $_GET['role'] : 'all';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch all users from database based on filter
$all_users = [];

if ($role_filter == 'all' || $role_filter == 'admin') {
    // Fetch admins (exclude soft-deleted)
    $admin_query = "SELECT admin_ID as id, admin_name as fullname, admin_username as username, admin_email as email, 'Admin' as role FROM admins WHERE is_deleted = 0";
    
    // Add search filter for admins
    if (!empty($search_query)) {
        $search_escaped = mysqli_real_escape_string($conn, $search_query);
        $admin_query .= " AND (admin_name LIKE '%$search_escaped%' OR admin_username LIKE '%$search_escaped%' OR admin_email LIKE '%$search_escaped%')";
    }
    
    $admin_query .= " ORDER BY admin_ID";
    $admin_result = mysqli_query($conn, $admin_query);
    while ($row = mysqli_fetch_assoc($admin_result)) {
        $all_users[] = $row;
    }
}

if ($role_filter == 'all' || $role_filter == 'staff') {
    // Fetch staff (exclude soft-deleted)
    $staff_query = "SELECT staffID as id, staffName as fullname, staffUsername as username, staffEmail as email, 'Staff' as role FROM staff WHERE is_deleted = 0";
    
    // Add search filter for staff
    if (!empty($search_query)) {
        $search_escaped = mysqli_real_escape_string($conn, $search_query);
        $staff_query .= " AND (staffName LIKE '%$search_escaped%' OR staffUsername LIKE '%$search_escaped%' OR staffEmail LIKE '%$search_escaped%')";
    }
    
    $staff_query .= " ORDER BY staffID";
    $staff_result = mysqli_query($conn, $staff_query);
    while ($row = mysqli_fetch_assoc($staff_result)) {
        $all_users[] = $row;
    }
}

if ($role_filter == 'all' || $role_filter == 'customer') {
    // Fetch students (customers) (exclude soft-deleted)
    $student_query = "SELECT student_ID as id, student_name as fullname, student_username as username, student_email as email, 'Customer' as role FROM students WHERE is_deleted = 0";
    
    // Add search filter for students
    if (!empty($search_query)) {
        $search_escaped = mysqli_real_escape_string($conn, $search_query);
        $student_query .= " AND (student_name LIKE '%$search_escaped%' OR student_username LIKE '%$search_escaped%' OR student_email LIKE '%$search_escaped%')";
    }
    
    $student_query .= " ORDER BY student_ID";
    $student_result = mysqli_query($conn, $student_query);
    while ($row = mysqli_fetch_assoc($student_result)) {
        $all_users[] = $row;
    }
}

// Check for success message
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Check for error message
$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - User Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="../staff/sastyle.css">
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-top">
            <div class="logo-container">
                <img src="../img/logo.png" alt="SmartServe Logo">
                <h3>Smart<span>Serve</span></h3>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li><a href="a_dashboard.php"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                    <li><a href="a_menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li><a href="a_order_management.php"><span class="material-symbols-outlined">order_approve</span> Orders</a></li>
                    <li class="active"><a href="user_management.php"><span class="material-symbols-outlined">manage_accounts</span> User Management</a></li>
                    <li><a href="a_report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
                    <li class="nav-divider"></li>
                    <li><a href="a_profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="logoutadmin.php" class="logout-link"><span class="material-symbols-outlined">logout</span> Log Out</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="main-content staff-report-content">
        <div class="header">
            <div class="title">
                <h2>User Management</h2>
                <p>Manage all users accounts (Admin, Staff, & Customers)</p>
            </div>
            <a href="adduser.php" class="staff-menu-add-btn">
                <span class="material-symbols-outlined">person_add</span> Add Users
            </a>
        </div>

        <?php if ($success_message): ?>
        <div class="alert alert-success">
            <span class="material-symbols-outlined">check_circle</span>
            <?php echo htmlspecialchars($success_message); ?>
        </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
        <div class="alert alert-error">
            <span class="material-symbols-outlined">error</span>
            <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>

        <!-- Filter and Search Bar -->
        <div class="filter-search-wrapper">
            <!-- Search Form (Full Width) -->
            <form method="GET" action="user_management.php" class="search-form" id="searchForm">
                <input type="hidden" name="role" value="<?php echo htmlspecialchars($role_filter); ?>">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    id="searchInput"
                    placeholder="Search by name, username, or email..." 
                    value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="search-btn">
                    <span class="material-symbols-outlined" style="font-size: 18px;">search</span>
                    Search
                </button>
            </form>

            <!-- Filter Row (Right-aligned) -->
            <div class="filter-row">
                <div class="category-filter">
                    <select name="role" class="filter-select" onchange="window.location.href='user_management.php?role=' + this.value + '<?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>'">
                        <option value="all" <?php echo ($role_filter == 'all') ? 'selected' : ''; ?>>All Roles</option>
                        <option value="admin" <?php echo ($role_filter == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="staff" <?php echo ($role_filter == 'staff') ? 'selected' : ''; ?>>Staff</option>
                        <option value="customer" <?php echo ($role_filter == 'customer') ? 'selected' : ''; ?>>Customer</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="report-table-container">
            <?php if (count($all_users) > 0): ?>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($all_users as $user): ?>
                    <tr>
                        <td><?php echo str_pad($user['id'], 3, '0', STR_PAD_LEFT); ?></td>
                        <td><strong><?php echo htmlspecialchars($user['fullname']); ?></strong></td>
                        <td>
                            <?php 
                                $roleClass = 'role-' . strtolower($user['role']);
                            ?>
                            <span class="role-badge <?php echo $roleClass; ?>">
                                <?php echo htmlspecialchars($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <div class="staff-menu-actions">
                                <a href="updateusers.php?id=<?php echo $user['id']; ?>&role=<?php echo strtolower($user['role']); ?>" class="staff-menu-edit">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                <?php if($user['username'] !== $_SESSION['admin_username']): ?>
                                    <a href="deleteuser.php?id=<?php echo $user['id']; ?>&role=<?php echo strtolower($user['role']); ?>" class="staff-menu-edit" style="color: #d32f2f;" onclick="return confirm('Are you sure you want to delete this account? This action cannot be undone.')">
                                        <span class="material-symbols-outlined">delete</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No Users Found</h3>
                    <p>
                        <?php 
                        if (!empty($search_query)) {
                            echo 'No results found for "<strong>' . htmlspecialchars($search_query) . '</strong>"';
                        } else if ($role_filter != 'all') {
                            echo 'No users found with role: <strong>' . ucfirst(htmlspecialchars($role_filter)) . '</strong>';
                        } else {
                            echo 'There are currently no users to display.';
                        }
                        ?>
                    </p>
                    <?php if (!empty($search_query) || $role_filter != 'all'): ?>
                        <a href="user_management.php" style="display: inline-block; margin-top: 10px; padding: 8px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">Clear Filters</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
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

</body>
</html>

<?php
mysqli_close($conn);
?>