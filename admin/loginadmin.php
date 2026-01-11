<!-- 
 Frontend: Elya 
 Backend: Qis
 -->

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $valid_admins = [
        'admin1' => 'adminpass123',
    ];

    if (isset($valid_admins[$username]) && $valid_admins[$username] == $password) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'admin'; 

        header('Location: a_dashboard.php'); 
        exit();
    } else {
        $error_message = 'Invalid Admin credentials';
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
                        <span class="material-icons">admin_panel_settings</span>
                        <input type="text" name="username" placeholder="Admin Username" required>
                    </div>

                    <div class="styled-input">
                        <span class="material-icons">lock</span>
                        <input type="password" name="password" placeholder="Password" required>
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
            <p>Authorized Users Only</p>
        </div>
    </div>
</body>
</html>