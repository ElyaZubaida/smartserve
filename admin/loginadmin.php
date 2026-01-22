<!-- 
 Frontend: Elya 
 Backend: Qis
 -->
<?php
session_start();


// If already logged in as admin, redirect to dashboard
if (isset($_SESSION['admin_id']) && $_SESSION['role'] === 'admin') {
    header('Location: a_dashboard.php');
    exit();
}

include '../config/db_connect.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password';
    } else {
        // Query for admin login - check email OR username
        $query = "SELECT admin_ID, admin_name, admin_email, admin_username, admin_password FROM admins WHERE admin_email = ? OR admin_username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            // Verify password using MD5 (same as staff)
            if (md5($password) === $admin['admin_password']) {
                // Set session variables
                $_SESSION['admin_id'] = $admin['admin_ID'];
                $_SESSION['admin_name'] = $admin['admin_name'];
                $_SESSION['admin_email'] = $admin['admin_email'];
                $_SESSION['admin_username'] = $admin['admin_username'];
                $_SESSION['role'] = 'admin';

                header('Location: a_dashboard.php');
                exit();
            } else {
                $error_message = 'Invalid username or password';
            }
        } else {
            $error_message = 'Invalid username or password';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Serve - Admin Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="login-page">
    <header>
        <div class="logo">
            <img src="../img/logo.png" alt="Smart Serve Logo">
            <h1>Smart<span>Serve</span> <small style="font-size: 12px; color: #a5d6a7;">Admin Portal</small></h1>
        </div>
        <hr>
    </header>

    <div class="container">
        <div class="login-container">
            <div class="login-form">
                <h2>Admin Log In</h2>

                <?php if (isset($error_message)): ?>
                    <p class="error-message"><span class="material-icons">error_outline</span> <?php echo $error_message; ?></p>
                <?php endif; ?>

                <form action="loginadmin.php" method="POST">
                    <div class="styled-input">
                        <span class="material-icons">person</span>
                        <input type="text" name="username" placeholder="Email or Username" required>
                    </div>

                    <div class="styled-input">
                        <span class="material-icons">lock</span>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="forgot-password">
                        <a href="a_forgotpass.php">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="login-btn">Log In</button>
                </form>
            </div>
        </div>

        <div class="welcome-container">
            <img src="../img/logo.png" alt="Smart Serve Logo" style="width: 100px; height: 100px;">
            <h2>Welcome to <br>SmartServe</h2>
            <div class="leaf-divider">
                <span></span>
                <span class="material-icons">eco</span>
                <span></span>
            </div>
            <p>Admin Portal - Authorized Users Only</p>
        </div>
    </div>
</body>
</html>