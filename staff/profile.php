<?php
// Frontend: Mina 
// Backend: Amirah, Qis

session_start();

// 1. Check Login
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}

include '../config/db_connect.php';

$staff_id = $_SESSION['staff_id'];
$success_message = '';
$error_message = '';

// 2. Fetch Current Staff Data
// FIX: We fetch 'staffPassword' here so we can check it later
// FIX: We check 'is_deleted = 0'
$query = "SELECT staffID, staffName, staffEmail, staffUsername, staffPassword FROM staff WHERE staffID = ? AND is_deleted = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
$stmt->close();

// If staff not found, logout
if (!$staff) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

// 3. Handle Form Submission
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
        // Check uniqueness (Email)
        $check_email = "SELECT staffID FROM staff WHERE staffEmail = ? AND staffID != ?";
        $stmt = $conn->prepare($check_email);
        $stmt->bind_param("si", $email, $staff_id);
        $stmt->execute();
        $email_result = $stmt->get_result();
        
        if ($email_result->num_rows > 0) {
            $error_message = "Email is already used by another account.";
        } else {
            // Check uniqueness (Username)
            $check_username = "SELECT staffID FROM staff WHERE staffUsername = ? AND staffID != ?";
            $stmt = $conn->prepare($check_username);
            $stmt->bind_param("si", $username, $staff_id);
            $stmt->execute();
            $username_result = $stmt->get_result();
            
            if ($username_result->num_rows > 0) {
                $error_message = "Username is already taken.";
            } else {
                
                // --- SCENARIO A: Update Info Only (No Password Change) ---
                if (empty($current_password) && empty($new_password)) {
                    
                    // FIX: Added 'updated_at = NOW()'
                    $update_query = "UPDATE staff SET staffName = ?, staffEmail = ?, staffUsername = ?, updated_at = NOW() WHERE staffID = ?";
                    $stmt = $conn->prepare($update_query);
                    $stmt->bind_param("sssi", $name, $email, $username, $staff_id);
                    
                    if ($stmt->execute()) {
                        $success_message = "Profile updated successfully!";
                        
                        // Update Session & Local Variable
                        $_SESSION['staff_name'] = $name;
                        $_SESSION['staff_email'] = $email;
                        $staff['staffName'] = $name;
                        $staff['staffEmail'] = $email;
                        $staff['staffUsername'] = $username;
                    } else {
                        $error_message = "Failed to update profile.";
                    }
                } 
                // --- SCENARIO B: Update Info AND Password ---
                else {
                    if (empty($current_password)) {
                        $error_message = "Please enter your current password to change it.";
                    } elseif (empty($new_password)) {
                        $error_message = "Please enter a new password.";
                    } elseif ($new_password !== $confirm_password) {
                        $error_message = "New passwords do not match.";
                    } elseif (strlen($new_password) < 6) {
                        $error_message = "New password must be at least 6 characters.";
                    } else {
                        
                        // --- THE HYBRID PASSWORD CHECK (Crucial Fix) ---
                        $db_pass = $staff['staffPassword'];
                        
                        // Check 1: Is it Plain Text? (e.g., 'mike123')
                        $is_plain = ($current_password === $db_pass);
                        // Check 2: Is it MD5? (e.g., 'e10adc3949...')
                        $is_md5 = (md5($current_password) === $db_pass);
                        
                        if (!$is_plain && !$is_md5) {
                            $error_message = "Current password is incorrect.";
                        } else {
                            // Password correct, proceed to update
                            $new_password_hash = md5($new_password);
                            
                            // FIX: Added 'updated_at = NOW()'
                            $update_query = "UPDATE staff SET staffName = ?, staffEmail = ?, staffUsername = ?, staffPassword = ?, updated_at = NOW() WHERE staffID = ?";
                            $stmt = $conn->prepare($update_query);
                            $stmt->bind_param("ssssi", $name, $email, $username, $new_password_hash, $staff_id);
                            
                            if ($stmt->execute()) {
                                $success_message = "Profile and password updated successfully!";
                                
                                $_SESSION['staff_name'] = $name;
                                $_SESSION['staff_email'] = $email;
                                $staff['staffName'] = $name;
                                $staff['staffEmail'] = $email;
                                $staff['staffUsername'] = $username;
                                $staff['staffPassword'] = $new_password_hash; // Update local variable
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
    
    <style>
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; display: flex; align-items: center; gap: 10px; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-error { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
    </style>
</head>
<body>

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