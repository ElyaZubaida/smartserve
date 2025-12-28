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
      <!-- Checkout Content -->
    <div class="checkout-container">
        <div class="checkout-title">
            <h2>Place Order</h2>
        </div>

        <div class="checkout-content">
            <!-- Order Item 1 -->
            <div class="order-item">
                <div class="item-image">
                    <img src="img/nasilemak.jpg" alt="Nasi Lemak">
                </div>
                <div class="item-details">
                    <div class="item-name">Nasi Lemak</div>
                    <div class="item-price">RM2.00</div>
                </div>
                <div class="item-quantity">x2</div>
            </div>

            <!-- Order Item 2 -->
            <div class="order-item">
                <div class="item-image">
                    <img src="img/tehtarik.jpg" alt="Teh Tarik">
                </div>
                <div class="item-details">
                    <div class="item-name">Teh Tarik</div>
                    <div class="item-price">RM2.00</div>
                </div>
                <div class="item-quantity">x2</div>
            </div>

            <!-- Checkout Footer -->
            <div class="checkout-footer">
                <div class="pickup-section">
                    <label>Pickup Time?</label>
                    <select class="pickup-select">
                        <option>10.00AM</option>
                        <option>10.30AM</option>
                        <option>11.00AM</option>
                        <option>11.30AM</option>
                        <option>12.00PM</option>
                        <option>12.30PM</option>
                        <option>1.00PM</option>
                        <option>1.30PM</option>
                    </select>
                </div>

                <div class="total-section">
                    <div class="total-label">Total: RM8.00</div>
                    <button class="place-order-btn">Place Order</button>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>
