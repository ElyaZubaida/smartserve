<!-- 
 Frontend: Insyirah 
 Backend: Aleesya 
 -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - My Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
</head>
<body>

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
    <!-- Start code here -->
    <div class="orders-container">
        <div class="orders-title">
            <h1>My Orders</h1>
        </div>

        <div class="order-card">
            <div class="order-left">
                <div class="order-info-group">
                    <span class="order-number">Order #1000</span>
                    <div class="status-badge Pending">
                        <span class="material-symbols-outlined">restaurant</span> Pending
                    </div>
                </div>
                <span class="order-date">Today, 10:00 AM</span>
            </div>
            
            <div class="order-right-stack">
                <button class="order-details-btn" onclick="location.href='orderdetails.php'">
                    <span class="material-symbols-outlined">visibility</span> Details
                </button>
                <button class="cancel-order-btn">
                    <span class="material-symbols-outlined">delete</span> Cancel
                </button>
            </div>
        </div>

        <div class="order-card order-completed-style">
            <div class="order-left">
                <div class="order-info-group">
                    <span class="order-number">Order #999</span>
                    <div class="order-status status-completed">
                        <span class="material-symbols-outlined">task_alt</span> Completed
                    </div>
                </div>
                <span class="order-date">Yesterday, 1:30 PM</span>
            </div>
            
            <div class="order-right-stack">
                <button class="order-details-btn" onclick="location.href='orderdetails.php'">
                    <span class="material-symbols-outlined">visibility</span> Details
                </button>
                <button class="cancel-order-btn btn-dimmed" disabled title="Completed orders cannot be cancelled">
                    <span class="material-symbols-outlined">block</span> Cancel
                </button>
            </div>
        </div>
    </div>
</body>
</html>
