<!-- 
 Frontend: Mina 
 Backend: Amirah, Qis
 -->
<?php
session_start();

// Check if staff is logged in
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}

include '../config/db_connect.php';

$staff_id = $_SESSION['staff_id'];
$success_message = '';
$error_message = '';

// Fetch current staff data
$query = "SELECT staffID, staffName, staffEmail, staffUsername FROM staff WHERE staffID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate required fields
    if (empty($name) || empty($email) || empty($username)) {
        $error_message = "Name, email, and username are required.";
    } else {
        // Check if email already exists (for other users)
        $check_email = "SELECT staffID FROM staff WHERE staffEmail = ? AND staffID != ?";
        $stmt = $conn->prepare($check_email);
        $stmt->bind_param("si", $email, $staff_id);
        $stmt->execute();
        $email_result = $stmt->get_result();
        
        if ($email_result->num_rows > 0) {
            $error_message = "Email is already used by another account.";
        } else {
            // Check if username already exists (for other users)
            $check_username = "SELECT staffID FROM staff WHERE staffUsername = ? AND staffID != ?";
            $stmt = $conn->prepare($check_username);
            $stmt->bind_param("si", $username, $staff_id);
            $stmt->execute();
            $username_result = $stmt->get_result();
            
            if ($username_result->num_rows > 0) {
                $error_message = "Username is already taken.";
            } else {
                // Update profile (without password)
                if (empty($current_password) && empty($new_password)) {
                    $update_query = "UPDATE staff SET staffName = ?, staffEmail = ?, staffUsername = ? WHERE staffID = ?";
                    $stmt = $conn->prepare($update_query);
                    $stmt->bind_param("sssi", $name, $email, $username, $staff_id);
                    
                    if ($stmt->execute()) {
                        $success_message = "Profile updated successfully!";
                        
                        // Update session variables
                        $_SESSION['staff_name'] = $name;
                        $_SESSION['staff_email'] = $email;
                        
                        // Refresh staff data
                        $staff['staffName'] = $name;
                        $staff['staffEmail'] = $email;
                        $staff['staffUsername'] = $username;
                    } else {
                        $error_message = "Failed to update profile.";
                    }
                } else {
                    // Update profile with password change
                    if (empty($current_password)) {
                        $error_message = "Please enter your current password to change it.";
                    } elseif (empty($new_password)) {
                        $error_message = "Please enter a new password.";
                    } elseif ($new_password !== $confirm_password) {
                        $error_message = "New passwords do not match.";
                    } elseif (strlen($new_password) < 6) {
                        $error_message = "New password must be at least 6 characters.";
                    } else {
                        // Verify current password
                        $verify_query = "SELECT staffPassword FROM staff WHERE staffID = ?";
                        $stmt = $conn->prepare($verify_query);
                        $stmt->bind_param("i", $staff_id);
                        $stmt->execute();
                        $password_result = $stmt->get_result();
                        $current_data = $password_result->fetch_assoc();
                        
                        // Check if current password matches (using MD5 as per your system)
                        if (md5($current_password) !== $current_data['staffPassword']) {
                            $error_message = "Current password is incorrect.";
                        } else {
                            // Update profile with new password
                            $new_password_hash = md5($new_password);
                            $update_query = "UPDATE staff SET staffName = ?, staffEmail = ?, staffUsername = ?, staffPassword = ? WHERE staffID = ?";
                            $stmt = $conn->prepare($update_query);
                            $stmt->bind_param("ssssi", $name, $email, $username, $new_password_hash, $staff_id);
                            
                            if ($stmt->execute()) {
                                $success_message = "Profile and password updated successfully!";
                                
                                // Update session variables
                                $_SESSION['staff_name'] = $name;
                                $_SESSION['staff_email'] = $email;
                                
                                // Refresh staff data
                                $staff['staffName'] = $name;
                                $staff['staffEmail'] = $email;
                                $staff['staffUsername'] = $username;
                            } else {
                                $error_message = "Failed to update profile.";
                            }
                        }
                    }
                }
            }
        }
        $stmt->close();
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Profile</title>
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
                    <li><a href="menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li><a href="order_management.php"><span class="material-symbols-outlined">order_approve</span> Orders</a></li>
                    <li><a href="report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
                    <li class="nav-divider"></li>
                    <li class="active"><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="../logout.php" class="logout-link"><span class="material-symbols-outlined">logout</span> Log Out</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Main content area -->
    <div class="main-content profile-content">
        <div class="header">
            <div class="title">
                <h2>Profile</h2>
                <p>Manage your profile information</p>
            </div>
        </div>
        
        <div class="staff-profile-page">
            <div class="profile-card">
                <h2>My Profile</h2>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <span class="material-symbols-outlined">check_circle</span>
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error">
                        <span class="material-symbols-outlined">error</span>
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-section">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="input-field" value="<?php echo htmlspecialchars($staff['staffName']); ?>" required>
                    </div>

                    <div class="form-section">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="input-field" value="<?php echo htmlspecialchars($staff['staffEmail']); ?>" required>
                    </div>

                    <div class="form-section">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="input-field" value="<?php echo htmlspecialchars($staff['staffUsername']); ?>" required>
                    </div>

                    <hr class="form-divider">
                    <p class="form-note">Leave password fields empty if you don't want to change it.</p>

                    <div class="form-section">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="input-field" placeholder="Enter current password">
                    </div>

                    <div class="form-section">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="input-field" placeholder="Enter new password">
                    </div>

                    <div class="form-section">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="input-field" placeholder="Confirm new password">
                    </div>

                    <button type="submit" class="btn-update">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>