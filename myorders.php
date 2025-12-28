<!-- 
 Frontend: Insyirah 
 Backend: ? 
 -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - My Orders</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navigation Bar -->
    <header>
        <div class="menubar">
            <!-- Logo -->
            <div class="logo">
                <img src="logo.png" alt="Smart Serve Logo"> <!-- Replace with your logo image -->
            </div>

            <!-- Menu Links -->
            <nav>
                <ul>
                    <li><a href="menu.php">Home</a></li>
                    <li><a href="myorders.php">My Orders</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <!-- Start code here -->
      <!-- Orders Content -->
    <div class="orders-container">
        <div class="orders-title">
            <h2>My Orders</h2>
        </div>

        <!-- Order 1 -->
        <div class="order-card">
            <div class="order-left">
                <div class="order-number">Order No #1000</div>
                <button class="order-status-btn">Order status</button>
            </div>
            <div class="order-right">
                <button class="order-action-btn">Order details</button>
                <button class="order-action-btn">Cancel order</button>
            </div>
        </div>

        <!-- Order 2 -->
        <div class="order-card">
            <div class="order-left">
                <div class="order-number">Order No #999</div>
                <button class="order-status-btn">Order status</button>
            </div>
            <div class="order-right">
                <button class="order-action-btn">Order details</button>
                <button class="order-action-btn">Cancel order</button>
            </div>
        </div>
    </div>
    
</body>
</html>
