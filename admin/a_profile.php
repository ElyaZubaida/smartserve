<!-- 
 Frontend: Mina 
 Backend: Amirah, Qis
 -->
<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginadmin.php");
    exit;
}

include '../config/db_connect.php';

$admin_id = (int) $_SESSION['admin_id'];
$success_message = '';
$error_message = '';

// Fetch current admin data
$query = "SELECT admin_ID, admin_name, admin_email, admin_username FROM admins WHERE admin_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if (!$admin) {
    // In case session has an invalid admin_id
    session_destroy();
    header("Location: ../login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if ($name === '' || $email === '' || $username === '') {
        $error_message = "Name, email, and username are required.";
    } else {
        // Check if email already exists (other admins)
        $check_email = "SELECT admin_ID FROM admins WHERE admin_email = ? AND admin_ID != ?";
        $stmt = $conn->prepare($check_email);
        $stmt->bind_param("si", $email, $admin_id);
        $stmt->execute();
        $email_result = $stmt->get_result();
        $stmt->close();

        if ($email_result->num_rows > 0) {
            $error_message = "Email is already used by another account.";
        } else {
            // Check if username already exists (other admins)
            $check_username = "SELECT admin_ID FROM admins WHERE admin_username = ? AND admin_ID != ?";
            $stmt = $conn->prepare($check_username);
            $stmt->bind_param("si", $username, $admin_id);
            $stmt->execute();
            $username_result = $stmt->get_result();
            $stmt->close();

            if ($username_result->num_rows > 0) {
                $error_message = "Username is already taken.";
            } else {
                $wants_password_change = ($current_password !== '' || $new_password !== '' || $confirm_password !== '');

                // If no password change, update only profile details
                if (!$wants_password_change) {
                    $update_query = "UPDATE admins SET admin_name = ?, admin_email = ?, admin_username = ? WHERE admin_ID = ?";
                    $stmt = $conn->prepare($update_query);
                    $stmt->bind_param("sssi", $name, $email, $username, $admin_id);

                    if ($stmt->execute()) {
                        $success_message = "Profile updated successfully!";

                        // Update session variables (optional)
                        $_SESSION['admin_name'] = $name;
                        $_SESSION['admin_email'] = $email;

                        // Refresh admin data for display
                        $admin['admin_name'] = $name;
                        $admin['admin_email'] = $email;
                        $admin['admin_username'] = $username;
                    } else {
                        $error_message = "Failed to update profile.";
                    }
                    $stmt->close();
                } else {
                    // Password change flow
                    if ($current_password === '') {
                        $error_message = "Please enter your current password to change it.";
                    } elseif ($new_password === '') {
                        $error_message = "Please enter a new password.";
                    } elseif ($new_password !== $confirm_password) {
                        $error_message = "New passwords do not match.";
                    } elseif (strlen($new_password) < 6) {
                        $error_message = "New password must be at least 6 characters.";
                    } else {
                        // Verify current password
                        // NOTE: Adjust column name if yours is different (admin_password)
                        $verify_query = "SELECT admin_password FROM admins WHERE admin_ID = ?";
                        $stmt = $conn->prepare($verify_query);
                        $stmt->bind_param("i", $admin_id);
                        $stmt->execute();
                        $pass_result = $stmt->get_result();
                        $current_data = $pass_result->fetch_assoc();
                        $stmt->close();

                        // If your system uses MD5 (not recommended, but common in student projects)
                        if (!$current_data || md5($current_password) !== $current_data['admin_password']) {
                            $error_message = "Current password is incorrect.";
                        } else {
                            $new_hash = md5($new_password);

                            $update_query = "UPDATE admins 
                                            SET admin_name = ?, admin_email = ?, admin_username = ?, admin_password = ?
                                            WHERE admin_ID = ?";
                            $stmt = $conn->prepare($update_query);
                            $stmt->bind_param("ssssi", $name, $email, $username, $new_hash, $admin_id);

                            if ($stmt->execute()) {
                                $success_message = "Profile and password updated successfully!";

                                // Update session variables (optional)
                                $_SESSION['admin_name'] = $name;
                                $_SESSION['admin_email'] = $email;

                                // Refresh display data
                                $admin['admin_name'] = $name;
                                $admin['admin_email'] = $email;
                                $admin['admin_username'] = $username;
                            } else {
                                $error_message = "Failed to update profile.";
                            }
                            $stmt->close();
                        }
                    }
                }
            }
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Admin Profile</title>
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
                    <li><a href="a_dashboard.php"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                    <li><a href="a_menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li><a href="a_order_management.php"><span class="material-symbols-outlined">order_approve</span> Orders</a></li>
                    <li><a href="user_management.php"><span class="material-symbols-outlined">manage_accounts</span> User Management</a></li>
                    <li><a href="a_report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
                    <li class="nav-divider"></li>
                    <li class="active"><a href="a_profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="logoutadmin.php" class="logout-link"><span class="material-symbols-outlined">logout</span> Log Out</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Main content area -->
    <div class="main-content profile-content">
        <div class="header">
            <div class="title">
                <h2>Admin Profile</h2>
                <p>Manage your profile information</p>
            </div>
        </div>

        <div class="staff-profile-page">
            <div class="profile-card">
                <h2>My Profile</h2>

                <?php if ($success_message !== ''): ?>
                    <div class="alert alert-success">
                        <span class="material-symbols-outlined">check_circle</span>
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error_message !== ''): ?>
                    <div class="alert alert-error">
                        <span class="material-symbols-outlined">error</span>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-section">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="input-field"
                               value="<?php echo htmlspecialchars($admin['admin_name']); ?>" required>
                    </div>

                    <div class="form-section">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="input-field"
                               value="<?php echo htmlspecialchars($admin['admin_email']); ?>" required>
                    </div>

                    <div class="form-section">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="input-field"
                               value="<?php echo htmlspecialchars($admin['admin_username']); ?>" required>
                    </div>

                    <hr class="form-divider">
                    <p class="form-note">Leave password fields empty if you don't want to change it.</p>

                    <div class="form-section">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="input-field"
                               placeholder="Enter current password">
                    </div>

                    <div class="form-section">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="input-field"
                               placeholder="Enter new password">
                    </div>

                    <div class="form-section">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="input-field"
                               placeholder="Confirm new password">
                    </div>

                    <button type="submit" class="btn-update">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
