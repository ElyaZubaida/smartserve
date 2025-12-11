<!-- 
 Frontend: Elya 
 Backend: ? 
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Update Menu</title>
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
