<!-- 
 Frontend: Insyirah 
 Backend: Qis 
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Smart Serve</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="signup-page">
    <!-- Header -->
    <header>
        <div class="logo">
            <img src="img/logo.png" alt="Smart Serve Logo"> <!-- Replace with your logo image -->
            <h1>SmartServe</h1>
        </div>
        <hr> <!-- Separator line -->
    </header>

    <!-- Sign Up Form -->
    <div class="signup-container">
        <h2>SIGN UP</h2>
        <form action="signup_process.php" method="POST">
            <div class="signup-field">
                <input type="text" name="name" placeholder="NAME" required>
            </div>
            <div class="signup-field">
                <input type="email" name="email" placeholder="EMAIL" required>
            </div>
            <div class="signup-field">
                <input type="text" name="username" placeholder="USERNAME" required>
            </div>
            <div class="signup-field">
                <input type="password" name="password" placeholder="PASSWORD" required>
            </div>
            <button type="submit" class="signup-btn">SIGN UP</button>
        </form>
    </div>
</body>
</html>