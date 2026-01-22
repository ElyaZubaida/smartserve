<!-- 
 Frontend: Mina 
 Backend: Qis 
 -->

 <?php
// ================= BACKEND START ================= //
session_start();
include 'config/db_connect.php';

// 1. Check Login
if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$success_message = '';
$error_message = '';

// 2. Fetch Data
// Matches your DB columns: student_name, student_email, student_username, student_password
$query = "SELECT * FROM students WHERE student_ID = ? AND is_deleted = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// 3. Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['studentName']);
    $email = trim($_POST['studentEmail']);
    $username = trim($_POST['studentUsername']);
    
    $current_password = $_POST['currentPassword'];
    $new_password = $_POST['newPassword'];
    $confirm_password = $_POST['confirmPassword'];

    // Basic Validation
    if (empty($name) || empty($email) || empty($username)) {
        $error_message = "Name, Email, and Username are required.";
    } else {
        // --- UNIQUENESS CHECK (Email) ---
        $check_email = "SELECT student_ID FROM students WHERE student_email = ? AND student_ID != ?";
        $stmt = $conn->prepare($check_email);
        $stmt->bind_param("si", $email, $student_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error_message = "Email is already taken.";
            $stmt->close();
        } else {
            $stmt->close();
            
            // --- UNIQUENESS CHECK (Username) ---
            $check_user = "SELECT student_ID FROM students WHERE student_username = ? AND student_ID != ?";
            $stmt = $conn->prepare($check_user);
            $stmt->bind_param("si", $username, $student_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $error_message = "Username is already taken.";
                $stmt->close();
            } else {
                $stmt->close();

                // --- UPDATE LOGIC ---
                // Scenario A: Update Info Only (No Password)
                if (empty($current_password) && empty($new_password)) {
                    $update_query = "UPDATE students SET student_name = ?, student_email = ?, student_username = ?, updated_at = NOW() WHERE student_ID = ?";
                    $stmt = $conn->prepare($update_query);
                    $stmt->bind_param("sssi", $name, $email, $username, $student_id);
                    
                    if ($stmt->execute()) {
                        $success_message = "Profile updated successfully!";
                        $_SESSION['student_name'] = $name;
                        $_SESSION['student_email'] = $email;
                        // Refresh data
                        $student['student_name'] = $name;
                        $student['student_email'] = $email;
                        $student['student_username'] = $username;
                    } else {
                        $error_message = "Update failed.";
                    }
                    $stmt->close();
                } 
                // Scenario B: Update Password too
                else {
                    if (empty($current_password)) {
                        $error_message = "Enter current password to change it.";
                    } elseif ($new_password !== $confirm_password) {
                        $error_message = "New passwords do not match.";
                    } else {
                        // HYBRID CHECK (Handles Aleesya's Bcrypt & Others' MD5)
                        $db_pass = $student['student_password'];
                        $is_md5 = (md5($current_password) === $db_pass);
                        $is_bcrypt = password_verify($current_password, $db_pass);

                        if (!$is_md5 && !$is_bcrypt) {
                            $error_message = "Current password is incorrect.";
                        } else {
                            $new_password_hash = md5($new_password); // Save as MD5
                            
                            $update_query = "UPDATE students SET student_name = ?, student_email = ?, student_username = ?, student_password = ?, updated_at = NOW() WHERE student_ID = ?";
                            $stmt = $conn->prepare($update_query);
                            $stmt->bind_param("ssssi", $name, $email, $username, $new_password_hash, $student_id);
                            
                            if ($stmt->execute()) {
                                $success_message = "Password updated successfully!";
                                $_SESSION['student_name'] = $name;
                                $_SESSION['student_email'] = $email;
                                $student['student_name'] = $name;
                                $student['student_email'] = $email;
                                $student['student_username'] = $username;
                                $student['student_password'] = $new_password_hash;
                            } else {
                                $error_message = "Update failed.";
                            }
                            $stmt->close();
                        }
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Student Profile</title>

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">

    <style>
        .msg-box { padding: 12px; margin-bottom: 20px; border-radius: 5px; font-size: 0.9em; font-weight: 500; }
        .msg-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .msg-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .pass-label { font-size: 0.85em; color: #666; margin-top: 10px; display: block; margin-bottom: 5px; font-weight: bold;}
    </style>
</head>

<body class="staff-style-student-page">
<!-- ================= NAVIGATION BAR ================= -->
<header>
    <div class="menubar">
        <div class="logo">
            <img src="img/logo.png" alt="Smart Serve Logo">
        </div>

        <nav>
            <ul>
                <li><a href="menu.php"><span class="material-symbols-outlined">home</span> Home</a></li>
                    <li><a href="ai.php"><span class="material-symbols-outlined">psychology</span> Food Recommendation</a></li>
                    <li><a href="myorders.php"><span class="material-symbols-outlined">receipt_long</span> Orders</a></li>
                    <li><a href="cart.php"><span class="material-symbols-outlined">shopping_cart</span> Cart</a></li>
                    <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="logout.php"><span class="material-symbols-outlined">logout</span></a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- ================= PROFILE CARD ================= -->
<div class="profile-card">
    <h2>Profile</h2>

    <?php if ($success_message): ?>
        <div class="msg-box msg-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="msg-box msg-error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-group">
            <label><span class="material-symbols-outlined">person</span> Full Name</label>
            <input type="text" name="studentName" class="input-field" placeholder="Enter your full name" value="<?php echo htmlspecialchars($student['student_name']); ?>" required>
        </div>
        
        <div class="input-group">
            <label><span class="material-symbols-outlined">mail</span> Email Address</label>
            <input type="email" name="studentEmail" class="input-field" placeholder="name@example.com" value="<?php echo htmlspecialchars($student['student_email']); ?>" required>
        </div>
        
        <div class="input-group">
            <label><span class="material-symbols-outlined">badge</span> Username</label>
            <input type="text" name="studentUsername" class="input-field" placeholder="Choose a unique username" value="<?php echo htmlspecialchars($student['student_username']); ?>" required>
        </div>

        <hr class="form-divider">
        <h3 class="form-subtitle">Security Settings</h3>

        <div class="input-group">
            <label><span class="material-symbols-outlined">lock_open</span> Current Password</label>
            <input type="password" name="currentPassword" class="input-field" placeholder="Verify current password">
        </div>
        
        <div class="input-group">
            <label><span class="material-symbols-outlined">lock</span> New Password</label>
            <input type="password" name="newPassword" class="input-field" placeholder="Enter new password">
        </div>
        
        <div class="input-group">
            <label><span class="material-symbols-outlined">verified_user</span> Confirm New Password</label>
            <input type="password" name="confirmPassword" class="input-field" placeholder="Repeat new password">
        </div>

        <button type="submit" class="btn-update">Update Profile</button>
    </form>
</div>

</body>
</html>

