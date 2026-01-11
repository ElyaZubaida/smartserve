<!-- 
 Frontend: Elya 
 Backend: Amirah 
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Add Menu</title>
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

    <!-- Main content area -->
    <div class="main-content staff-menu-content">
    <div class="header">
        <div class="title">
            <h2>Add New Menu Item</h2>
            <p>Create a new food item for the canteen system</p>
        </div>
        <a href="a_menu_management.php" class="btn-back">
            <span class="material-symbols-outlined">arrow_back</span> Back to List
        </a>
    </div>

    <div class="update-form-container">
        <form id="addForm" method="POST" enctype="multipart/form-data" class="staff-update-form">
            <div class="form-grid">
                
                <div class="image-upload-section">
                    <div class="menu-item-image">
                        <img id="menuItemImage" src="../img/placeholder.jpg" alt="Menu Preview" class="menu-item-img">
                    </div>
                    <label class="file-upload-label">
                        <span class="material-symbols-outlined">add_a_photo</span> Upload Product Image
                        <input type="file" name="image" id="image" class="file-input" accept="image/*" required onchange="previewImage(event)">
                    </label>
                    <p style="font-size: 12px; color: #888; margin-top: 10px;">Recommended size: 800x600px</p>
                </div>

                <div class="form-inputs">
                    <div class="input-group">
                        <label for="name">Food Name</label>
                        <input type="text" id="name" name="name" placeholder="e.g., Nasi Lemak Ayam" required>
                    </div>

                    <div class="input-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Describe the ingredients or taste..." required></textarea>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label for="price">Price (RM)</label>
                            <input type="number" step="0.01" id="price" name="price" placeholder="0.00" required>
                        </div>

                        <div class="input-group">
                            <label for="availability">Initial Availability</label>
                            <select id="availability" name="availability" required>
                                <option value="Available" selected>Available</option>
                                <option value="Out of Stock">Out of Stock</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="add_menu" class="update-confirm-btn" style="background-color: #007bff;">
                            Add Menu 
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('menuItemImage');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</body>

</html>
