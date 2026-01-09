<!-- 
 Frontend: Elya 
 Backend: Aleesya
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

    <header>
        <div class="menubar">
            <div class="logo">
                <img src="img/logo.png" alt="Smart Serve Logo" >
            </div>
            <nav>
                <ul>
                    <li><a href="menu.php"><span class="material-symbols-outlined">home</span> Home</a></li>
                    <li><a href="ai.php"><span class="material-symbols-outlined">psychology</span> Food Recommendation</a></li>
                    <li><a href="myorders.php"><span class="material-symbols-outlined">receipt_long</span> Orders</a></li>
                    <li><a href="cart.php"><span class="material-symbols-outlined">shopping_cart</span> Cart</a></li>
                    <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="logout.php"><span class="material-symbols-outlined">logout</span></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Welcome!</h1>
            <h3>Discover Delicious Meals Tailored Just for You</h3>
        </div>
    </section>

    <section class="search-container">
        <div class="search-box">
            <span class="material-symbols-outlined">search</span>
            <input type="text" placeholder="What are you craving today?" class="search-input">
        </div>
    </section>

    <div class="category-wrapper">
        <section class="category-buttons">
            <button class="active" onclick="scrollToCategory('recommended')"><span class="material-symbols-outlined">star_shine</span> For You</button>
            <button onclick="scrollToCategory('breakfast')"><span class="material-symbols-outlined">egg_alt</span> Breakfast</button>
            <button onclick="scrollToCategory('rice')"><span class="material-symbols-outlined">rice_bowl</span> Rice</button>
            <button onclick="scrollToCategory('noodles')"><span class="material-symbols-outlined">ramen_dining</span> Noodles</button>
            <button onclick="scrollToCategory('soup')"><span class="material-symbols-outlined">soup_kitchen</span> Soup</button>
            <button onclick="scrollToCategory('desserts')"><span class="material-symbols-outlined">icecream</span> Desserts</button>
            <button onclick="scrollToCategory('drinks')"><span class="material-symbols-outlined">water_full</span> Drinks</button>
        </section>
    </div>

    <main class="menu-container">
        
        <section class="menu-category ai-highlight" id="recommended">
            <div class="category-header">
                <h2><span class="material-symbols-outlined">star_shine</span> AI Recommended for You</h2>
                <p>Based on your order history</p>
            </div>
            
            <div class="menu-grid">
                <div class="menu-item featured">
                    <a href="menudetails.php?id=1" class="item-link">
                        <img src="img/nasilemak.jpg" alt="Nasi Lemak">
                        <div class="item-info">
                            <h3>Nasi Lemak Special</h3>
                            <span class="price">RM 5.00</span>
                        </div>
                    </a>
                </div>

                <div class="menu-item featured">
                    <a href="menudetails.php?id=1" class="item-link">
                        <img src="img/meegoreng.jpg" alt="Mee Goreng">
                        <div class="item-info">
                            <h3>Mee Goreng Mamak</h3>
                            <span class="price">RM 4.50</span>
                        </div>
                    </a>
                </div>

                <div class="menu-item featured">
                    <img src="img/tehtarik.jpg" alt="Teh Tarik">
                    <div class="item-info">
                        <h3>Teh Tarik</h3>
                        <span class="price">RM 6.00</span>
                    </div>
                </div>
            </div>
        </section>
        <section class="menu-category" id="breakfast">
            <div class="category-header">
                <h2><span class="material-symbols-outlined">rice_bowl</span> Breakfast</h2>
            </div>
            <div class="menu-grid">
                <div class="menu-item">
                    <img src="img/chickenrice.jpg" alt="Chicken Rice">
                    <div class="item-info">
                        <h3>Hainanese Chicken Rice</h3>
                        <span class="price">RM 7.50</span>
                    </div>
                </div>
                <div class="menu-item">
                    <img src="img/friedrice.jpg" alt="Fried Rice">
                    <div class="item-info">
                        <h3>Kampung Fried Rice</h3>
                        <span class="price">RM 6.50</span>
                    </div>
                </div>
                <div class="menu-item">
                    <img src="img/paprik.jpg" alt="Nasi Paprik">
                    <div class="item-info">
                        <h3>Nasi Paprik Ayam</h3>
                        <span class="price">RM 8.00</span>
                    </div>
                </div>
            </div>
        </section>
        <section class="menu-category" id="rice">
            <div class="category-header">
                <h2><span class="material-symbols-outlined">rice_bowl</span> Rice Dishes</h2>
            </div>
            <div class="menu-grid">
                <div class="menu-item">
                    <img src="img/chickenrice.jpg" alt="Chicken Rice">
                    <div class="item-info">
                        <h3>Hainanese Chicken Rice</h3>
                        <span class="price">RM 7.50</span>
                    </div>
                </div>
                <div class="menu-item">
                    <img src="img/friedrice.jpg" alt="Fried Rice">
                    <div class="item-info">
                        <h3>Kampung Fried Rice</h3>
                        <span class="price">RM 6.50</span>
                    </div>
                </div>
                <div class="menu-item">
                    <img src="img/paprik.jpg" alt="Nasi Paprik">
                    <div class="item-info">
                        <h3>Nasi Paprik Ayam</h3>
                        <span class="price">RM 8.00</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        function scrollToCategory(category) {
            const section = document.getElementById(category);
            const offset = 130; // Accounts for sticky headers
            const bodyRect = document.body.getBoundingClientRect().top;
            const elementRect = section.getBoundingClientRect().top;
            const elementPosition = elementRect - bodyRect;
            const offsetPosition = elementPosition - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
            
            document.querySelectorAll('.category-buttons button').forEach(btn => {
                btn.classList.remove('active');
                if(btn.innerText.toLowerCase().includes(category)) {
                    btn.classList.add('active');
                }
            });
        }
    </script>
</body>
</html>