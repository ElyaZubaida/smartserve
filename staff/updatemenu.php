<?php
session_start();
// Include database connection
include '../config/db_connect.php';

// Check if menu ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "No menu item selected.";
    header("Location: menu_management.php");
    exit();
}

$menu_id = mysqli_real_escape_string($conn, $_GET['id']);

// NEW CODE GOES HERE ↓↓↓
// Fetch existing menu item details
$query = "SELECT * FROM `MENU` WHERE `MENU_ID` = '$menu_id' AND `IS_DELETED` = 0";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error_message'] = "Menu item not found or has been deleted.";
    header("Location: menu_management.php");
    exit();
}

// Rest of your existing code continues here...
$menu_item = mysqli_fetch_assoc($result);

// Handle Update Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if update or delete action
    if (isset($_POST['update_menu'])) {
        // Retrieve form data
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $price = mysqli_real_escape_string($conn, $_POST['price']);
        $menu_category = mysqli_real_escape_string($conn, $_POST['menu_category']);
        $food_type = mysqli_real_escape_string($conn, $_POST['food_type']);
        $meal_type = mysqli_real_escape_string($conn, $_POST['meal_type']);
        $cuisine = mysqli_real_escape_string($conn, $_POST['cuisine']);
        $flavour = mysqli_real_escape_string($conn, $_POST['flavour']);
        $portion = mysqli_real_escape_string($conn, $_POST['portion']);
        $availability = mysqli_real_escape_string($conn, $_POST['availability']);

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
                header("Location: updatemenu.php?id=" . $menu_id);
                exit();
            }
        } else {
            // Keep existing image if no new image uploaded
            $image_query = "SELECT MENU_IMAGE FROM `MENU` WHERE MENU_ID = '$menu_id'";
            $image_result = mysqli_query($conn, $image_query);
            $image_row = mysqli_fetch_assoc($image_result);
            $image_name = $image_row['MENU_IMAGE'];
        }

        // Prepare SQL update statement
        $query = "UPDATE `MENU` SET 
            `MENU_NAME` = ?, 
            `MENU_DESC` = ?, 
            `MENU_PRICE` = ?, 
            `MENU_IMAGE` = ?, 
            `MENU_CATEGORY` = ?,
            `FOOD_TYPE` = ?,
            `MEAL_TYPE` = ?,
            `CUISINE` = ?,
            `FLAVOUR` = ?,
            `PORTION` = ?,
            `MENU_AVAILABILITY` = ?
        WHERE `MENU_ID` = ?";

        $stmt = $conn->prepare($query);
        $availability_int = ($availability == 'Available') ? 1 : 0;
        $stmt->bind_param("ssdsssssssii", 
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
        $menu_id 
    );

        // Execute the statement
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Menu item updated successfully!";
            header("Location: menu_management.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error updating menu item: " . $stmt->error;
            header("Location: updatemenu.php?id=" . $menu_id);
            exit();
        }
    } 
    // Handle Soft Delete
    elseif (isset($_POST['delete_menu'])) {
        // Soft delete query
        $delete_query = "UPDATE `MENU` SET `IS_DELETED` = 1 WHERE `MENU_ID` = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $menu_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Menu item soft deleted successfully!";
            header("Location: menu_management.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error deleting menu item: " . $stmt->error;
            header("Location: updatemenu.php?id=" . $menu_id);
            exit();
        }
    }
}

// Fetch existing menu item details
$query = "SELECT * FROM `MENU` WHERE `MENU_ID` = '$menu_id'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error_message'] = "Menu item not found.";
    header("Location: menu_management.php");
    exit();
}

