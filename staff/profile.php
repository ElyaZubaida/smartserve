<!-- 
 Frontend: Mina 
 Backend: Amirah
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Profile</title>
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

<!-- ================= MAIN CONTENT ================= -->
    <!-- Main content area -->
    <div class="main-content profile-content">
    <div class="header">
        <div class="title">
            <h2>Profile</h2>
            <p>Manage your profile information</p>
        </div>
    </div>
    
    <div class="staff-profile-page">
        <div class="profile-card">
            <h2>My Profile</h2>
            <form>
                <input type="text" class="input-field" placeholder="Full Name" required>
                <input type="email" class="input-field" placeholder="Email" required>
                <input type="text" class="input-field" placeholder="Username" required>
                <input type="password" class="input-field" placeholder="Password" required>

                <button type="submit" class="btn-update">Update Profile</button>
            </form>
        </div>
    </div>
</div>
</body>

</html>
