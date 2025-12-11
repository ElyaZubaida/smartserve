<!-- 
 Frontend: Elya 
 Backend: ? 
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Menu Management</title>
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
