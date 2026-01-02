<!-- 
 Frontend: Mina 
 Backend: ? 
 -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
</head>
<body class="staff-style-student-page">
    <!-- Navigation Bar -->
    <header>
        <div class="menubar">
            <div class="logo">
                <img src="img/logo.png" alt="Smart Serve Logo">
            </div>

            <nav>
                <ul>
                    <li><a href="menu.php"><span class="material-symbols-outlined">home</span> Home</a></li>
                    <li><a href="ai.php"><span class="material-symbols-outlined">psychology</span> Food Recomendation</a></li>
                    <li><a href="myorders.php"><span class="material-symbols-outlined">receipt_long</span> Orders</a></li>
                    <li><a href="cart.php"><span class="material-symbols-outlined">shopping_cart</span> Cart</a></li>
                    <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="logout.php"><span class="material-symbols-outlined">logout</span></a></li>
                </ul>
            </nav>
        </div>
    </header>
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
</body>
</html>
