<!-- 
 Frontend: Elya 
 Backend: Amirah 
 -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Menu Management</title>
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
                    <li class="active"><a href="a_dashboard.php"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                    <li><a href="a_menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li><a href="a_order_management.php"><span class="material-symbols-outlined">order_approve</span> Order Management</a></li>
                    <li><a href="user_management.php"><span class="material-symbols-outlined">manage_accounts</span> User Management</a></li>
                    <li><a href="a_report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
                    <li class="nav-divider"></li>
                    <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="../logout.php" class="logout-link"><span class="material-symbols-outlined">logout</span> Log Out</a></li>
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

        <div class="staff-menu-grid">
            <div class="staff-menu-card">
                <div class="staff-menu-img">
                    <img src="../img/nasilemak.jpg" alt="Nasi Lemak">
                </div>
                <div class="staff-menu-details">
                    <span class="staff-menu-category">Rice</span>
                    <h3>Nasi Lemak</h3>
                    <p>Yum yum yummy nasi lemak</p>
                    <div class="staff-menu-footer">
                        <span class="staff-menu-price">RM 5.00</span>
                        <div class="staff-menu-actions">
                            <a href="a_updatemenu.php?id=1" class="staff-menu-edit"><span class="material-symbols-outlined">edit</span></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="staff-menu-card">
                <div class="staff-menu-img">
                    <img src="../img/meegoreng.jpg" alt="Mee Goreng">
                </div>
                <div class="staff-menu-details">
                    <span class="staff-menu-category">Noodles</span>
                    <h3>Mee Goreng</h3>
                    <p>Yum yum yummy mee goreng</p>
                    <div class="staff-menu-footer">
                        <span class="staff-menu-price">RM 4.50</span>
                        <div class="staff-menu-actions">
                            <a href="editmenu.php?id=2" class="staff-menu-edit"><span class="material-symbols-outlined">edit</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>

</html>
