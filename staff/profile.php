<!-- 
 Frontend: Mina 
 Backend: ? 
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Profile</title>

    <!-- Google Fonts Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="sastyle.css">
</head>
<body>

<!-- ================= SIDEBAR ================= -->
<div class="sidebar">
    <div class="logo">
        <img src="logo.png" alt="SmartServe Logo">
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="menu_management.php">Menu Management</a></li>
            <li><a href="order_management.php">Order Management</a></li>
            <li><a href="report.php">Reports</a></li>
            <li><a href="profile.php">My Profile</a></li>
            <li><a href="../logout.php">Log Out</a></li>
        </ul>
    </nav>
</div>

<!-- ================= MAIN CONTENT ================= -->
<div class="main-content">

    <div class="staff-profile-page">
        <div class="profile-card">
            <h2>My Profile</h2>
            <form>
                <input type="text" class="input-field" placeholder="Full Name" required>
                <input type="email" class="input-field" placeholder="Email" required>
                <input type="text" class="input-field" placeholder="Username" required>
                <input type="password" class="input-field" placeholder="Password" required>

                <button type="submit" class="btn-update">Update Profile</button>
            </form>
        </div>
    </div>

    <!-- ================= FOOTER ================= -->
    <footer>
        <p>SmartServe - Staff Portal</p>
    </footer>
</div>

</body>
</html>
