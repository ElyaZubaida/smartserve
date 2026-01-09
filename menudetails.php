<!-- 
 Frontend: Elya 
 Backend: Aleesya 
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Serve - Menu Details</title>
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

    <!-- Menu Details Section -->
    <div class="details-container">
        <div class="orders-title">
            <h2>Menu Details</h2>
        </div>
        
        <section class="menu-details-card">
            
            <div class="details-image-section">
                <img src="img/nasilemak.jpg" alt="Nasi Lemak">
            </div>

            <div class="details-info-section">
                <div class="food-header">
                    <h1>Nasi Lemak</h1>
                    <span class="food-price-tag">RM 5.00</span>
                </div>
                
                <p class="food-desc">Yum yum yummy nasi lemak served with our special sambal.</p>

                <div class="order-options">
                    <div class="input-group">
                        <label><span class="material-symbols-outlined">notes</span> Special Request</label>
                        <input type="text" placeholder="Any special request?">
                    </div>

                    <div class="qty-and-cart">
                        <div class="quantity-controls-modern">
                            <button class="qty-btn decrease">-</button>
                            <input type="number" id="quantity" value="1" readonly>
                            <button class="qty-btn increase">+</button>
                        </div>
                        
                        <button class="btn-add-to-cart" onclick="location.href='cart.php'">
                            <span class="material-symbols-outlined">shopping_cart</span> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        // Quantity adjustment functionality
        const decreaseButton = document.querySelector('.decrease');
        const increaseButton = document.querySelector('.increase');
        const quantityInput = document.getElementById('quantity');

        decreaseButton.addEventListener('click', () => {
            if (quantityInput.value > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        });

        increaseButton.addEventListener('click', () => {
            quantityInput.value = parseInt(quantityInput.value) + 1;
        });
    </script>

</body>
</html>