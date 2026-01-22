<!-- 
 Frontend: Elya 
 Backend: Aleesya
 -->

<?php
    // Database connection
    session_start();
    include 'config/db_connect.php';

    // Check if user is logged in
    if (!isset($_SESSION['student_id'])) 
    {
        header('Location: login.php');
        exit();
    }

    $student_id = $_SESSION['student_id'];

    //fetch all menu items grouped by category
    $menu_query = "SELECT menuID, menuName, menuPrice, menuImage, menuCategory, menuAvailability FROM menus ORDER BY menuCategory, menuName";
    $menu_result = $conn->query($menu_query);

    //group menu items by category
    $menu_by_category = [];
    while ($item = $menu_result->fetch_assoc()) 
    {
        $category = $item['menuCategory'];

        if (!isset($menu_by_category[$category])) 
        {
            $menu_by_category[$category] = [];
        }

        $menu_by_category[$category][] = $item;
    }
?>

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
                <h1>Welcome<?php echo isset($_SESSION['student_name']) ? ', ' . htmlspecialchars($_SESSION['student_name']) : ''; ?>!</h1>
                <h3>Discover Delicious Meals Tailored Just for You</h3>
            </div>
        </section>

        <section class="search-container">
            <div class="search-box">
                <span class="material-symbols-outlined">search</span>
                <input type="text" id="searchInput" placeholder="What are you craving today?" class="search-input">
            </div>
        </section>

        <div class="category-wrapper">
            <section class="category-buttons">
                <button onclick="scrollToCategory('rice')"><span class="material-symbols-outlined">rice_bowl</span> Rice</button>
                <button onclick="scrollToCategory('noodles')"><span class="material-symbols-outlined">ramen_dining</span> Noodles</button>
                <button onclick="scrollToCategory('soup')"><span class="material-symbols-outlined">soup_kitchen</span> Soup</button>
                <button onclick="scrollToCategory('wrapnbuns')"><span class="material-symbols-outlined">bakery_dining</span> Wrap & Buns</button>
                <button onclick="scrollToCategory('snacks')"><span class="material-symbols-outlined">fastfood</span> Snacks</button>
                <button onclick="scrollToCategory('dessert')"><span class="material-symbols-outlined">icecream</span> Desserts</button>
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
                        <a href="menudetails.php?id=2" class="item-link">
                            <img src="img/meegoreng.jpg" alt="Mee Goreng">
                            <div class="item-info">
                                <h3>Mee Goreng Mamak</h3>
                                <span class="price">RM 4.50</span>
                            </div>
                        </a>
                    </div>

                    <div class="menu-item featured">
                        <a href="menudetails.php?id=3" class="item-link">
                            <img src="img/tehtarik.jpg" alt="Teh Tarik">
                            <div class="item-info">
                                <h3>Teh Tarik</h3>
                                <span class="price">RM 6.00</span>
                            </div>
                        </a>
                    </div>
                </div>
            </section>

            <?php
            $categories = ['rice', 'noodles', 'soup', 'wrapnbuns', 'snacks', 'dessert', 'drinks'];

            foreach($categories as $cat): 
                // Only display the section if the category actually has items in the database
                if (isset($menu_by_category[$cat]) && !empty($menu_by_category[$cat])): 
            ?>
                <section class="menu-category" id="<?php echo $cat; ?>">
                    <div class="category-header">
                        <h2>
                            <span class="material-symbols-outlined">
                                <?php
                                    // Dynamic Icons based on category
                                    if($cat == 'rice') echo 'rice_bowl';
                                    elseif($cat == 'noodles') echo 'ramen_dining';
                                    elseif($cat == 'soup') echo 'soup_kitchen';
                                    elseif($cat == 'wrapnbuns') echo 'bakery_dining';
                                    elseif($cat == 'snacks') echo 'fastfood';
                                    elseif($cat == 'dessert') echo 'icecream';
                                    elseif($cat == 'drinks') echo 'water_full';
                                ?>
                            </span>
                            <?php echo ($cat == 'wrapnbuns') ? "Wrap & Buns" : ucfirst($cat); ?>
                        </h2>
                    </div>
                    
                    <div class="menu-grid">
                        <?php foreach ($menu_by_category[$cat] as $item): ?>
                            <div class="menu-item <?php echo ($item['menuAvailability'] == 0) ? 'unavailable' : ''; ?>">
                                <a href="menudetails.php?id=<?php echo $item['menuID']; ?>" class="item-link">
                                    <img src="img/<?php echo htmlspecialchars($item['menuImage']); ?>" 
                                        onerror="this.src='img/default_food.png'" 
                                        alt="<?php echo htmlspecialchars($item['menuName']); ?>">
                                    <div class="item-info">
                                        <h3><?php echo htmlspecialchars($item['menuName']); ?></h3>
                                        <span class="price">RM <?php echo number_format($item['menuPrice'], 2); ?></span>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php 
                endif; 
            endforeach; 
            ?>
        </main>

        <!-- Back to Top Button -->
        <button id="backToTop" onclick="scrollToTop()">
            <span class="material-symbols-outlined">arrow_upward</span>
        </button>

        <script>
            function scrollToCategory(category) 
            {
                const section = document.getElementById(category);
                const offset = 130; 
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

            // Search functionality
            document.getElementById('searchInput').addEventListener('input', function(e) 
            {
                const searchTerm = e.target.value.toLowerCase();
                const menuItems = document.querySelectorAll('.menu-item');
                const categories = document.querySelectorAll('.menu-category');
                
                // If search is empty, show everything
                if (searchTerm === '') 
                {
                    menuItems.forEach(item => {
                        item.style.display = 'block';
                    });
                    categories.forEach(category => {
                        category.style.display = 'block';
                    });
                    return;
                }
                
                // Hide all categories initially
                categories.forEach(category => {
                    category.style.display = 'none';
                });
                
                // Search through menu items
                menuItems.forEach(item => 
                {
                    const itemName = item.querySelector('h3').textContent.toLowerCase();
                    const parentCategory = item.closest('.menu-category');
                    
                    if (itemName.includes(searchTerm)) 
                    {
                        item.style.display = 'block';
                        // Show the parent category if item matches
                        if (parentCategory) {
                            parentCategory.style.display = 'block';
                        }
                    } 
                    else 
                    {
                        item.style.display = 'none';
                    }
                });
            });

            // Back to Top Button Functionality
            const backToTopBtn = document.getElementById('backToTop');

            // Show/hide button based on scroll position
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopBtn.style.display = 'flex';
                } else {
                    backToTopBtn.style.display = 'none';
                }
            });

            // Scroll to top function
            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        </script>
    </body>
</html>