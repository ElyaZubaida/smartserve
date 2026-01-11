<!-- 
 Frontend: Insyirah 
 Backend: Aleesya
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Place Order</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
</head>
<body class="place-order-page">

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

    <div class="checkout-container">
        <div class="checkout-title">
            <h1>Review & Place Order</h1>
            <p>Please check your items before confirming</p>
        </div>

        <div class="checkout-content">
            <div class="order-summary-header">
                <h3><span class="material-symbols-outlined">restaurant_menu</span> Your Selection</h3>
            </div>
            
            <div class="order-items-list">
                <div class="order-item-card">
                    <div class="item-img-container">
                        <img src="img/nasilemak.jpg" alt="Nasi Lemak">
                    </div>
                    <div class="item-info">
                        <span class="item-name">Nasi Lemak</span>
                        <span class="item-price">RM 2.00</span>
                    </div>
                    <div class="item-qty-badge">x2</div>
                    <div class="item-subtotal">RM 4.00</div>
                </div>

                <div class="order-item-card">
                    <div class="item-img-container">
                        <img src="img/tehtarik.jpg" alt="Teh Tarik">
                    </div>
                    <div class="item-info">
                        <span class="item-name">Teh Tarik</span>
                        <span class="item-price">RM 2.00</span>
                    </div>
                    <div class="item-qty-badge">x2</div>
                    <div class="item-subtotal">RM 4.00</div>
                </div>
            </div>

            <div class="checkout-footer-card">
                <form action="orderdetails.php" method="POST" class="order-final-form">
                    <div class="pickup-box">
                        <div class="icon-label">
                            <span class="material-symbols-outlined">schedule</span>
                            <label>When will you pick this up?</label>
                        </div>
                        <select name="pickup_time" class="pickup-select-styled" required>
                            <option value="" disabled selected>Select a time slot</option>
                            <option>10.00 AM</option>
                            <option>10.30 AM</option>
                            <option>11.00 AM</option>
                            <option>11.30 AM</option>
                            <option>12.00 PM</option>
                            <option>12.30 PM</option>
                            <option>1.00 PM</option>
                            <option>1.30 PM</option>
                        </select>
                    </div>

                    <div class="final-total-box">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span>RM 8.00</span>
                        </div>
                        <div class="total-row main">
                            <span>Grand Total</span>
                            <span>RM 8.00</span>
                        </div>
                        <button type="submit" class="place-order-confirm-btn">
                            Confirm & Place Order <span class="material-symbols-outlined">arrow_forward</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>