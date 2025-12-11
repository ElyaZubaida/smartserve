<!-- 
 Frontend: Elya 
 Backend: ? 
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Add Menu</title>
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
        <h2>Add Menu</h2>

        <!-- Add Menu Form -->
        <form id="addForm" method="POST" enctype="multipart/form-data">
            <div class="menu-item-image">
                <img id="menuItemImage" src="img/placeholder.jpg" alt="Menu Image" class="menu-item-img">
                <input type="file" name="image" id="image" class="file-input" accept="image/*" required>
            </div>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter menu name" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" placeholder="Enter menu description" required></textarea>

            <label for="price">Price (RM):</label>
            <input type="number" id="price" name="price" placeholder="Enter menu price" required>

            <label for="availability">Availability:</label>
            <select id="availability" name="availability" required>
                <option value="Available">Available</option>
                <option value="Out of Stock">Out of Stock</option>
            </select>

            <button type="submit" name="add_menu" class="update-btn">Add Menu</button>
        </form>
    </div>

</body>
</html>
