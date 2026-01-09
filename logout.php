<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Serve - Logout</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Additional styles for logout page only */
        .logout-page-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f7f4ef;
            padding-top: 100px;
        }

        .logout-content {
            background-color: #d3d3d3;
            border-radius: 20px;
            padding: 60px 80px;
            text-align: center;
            width: 90%;
            max-width: 900px;
        }

        .logout-content h1 {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 40px;
            color: #000;
        }

        .logout-modal {
            background-color: white;
            border-radius: 15px;
            padding: 60px 40px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            max-width: 400px;
            margin: 0 auto;
        }

        .logout-modal p {
            font-size: 24px;
            margin-bottom: 40px;
            color: #000;
        }

        .logout-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .logout-buttons button {
            padding: 15px 50px;
            font-size: 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            background-color: #000;
            color: white;
            transition: background-color 0.3s ease;
        }

        .logout-buttons button:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="menubar">
        <div class="logo">
            <img src="logo.png" alt="Smart Serve Logo">
            <span style="font-size: 28px; font-weight: bold; margin-left: 10px;">SMART SERVE</span>
        </div>
        <ul>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="index.php">Home</a></li>
        </ul>
    </nav>

    <!-- Logout Confirmation Section -->
    <div class="logout-page-container">
        <div class="logout-content">
            <h1>Log out</h1>
            <div class="logout-modal">
                <p>Are you sure to<br>log out?</p>
                <div class="logout-buttons">
                    <button onclick="confirmLogout()">Yes</button>
                    <button onclick="cancelLogout()">No</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmLogout() {
            // Redirect directly to logout.php which will handle session and redirect to login
            window.location.href = 'login.php';
        }

        function cancelLogout() {
            // Go back to previous page
            window.history.back();
        }
    </script>
</body>
</html>