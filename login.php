<!-- 
 Frontend: Elya 
 Backend: Qis 
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

    <header>
        <div class="logo">
            <img src="img/logo.png" alt="Smart Serve Logo">
            <h1>SmartServe</h1>
        </div>
        <hr>
    </header>

    <div class="container">
        <div class="login-container">
            <div class="login-form">
                <h2>Log In</h2>

                <?php if (isset($error_message)): ?>
                    <p class="error-message"><span class="material-icons">error_outline</span> <?php echo $error_message; ?></p>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="role-selection">
                        <label class="role-pill">
                            <input type="radio" name="role" value="student" checked>
                            <span>Student</span>
                        </label>
                        <label class="role-pill">
                            <input type="radio" name="role" value="staff">
                            <span>Staff</span>
                        </label>
                    </div>

                    <div class="styled-input">
                        <span class="material-icons">person</span>
                        <input type="text" id="username" name="username" placeholder="Username" required>
                    </div>

                    <div class="styled-input">
                        <span class="material-icons">lock</span>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                    
                    <div class="forgot-password">
                        <a href="forgotpass.php">Forgot Password?</a>
                    </div>

                    <button type="submit" class="login-btn">Log In</button>
                </form>

                <div class="sign-up" id="signup-section">
                    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
                </div>
            </div>
        </div>

        <div class="welcome-container">
            <img src="img/logo.png" alt="Smart Serve Logo" style="width: 100px; height: 100px;">
            <h2>Welcome to <br>SmartServe</h2>
            <div class="leaf-divider">
                <span></span>
                <span class="material-icons">eco</span>
                <span></span>
            </div>
            <p>Student Canteen Food Ordering System</p>
        </div>
    </div>
</body>
<script>
    const studentRadio = document.querySelector('input[value="student"]');
    const staffRadio = document.querySelector('input[value="staff"]');
    const signupSection = document.getElementById('signup-section');
    // Get the Forgot Password container
    const forgotPasswordLink = document.querySelector('.forgot-password');

    function toggleSignup() {
        if (staffRadio.checked) {
            signupSection.style.display = 'none';    // Hide Sign Up for Staff
            forgotPasswordLink.style.display = 'none'; // Hide Forgot Password for Staff
        } else {
            signupSection.style.display = 'block';   // Show for Students
            forgotPasswordLink.style.display = 'block'; 
        }
    }

    studentRadio.addEventListener('change', toggleSignup);
    staffRadio.addEventListener('change', toggleSignup);

    toggleSignup(); // Run on load
</script>
</html>

