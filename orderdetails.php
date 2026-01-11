<!-- 
 Frontend: Elya 
 Backend: Aleesya 
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Order Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
</head>
<body class="details-page">

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

    <div class="order-details-container">
        <div class="checkout-title">
            <h1>Order Details</h1>
        </div>
        <div class="back-nav">
            <a href="myorders.php"><span class="material-symbols-outlined">arrow_back</span> My Orders</a>
        </div>

        <div class="details-main-card">
            <div class="details-header">
                <div class="header-left">
                    <h1>Order #1000</h1>
                    <p>Placed on: <?php echo date('d/m/Y'); ?></p>
                </div>
                <div class="status-badge Pending">Pending</div>
            </div>

            <div class="order-tracker">
                <div class="step active">
                    <span class="material-symbols-outlined">check_circle</span>
                    <p>Pending</p>
                </div>
                <div class="step">
                    <span class="material-symbols-outlined">restaurant</span>
                    <p>Preparing</p>
                </div>
                <div class="step">
                    <span class="material-symbols-outlined">shopping_bag</span>
                    <p>Ready</p>
                </div>
                <div class="step">
                    <span class="material-symbols-outlined">task_alt</span>
                    <p>Completed</p>
                </div>
                <div class="progress-line"></div>
            </div>

            <div class="details-grid">
                <div class="items-summary">
                    <h3>Items Ordered</h3>
                    <div class="summary-item">
                        <span>Nasi Lemak Special x1</span>
                        <span>RM 5.00</span>
                    </div>
                    <hr>
                    <div class="summary-total">
                        <span>Total Paid</span>
                        <span>RM 5.00</span>
                    </div>
                </div>

                <div class="pickup-info-card">
                    <h3>Pickup Information</h3>
                    <div class="info-row">
                        <span class="material-symbols-outlined">schedule</span>
                        <div>
                            <strong>Pickup Time</strong>
                            <p>12:30 PM Today</p>
                        </div>
                    </div>
                    <div class="info-row">
                        <span class="material-symbols-outlined">location_on</span>
                        <div>
                            <strong>Location</strong>
                            <p>Dataran Cendekia</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>