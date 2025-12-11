<!-- 
 Frontend: Elya 
 Backend: ? 
 -->
<?php
session_start();

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch username and password from POST data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Example of validating against static data (replace with database logic later)
    $valid_users = [
        'student1' => 'password123',  // Example student
        'staff1' => 'staffpassword',  // Example staff
    ];

    // Check if the username exists and the password matches
    if (isset($valid_users[$username]) && $valid_users[$username] == $password) {
        // Store the username and role in session
        $_SESSION['username'] = $username;
        $_SESSION['role'] = (strpos($username, 'student') !== false) ? 'student' : 'staff';

        // Redirect based on the user's role
        if ($_SESSION['role'] == 'student') {
            header('Location: menu.php'); // Redirect to student dashboard
        } else {
            header('Location: staff/dashboard.php'); // Redirect to staff dashboard
        }
        exit();
    } else {
        // If login fails, show an error message
        $error_message = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Serve - Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style.css"> <!-- Link to your external CSS -->
</head>
<body class="login-page">

    <!-- Smart Serve Logo -->
    <header>
        <div class="logo">
            <img src="logo.png" alt="Smart Serve Logo"> <!-- Replace with your logo image -->
            <h1>SmartServe</h1>
        </div>
        <hr> <!-- Separator line -->
    </header>

    <!-- Main Container for the Login Page -->
    <div class="container">
        
        <!-- Left side: Login Form -->
        <div class="login-container">
            <div class="login-form">
                <h2>Log In</h2>

                <!-- Display error message if login fails -->
                <?php if (isset($error_message)): ?>
                    <p class="error-message"><?php echo $error_message; ?></p>
                <?php endif; ?>

                <!-- Login Form Fields -->
                <form action="login.php" method="POST">
                    <div class="role-selection">
                        <label>
                            <input type="radio" name="role" value="student" id="student" checked>
                            Student
                        </label>
                        <label>
                            <input type="radio" name="role" value="staff" id="staff">
                            Staff
                        </label>
                    </div>

                    <div class="input-field">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="input-field">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <!-- Forgot Password Link (aligned right) -->
                    <div class="forgot-password">
                        <a href="forgotpass.php">Forgot Password?</a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="login-btn">Log In</button>
                </form>

                <!-- Sign Up Link (centered) -->
                <div class="sign-up">
                    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
                </div>
            </div>
        </div>

        <!-- Right side: Welcome Section -->
        <div class="welcome-container">
            <h2>Welcome to SmartServe</h2>
            <hr> 
            <p>Student Canteen Food Ordering</p>
            <p>Ordering System</p>
        </div>

    </div>

</body>
</html>
