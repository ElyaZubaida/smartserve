<!-- 
 Frontend: Elya 
 Backend: ? 
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Serve - Menu Details</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <!-- Navigation Bar -->
    <header>
        <div class="menubar">
            <div class="logo">
                <img src="logo.png" alt="Smart Serve Logo"> <!-- Replace with your logo image -->
            </div>
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

    <!-- Menu Details Section -->
    <section class="menu-details">
        <div class="menu-info">
            <h2>Menu Details</h2>

            <!-- Static Data for Menu Item -->
            <div class="food-image">
                <img src="img/nasilemak.jpg" alt="Nasi Lemak">
            </div>

            <div class="food-description">
                <p><strong>Name:</strong> Nasi Lemak</p>
                <p><strong>Price:</strong> RM 5.00</p>
                <p><strong>Description:</strong> Yum yum yummy nasi lemak</p>
            </div>

            <!-- Quantity and Request -->
            <div class="food-quantity">
                <p><strong>Request:</strong> <input type="text" placeholder="Any special request?"></p>
                <div class="quantity-controls">
                    <button class="decrease">-</button>
                    <input type="number" id="quantity" value="1" min="1" max="99">
                    <button class="increase">+</button>
                </div>
            </div>

            <!-- Add to Cart Button -->
            <div class="add-to-cart">
                <button class="btn-add-to-cart"><a href="cart.php">Add to Cart</button></a>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <p>SmartServe - Student Canteen Food Ordering System</p>
    </footer>

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