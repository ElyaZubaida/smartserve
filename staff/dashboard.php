<!-- 
 Frontend: Insyirah 
 Backend: ? 
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="sastyle.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Logo -->
        <div class="logo">
            <img src="logo.png" alt="SmartServe Logo"> <!-- Replace with your logo image -->
        </div>

        <!-- Menu Links -->
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
        <!-- Start code here -->
     <!-- Dashboard Content -->
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
