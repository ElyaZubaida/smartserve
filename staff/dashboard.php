<!-- 
 Frontend: Insyirah 
 Backend: Amirah 
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Dashboard</title>
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
        <div class="dashboard-container">
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>Today's Sale</h3>
                    <div class="value">RM 1000.00</div>
                </div>
                <div class="dashboard-card">
                    <h3>Items</h3>
                    <div class="value">30</div>
                </div>
                <div class="dashboard-card">
                    <h3>New Orders</h3>
                    <div class="value">3</div>
                </div>
                <div class="dashboard-card">
                    <h3>Uncomplete</h3>
                    <div class="value">2</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
