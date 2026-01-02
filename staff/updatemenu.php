<!-- 
 Frontend: Elya 
 Backend: Amirah
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Update Menu</title>
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
    <div class="main-content" >
        <h2>Update Menu</h2>

        <!-- Edit Form -->
        <form id="editForm">
            <div class="menu-item-image">
                <img id="menuItemImage" src="../img/nasilemak.jpg" alt="Nasi Lemak" class="menu-item-img">
            </div>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="Nasi Lemak" required>

            <label for="description">Description:</label>
            <input type="text" id="description" name="description" value="Yum yum yummy nasi lemak" required>

            <label for="price">Price:</label>
            <input type="text" id="price" name="price" value="5.00" required>

            <label for="availability">Availability:</label>
            <select id="availability" name="availability">
                <option value="N/A">N/A</option>
                <option value="Available" selected>Available</option>
                <option value="Out of Stock">Out of Stock</option>
            </select>

            <button type="button" id="updateBtn" class="update-btn">Update</button>
            <button type="button" id="deleteBtn" class="delete-btn">Delete Menu</button>
        </form>
    </div>
</body>

</html>
