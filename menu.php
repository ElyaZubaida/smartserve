<!-- 
 Frontend: Elya 
 Backend: ? 
 -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Homepage/Menu</title>
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
    <!-- Search Bar -->
    <section class="search-bar">
        <input type="text" placeholder="Search for dishes..." class="search-input">
        <span class="material-symbols-outlined">search</span>
    </section>

    <!-- Category Buttons -->
    <section class="category-buttons">
        <button onclick="scrollToCategory('recommended')">Recommended</button>
        <button onclick="scrollToCategory('breakfast')">Breakfast</button>
        <button onclick="scrollToCategory('rice')">Rice</button>
        <button onclick="scrollToCategory('noodles')">Noodles</button>
        <button onclick="scrollToCategory('soup')">Soup</button>
        <button onclick="scrollToCategory('desserts')">Desserts</button>
        <button onclick="scrollToCategory('drinks')">Drinks</button>
    </section>

    <!-- Recommended Section -->
    <section class="menu-category" id="recommended">
        <h2>Recommended for You</h2>
        <div class="menu-items">
            <div class="menu-item">
                <a href="menudetails.php?pid=1">
                    <img src="img/nasilemak.jpg" alt="Nasi Lemak">
                </a>
                <h3>Nasi Lemak</h3>
                <span>RM 5.00</span>
            </div>
            <div class="menu-item">
                <a href="menudetails.php?pid=2">
                    <img src="img/meegoreng.jpg" alt="Mee Goreng">
                </a>
                <h3>Mee Goreng</h3>
                <span>RM 4.50</span>
            </div>
            <div class="menu-item">
                <a href="menudetails.php?pid=3">
                    <img src="img/tehtarik.jpg" alt="Teh Tarik">
                </a>
                <h3>Teh Tarik</h3>
                <span>RM 2.50</span>
            </div>
        </div>
        <hr>
    </section>

    <!-- Other Categories -->
    <section class="menu-category" id="breakfast">
        <h2>Breakfast</h2>
        <div class="menu-items">
            <div class="menu-item">
                <a href="menudetails.php?pid=4">
                    <img src="img/nasilemak.jpg" alt="Nasi Lemak">
                </a>
                <h3>Nasi Lemak</h3>
                <span>RM 5.00</span>
            </div>
            <div class="menu-item">
                <a href="menudetails.php?pid=5">
                    <img src="img/meegoreng.jpg" alt="Mee Goreng">
                </a>
                <h3>Mee Goreng</h3>
                <span>RM 4.50</span>
            </div>
        </div>
        <hr>
    </section>

    <!-- Repeat for Other Categories (Rice, Noodles, etc.) -->
     
    <script>
        // Function to scroll to the respective category
        function scrollToCategory(category) {
            const section = document.getElementById(category);
            section.scrollIntoView({ behavior: 'smooth' });
        }
    </script>

</body>
</html>
