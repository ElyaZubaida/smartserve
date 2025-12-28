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
    <div class="signup-header">
        <img src="logo.png" alt="Smart Serve Logo">
        <h1>SMART SERVE</h1>
    </div>

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