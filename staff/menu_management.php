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

     <!-- Main Content -->
    <div class="main-content">
        <main>
            <h2>Menu</h2>
            <button class="add-menu-item"><a href="addmenu.php?id=1">Add</button></a> <!-- Button to add menu items -->
            <div class="menu-items">
                <!-- Link each menu item to a page to update/delete -->
                <a href="updatemenu.php?id=1" class="menu-item">
                    <img src="../img/nasilemak.jpg" alt="Nasi Lemak">
                    <div class="menu-item-details">
                        <h3>Name: Nasi Lemak</h3>
                        <p>Description: Yum yum yummy nasi lemak</p>
                        <p>Price: RM 5.00</p>
                    </div>
                </a>
                <a href="editmenu.php?id=2" class="menu-item">
                    <img src="../img/meegoreng.jpg" alt="Mee Goreng">
                    <div class="menu-item-details">
                        <h3>Name: Mee Goreng</h3>
                        <p>Description: Yum yum yummy mee goreng</p>
                        <p>Price: RM 4.50</p>
                    </div>
                </a>
                <!-- Add more menu items as needed -->
            </div>
        </main>
    </div>
</body>

</html>
