<!-- 
 Frontend: Mina 
 Backend: Qis
-->
 <!-- BACKEND STARTED -->
 <?php
    session_start();
    include 'config/db_connect.php';

    $message = '';
    $msg_type = ''; 

    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']); 
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if (empty($username) || empty($email) || empty($new_pass) || empty($confirm_pass)) {
            $message = "All fields are required.";
            $msg_type = "error";
        } 
        elseif ($new_pass !== $confirm_pass) {
            $message = "New passwords do not match.";
            $msg_type = "error";
        } 
        elseif (strlen($new_pass) < 6) {
            $message = "Password must be at least 6 characters.";
            $msg_type = "error";
        } 
        else {
            // Try student first
            $stmt = $conn->prepare(
                "SELECT student_ID FROM students 
                WHERE student_username = ? AND student_email = ? AND is_deleted = 0"
            );
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $new_hash = md5($new_pass);
                $update = $conn->prepare(
                    "UPDATE students SET student_password = ?, updated_at = NOW() 
                    WHERE student_username = ?"
                );
                $update->bind_param("ss", $new_hash, $username);
                $update->execute();

                $message = "Password reset successfully! You can login now.";
                $msg_type = "success";
            } else {
                // Try staff
                $stmt = $conn->prepare(
                    "SELECT staffID FROM staff 
                    WHERE staffUsername = ? AND staffEmail = ? AND is_deleted = 0"
                );
                $stmt->bind_param("ss", $username, $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $new_hash = md5($new_pass);
                    $update = $conn->prepare(
                        "UPDATE staff SET staffPassword = ?, updated_at = NOW() 
                        WHERE staffUsername = ?"
                    );
                    $update->bind_param("ss", $new_hash, $username);
                    $update->execute();

                    $message = "Password reset successfully! You can login now.";
                    $msg_type = "success";
                } else {
                    $message = "Username and Email do not match.";
                    $msg_type = "error";
                }
            }
        }
    }
?>
<!-- BACKEND ENDED -->
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Forgot Password</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="style.css">
</head>
<body>
     <header>
        <div class="logo">
            <img src="img/logo.png" alt="Smart Serve Logo">
            <h1>Smart<span>Serve</span></h1>
        </div>
        <hr>
    </header>

<div class="forgot-password-page">
    
    <div class="forgot-password-card">
        <h2>Reset Password</h2>
        
        <?php if ($message): ?>
            <div class="msg-box <?php echo $msg_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="username" class="input-field" placeholder="Username" required>
            
            <input type="email" name="email" class="input-field" placeholder="Email Address" required>

            <input type="password" name="new_password" class="input-field" placeholder="New Password" required>
            <input type="password" name="confirm_password" class="input-field" placeholder="Re-enter Password" required>

            <button type="submit" class="btn-update">Reset Password</button>
            
            <a href="login.php" class="back-link">Back to Login</a>
        </form>
    </div>
    
</div>
</body>
</html>