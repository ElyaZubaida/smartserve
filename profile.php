<!-- 
 Frontend: Mina 
 Backend: ? 
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Student Profile</title>

    <!-- Google Fonts Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="style.css">
</head>
<body class="staff-style-student-page">
<!-- ================= NAVIGATION BAR ================= -->
<header>
    <div class="menubar">
        <div class="logo">
            <img src="logo.png" alt="Smart Serve Logo">
        </div>

        <nav>
            <ul>
                <li><a href="myorders.php">My Orders</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- ================= PROFILE CARD ================= -->
<div class="profile-card">
    <h2>Profile</h2>
    <form>
        <input type="text" class="input-field" placeholder="Name" required>
        <input type="email" class="input-field" placeholder="Email" required>
        <input type="text" class="input-field" placeholder="Username" required>
        <input type="password" class="input-field" placeholder="Password" required>

        <button type="submit" class="btn-update">Update</button>
    </form>
</div>

<!-- ================= FOOTER ================= -->
<footer>
    <p>SmartServe - Student Canteen Food Ordering System</p>
</footer>
</body>
</html>

