<?php
session_start();

// Check if staff is logged in
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include '../config/db_connect.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_menu'])) {
    // Retrieve form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $menu_category = mysqli_real_escape_string($conn, $_POST['menuCategory']);
    $food_type = mysqli_real_escape_string($conn, $_POST['foodType']);
    $meal_type = mysqli_real_escape_string($conn, $_POST['mealType']);
    $cuisine = mysqli_real_escape_string($conn, $_POST['cuisine']);
    $flavour = mysqli_real_escape_string($conn, $_POST['flavour']);
    $portion = mysqli_real_escape_string($conn, $_POST['portion']);
    $availability = mysqli_real_escape_string($conn, $_POST['menuAvailability']);

    // Handle file upload
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../img/";
        $original_filename = basename($_FILES['image']['name']);
        $file_extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        
        // Generate unique filename
        $image_name = uniqid('menu_') . '.' . $file_extension;
        $target_file = $target_dir . $image_name;

        // Move uploaded file
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
            header("Location: addmenu.php");
            exit();
        }
    }

    // Staff is adding the menu - get staff_id from session, admin_id is NULL
    $staff_id = $_SESSION['staff_id'];
    $admin_id = NULL;

    // Prepare SQL insert statement
    $query = "INSERT INTO `menus` (
        `menuName`, 
        `menuDescription`, 
        `menuPrice`, 
        `menuImage`, 
        `menuCategory`,
        `foodType`,
        `mealType`,
        `cuisine`,
        `flavour`,
        `portion`,
        `menuAvailability`,
        `staff_id`,
        `admin_id`
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $availability_int = ($availability == 'Available') ? 1 : 0;
    $stmt->bind_param("ssdsssssssiii", 
        $name, 
        $description, 
        $price, 
        $image_name, 
        $menu_category,
        $food_type,
        $meal_type,
        $cuisine,
        $flavour,
        $portion,
        $availability_int,
        $staff_id,
        $admin_id
    );

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Menu item added successfully!";
        header("Location: menu_management.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error adding menu item: " . $stmt->error;
        header("Location: addmenu.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Add Menu</title>
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
                    <li><a href="dashboard.php"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                    <li class="active"><a href="menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li><a href="order_management.php"><span class="material-symbols-outlined">order_approve</span> Orders</a></li>
                    <li><a href="report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
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
        <a href="menu_management.php" class="btn-back">
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
                            <label for="menu_category">Menu Category</label>
                            <select id="menu_category" name="menuCategory" required>
                                <option value="">Select Category</option>
                                <option value="rice">Rice</option>
                                <option value="noodles">Noodles</option>
                                <option value="soup">Soup</option>
                                <option value="wrapnbuns">Wrap & Buns</option>
                                <option value="snacks">Snacks</option>
                                <option value="dessert">Dessert</option>
                                <option value="drinks">Drinks</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label for="food_type">Food Type <span class="tooltip-icon" title="Classify this meal based on a student's daily needs: Is it for staying healthy, fueling a long study session, a quick bite between classes, or a refreshing break?"><span class="material-symbols-outlined">info</span></span></label>
                            <select id="food_type" name="foodType" required>
                                <option value="">Select Food Type</option>
                                <option value="healthy">Healthy</option>
                                <option value="energy-boosting">Energy Boosting</option>
                                <option value="refreshing">Refreshing</option>
                                <option value="fastneasy">Fast & Easy</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="meal_type">Meal Type</label>
                            <select id="meal_type" name="mealType" required>
                                <option value="">Select Meal Type</option>
                                <option value="breakfast">Breakfast</option>
                                <option value="lunch">Lunch</option>
                                <option value="dinner">Dinner</option>
                                <option value="anytime">Anytime</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label for="cuisine">Cuisine</label>
                            <select id="cuisine" name="cuisine" required>
                                <option value="">Select Cuisine</option>
                                <option value="malay">Malay</option>
                                <option value="chinese">Chinese</option>
                                <option value="indian">Indian</option>
                                <option value="western">Western</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="flavour">Flavour</label>
                            <select id="flavour" name="flavour" required>
                                <option value="">Select Flavour</option>
                                <option value="spicy">Spicy</option>
                                <option value="sweet">Sweet</option>
                                <option value="savoury">Savoury</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label for="portion">Portion</label>
                            <select id="portion" name="portion" required>
                                <option value="">Select Portion</option>
                                <option value="light">Light (Light Snack)</option>
                                <option value="regular">Regular (Standard Meal)</option>
                                <option value="large">Large (High Hunger)</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="availability">Initial Availability</label>
                            <select id="availability" name="menuAvailability" required>
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

<?php
mysqli_close($conn);
?>