$menu_item = mysqli_fetch_assoc($result);
?>

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

    <!-- Main Content -->
    <div class="main-content staff-menu-content">
    <div class="header">
        <div class="title">
            <h2>Update Menu Item</h2>
            <p>Modify food details or remove the item from the system</p>
        </div>
        <a href="menu_management.php" class="btn-back">
            <span class="material-symbols-outlined">arrow_back</span> Back to List
        </a>
    </div>

    <div class="update-form-container">
        <form id="editForm" method="POST" enctype="multipart/form-data" class="staff-update-form">
            <div class="form-grid">
                <div class="image-upload-section">
                    <div class="menu-item-image">
                        <img id="menuItemImage" 
                             src="<?php 
                                echo !empty($menu_item['MENU_IMAGE']) 
                                    ? '../img/' . htmlspecialchars($menu_item['MENU_IMAGE']) 
                                    : '../img/placeholder.jpg'; 
                             ?>" 
                             alt="<?php echo htmlspecialchars($menu_item['MENU_NAME']); ?>" 
                             class="menu-item-img">
                    </div>
                    <label class="file-upload-label">
                        <span class="material-symbols-outlined">cloud_upload</span> Change Photo
                        <input type="file" name="image" id="image" class="file-input" accept="image/*" onchange="previewImage(event)">
                    </label>
                </div>

                <div class="form-inputs">
                    <div class="input-group">
                        <label for="name">Food Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($menu_item['MENU_NAME']); ?>" required>
                    </div>

                    <div class="input-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?php echo htmlspecialchars($menu_item['MENU_DESC']); ?></textarea>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label for="price">Price (RM)</label>
                            <input type="number" step="0.01" id="price" name="price" value="<?php echo number_format($menu_item['MENU_PRICE'], 2); ?>" required>
                        </div>

                        <div class="input-group">
                            <label for="menu_category">Menu Category</label>
                            <select id="menu_category" name="menu_category" required>
                                <option value="rice" <?php echo ($menu_item['MENU_CATEGORY'] == 'rice') ? 'selected' : ''; ?>>Rice</option>
                                <option value="noodles" <?php echo ($menu_item['MENU_CATEGORY'] == 'noodles') ? 'selected' : ''; ?>>Noodles</option>
                                <option value="soup" <?php echo ($menu_item['MENU_CATEGORY'] == 'soup') ? 'selected' : ''; ?>>Soup</option>
                                <option value="dessert" <?php echo ($menu_item['MENU_CATEGORY'] == 'dessert') ? 'selected' : ''; ?>>Dessert</option>
                                <option value="drinks" <?php echo ($menu_item['MENU_CATEGORY'] == 'drinks') ? 'selected' : ''; ?>>Drinks</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label for="food_type">Food Type</label>
                            <select id="food_type" name="food_type" required>
                                <option value="rice" <?php echo ($menu_item['FOOD_TYPE'] == 'rice') ? 'selected' : ''; ?>>Rice</option>
                                <option value="noodles" <?php echo ($menu_item['FOOD_TYPE'] == 'noodles') ? 'selected' : ''; ?>>Noodles</option>
                                <option value="soup" <?php echo ($menu_item['FOOD_TYPE'] == 'soup') ? 'selected' : ''; ?>>Soup</option>
                                <option value="dessert" <?php echo ($menu_item['FOOD_TYPE'] == 'dessert') ? 'selected' : ''; ?>>Dessert</option>
                                <option value="drinks" <?php echo ($menu_item['FOOD_TYPE'] == 'drinks') ? 'selected' : ''; ?>>Drinks</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="meal_type">Meal Type</label>
                            <select id="meal_type" name="meal_type" required>
                                <option value="breakfast" <?php echo ($menu_item['MEAL_TYPE'] == 'breakfast') ? 'selected' : ''; ?>>Breakfast</option>
                                <option value="lunch" <?php echo ($menu_item['MEAL_TYPE'] == 'lunch') ? 'selected' : ''; ?>>Lunch</option>
                                <option value="dinner" <?php echo ($menu_item['MEAL_TYPE'] == 'dinner') ? 'selected' : ''; ?>>Dinner</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label for="cuisine">Cuisine</label>
                            <select id="cuisine" name="cuisine" required>
                                <option value="malay" <?php echo ($menu_item['CUISINE'] == 'malay') ? 'selected' : ''; ?>>Malay</option>
                                <option value="chinese" <?php echo ($menu_item['CUISINE'] == 'chinese') ? 'selected' : ''; ?>>Chinese</option>
                                <option value="indian" <?php echo ($menu_item['CUISINE'] == 'indian') ? 'selected' : ''; ?>>Indian</option>
                                <option value="western" <?php echo ($menu_item['CUISINE'] == 'western') ? 'selected' : ''; ?>>Western</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="flavour">Flavour</label>
                            <select id="flavour" name="flavour" required>
                                <option value="spicy" <?php echo ($menu_item['FLAVOUR'] == 'spicy') ? 'selected' : ''; ?>>Spicy</option>
                                <option value="sweet" <?php echo ($menu_item['FLAVOUR'] == 'sweet') ? 'selected' : ''; ?>>Sweet</option>
                                <option value="savoury" <?php echo ($menu_item['FLAVOUR'] == 'savoury') ? 'selected' : ''; ?>>Savoury</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label for="portion">Portion</label>
                            <select id="portion" name="portion" required>
                                <option value="light" <?php echo ($menu_item['PORTION'] == 'light') ? 'selected' : ''; ?>>Light</option>
                                <option value="medium" <?php echo ($menu_item['PORTION'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                                <option value="large" <?php echo ($menu_item['PORTION'] == 'large') ? 'selected' : ''; ?>>Large</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="availability">Availability Status</label>
                            <select id="availability" name="availability" required>
                                <option value="Available" <?php echo ($menu_item['MENU_AVAILABILITY'] == 1) ? 'selected' : ''; ?>>Available</option>
                                <option value="Out of Stock" <?php echo ($menu_item['MENU_AVAILABILITY'] == 0) ? 'selected' : ''; ?>>Out of Stock</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="update_menu" class="update-confirm-btn">Save Changes</button>
                            <button type="button" onclick="confirmDelete(<?php echo $menu_item['MENU_ID']; ?>)" class="delete-menu-btn">Delete Menu</button>
                            <script>
                            function confirmDelete(menuId) {
                                if (confirm('Are you sure you want to soft delete this menu item?')) {
                                    window.location.href = 'deletemenu.php?id=' + menuId;
                                }
                            }
                            </script>
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
// Close database connection
mysqli_close($conn);
?>