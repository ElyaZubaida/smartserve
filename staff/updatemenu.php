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
    <div class="main-content staff-menu-content">
    <div class="staff-menu-header">
        <div class="staff-menu-title">
            <h2>Update Menu Item</h2>
            <p>Modify food details or remove the item from the system</p>
        </div>
        <a href="menu_management.php" class="staff-menu-add-btn" style="background-color: #666;">
            <span class="material-symbols-outlined">arrow_back</span> Back to List
        </a>
    </div>

    <div class="update-form-container">
        <form id="editForm" class="staff-update-form">
            <div class="form-grid">
                <div class="image-upload-section">
                    <div class="menu-item-image">
                        <img id="menuItemImage" src="../img/nasilemak.jpg" alt="Nasi Lemak" class="menu-item-img">
                    </div>
                    <label class="file-upload-label">
                        <span class="material-symbols-outlined">cloud_upload</span> Change Photo
                        <input type="file" class="file-input">
                    </label>
                </div>

                <div class="form-inputs">
                    <div class="input-group">
                        <label for="name">Food Name</label>
                        <input type="text" id="name" name="name" value="Nasi Lemak" required>
                    </div>

                    <div class="input-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required>Yum yum yummy nasi lemak</textarea>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label for="price">Price (RM)</label>
                            <input type="text" id="price" name="price" value="5.00" required>
                        </div>

                        <div class="input-group">
                            <label for="availability">Availability Status</label>
                            <select id="availability" name="availability">
                                <option value="Available" selected>Available</option>
                                <option value="Out of Stock">Out of Stock</option>
                                <option value="N/A">N/A</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" id="updateBtn" class="update-confirm-btn">Save Changes</button>
                        <button type="button" id="deleteBtn" class="delete-menu-btn">Delete Menu</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</body>

</html>
