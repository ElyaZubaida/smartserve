<!-- 
 Frontend: Insyirah 
 Backend: Qis 
 -->
<?php
session_start();
include 'config/db_connect.php';

$message = '';
$msg_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Validation: Check Empty Fields
    if (empty($name) || empty($email) || empty($username) || empty($password)) {
        $message = "All fields are required.";
        $msg_type = "error";
    } 
    // 2. Validation: Password Length
    elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $msg_type = "error";
    } 
    else {
        // 3. Validation: Check Duplicate Email
        $check_email = $conn->prepare("SELECT student_ID FROM students WHERE student_email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            $message = "Email is already registered.";
            $msg_type = "error";
        } else {
            // 4. Validation: Check Duplicate Username
            $check_user = $conn->prepare("SELECT student_ID FROM students WHERE student_username = ?");
            $check_user->bind_param("s", $username);
            $check_user->execute();
            $check_user->store_result();

            if ($check_user->num_rows > 0) {
                $message = "Username is already taken.";
                $msg_type = "error";
            } else {
                // 5. Success: Insert New User
                // We use MD5 for password to match your system
                $hashed_password = md5($password);
                $admin_id = 1; // Default admin ID assignment

                $insert = $conn->prepare("INSERT INTO students (admin_ID, student_name, student_email, student_username, student_password, created_at, is_deleted) VALUES (?, ?, ?, ?, ?, NOW(), 0)");
                $insert->bind_param("issss", $admin_id, $name, $email, $username, $hashed_password);

                if ($insert->execute()) {
                    $message = "Account created successfully! Redirecting to login...";
                    $msg_type = "success";
                    
                    // Optional: Auto redirect after 2 seconds
                    header("refresh:2;url=login.php");
                } else {
                    $message = "Registration failed. Please try again.";
                    $msg_type = "error";
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
    <title>Smart Serve - Signup</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style.css">
    
    <style>
        .msg-box { padding: 12px; margin-bottom: 20px; border-radius: 5px; font-size: 0.9em; text-align: center; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body class="signup-page">
    <!-- Header -->
     <header>
        <div class="logo">
            <img src="img/logo.png" alt="Smart Serve Logo">
            <h1>Smart<span>Serve</span></h1>
        </div>
        <hr>
    </header>


    <!-- Sign Up Form -->
    <div class="signup-container">
        <h2>SIGN UP</h2>

        <?php if ($message): ?>
            <div class="msg-box <?php echo $msg_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="signup-field">
                <input type="text" name="name" placeholder="NAME" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
            </div>
            <div class="signup-field">
                <input type="email" name="email" placeholder="EMAIL" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            <div class="signup-field">
                <input type="text" name="username" placeholder="USERNAME" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>
            <div class="signup-field">
                <input type="password" name="password" placeholder="PASSWORD" required>
            </div>
            <button type="submit" class="signup-btn">SIGN UP</button>
        </form>
        
        <p style="text-align:center; margin-top:15px; font-size: 0.9em;">
            Already have an account? <a href="login.php" style="color:#2c3e50; font-weight:bold;">Login here</a>
        </p>
    </div>
</body>
</html>