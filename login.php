<!-- 
 Frontend: Elya 
 Backend: Aleesya, Amirah 
 -->
<?php
    session_start();
    include 'config/db_connect.php';

    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $role = $_POST['role']; // 'student' or 'staff'

        if (empty($username) || empty($password)) {
            $error_message = 'Please enter both username and password';
        } else {
            // ================= STUDENT LOGIN ================= //
           if ($role == 'student') {
                // FIXED: Changed 'student_name' to 'student_username'
                // FIXED: Added 'AND is_deleted = 0'
                $query = "SELECT student_ID, student_name, student_email, student_password FROM students WHERE (student_email = ? OR student_username = ?) AND is_deleted = 0";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $username, $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    
                    // FIXED: Hybrid Password Check (MD5 for Alex, Bcrypt for Aleesya)
                    $db_pass = $user['student_password'];
                    $is_md5 = (md5($password) === $db_pass);
                    $is_bcrypt = password_verify($password, $db_pass);

                    if ($is_md5 || $is_bcrypt) {
                        $_SESSION['student_id'] = $user['student_ID'];
                        $_SESSION['student_name'] = $user['student_name'];
                        $_SESSION['student_email'] = $user['student_email'];
                        $_SESSION['role'] = 'student';

                        header('Location: menu.php');
                        exit();
                    } else {
                        $error_message = 'Invalid username or password';
                    }
                } else {
                    $error_message = 'Invalid username or password';
                }
            }
            // ================= STAFF LOGIN (FIXED) ================= //
            else {
                // Check email OR username, AND ensure account is not deleted
                $query = "SELECT staffID, staffName, staffEmail, staffPassword FROM staff WHERE (staffEmail = ? OR staffUsername = ?) AND is_deleted = 0";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $username, $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    
                    // --- HYBRID PASSWORD CHECK START ---
                    $db_password = $user['staffPassword'];
                    
                    // 1. Check if password matches Plain Text (Old users like Sarah)
                    $is_plain = ($password === $db_password);
                    
                    // 2. Check if password matches MD5 (New users like Mike)
                    $is_md5 = (md5($password) === $db_password);

                    // If EITHER is true, log them in
                    if ($is_plain || $is_md5) {
                        $_SESSION['staff_id'] = $user['staffID'];
                        $_SESSION['staffID'] = $user['staffID']; // Setting both keys to be safe
                        $_SESSION['staff_name'] = $user['staffName'];
                        $_SESSION['staff_email'] = $user['staffEmail'];
                        $_SESSION['role'] = 'staff';

                        header('Location: staff/dashboard.php'); // Redirect to dashboard
                        exit();
                    } else {
                        $error_message = 'Invalid username or password';
                    }
                    // --- HYBRID PASSWORD CHECK END ---
                } else {
                    $error_message = 'Invalid username or password';
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
    <title>Smart Serve - Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

    <header>
        <div class="logo">
            <img src="img/logo.png" alt="Smart Serve Logo">
            <h1>Smart<span>Serve</span></h1>
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
    const forgotPasswordLink = document.querySelector('.forgot-password');
    const usernameInput = document.getElementById('username');

    function toggleSignup() {
        if (staffRadio.checked) {
            signupSection.style.display = 'none';    // Hide Sign Up for Staff
            forgotPasswordLink.style.display = 'none'; // Hide Forgot Password for Staff
            usernameInput.placeholder = 'Email or Username';
        } else {
            signupSection.style.display = 'block';   // Show for Students
            forgotPasswordLink.style.display = 'block'; 
            usernameInput.placeholder = 'Username';
        }
    }

    studentRadio.addEventListener('change', toggleSignup);
    staffRadio.addEventListener('change', toggleSignup);

    toggleSignup(); 
</script>
</html>
