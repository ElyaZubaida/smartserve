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

    // Get menu ID from URL
    if (!isset($_GET['id']) || empty($_GET['id'])) 
    {
        header("Location: menu.php");
        exit();
    }

    $menuID = intval($_GET['id']);
    $student_id = $_SESSION['student_id'];

    // Fetch menu item details
    $query = "SELECT menuID, menuName, menuImage, menuCategory, menuDescription, menuPrice, menuAvailability 
              FROM menus 
              WHERE menuID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $menuID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if menu item exists
    if ($result->num_rows === 0) 
    {
        header("Location: menu.php");
        exit();
    }

    $menu_item = $result->fetch_assoc();

    // Check if item is available
    if ($menu_item['menuAvailability'] == 0) 
    {
        $availability_message = "This item is currently unavailable.";
    }

    $img = $menu_item['menuImage'];
    if (strpos($img, 'img/') === false) { $img = 'img/' . $img; }
?>

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

        <!-- Custom Success Modal -->
        <div id="successModal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <div class="modal-icon">
                    <span class="material-symbols-outlined">check_circle</span>
                </div>
                <h2>Added to Cart!</h2>
                <p>Your item has been successfully added to your cart.</p>
                <div class="modal-buttons">
                    <button onclick="continueBrowsing()" class="btn-secondary">
                        <span class="material-symbols-outlined">storefront</span>
                        Continue Browsing
                    </button>
                    <button onclick="goToCart()" class="btn-primary">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        View Cart
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Bar -->
        <header>
            <div class="menubar">
                <div class="logo">
                    <img src="img/logo.png" alt="Smart Serve Logo">
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

        <!-- Menu Details Section -->
        <div class="details-container">
            <div class="section-header-box">
            <div class="header-title-group">
                <span class="material-symbols-outlined pulse-icon">restaurant_menu</span>
                <h1>Menu Details</h1>
            </div>
            <p>View detailed information about this menu item.</p>
        </div>
            
            <section class="menu-details-card">
                
                <div class="details-image-section">
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="Food Image">
                </div>

                <div class="details-info-section">
                    <div class="food-header">
                        <span class="food-price-tag">RM <?php echo number_format($menu_item['menuPrice'], 2); ?></span>
                        <h1><?php echo htmlspecialchars($menu_item['menuName']); ?></h1>
                    </div>
                    
                    <p class="food-desc">
                        <?php echo htmlspecialchars($menu_item['menuDescription'] ?: 'Delicious ' . $menu_item['menuName']); ?>
                    </p>

                    <?php if (isset($availability_message)): ?>
                        <div class="availability-warning">
                            <span class="material-symbols-outlined">info</span>
                            <?php echo $availability_message; ?>
                        </div>
                    <?php endif; ?>

                    <form id="addToCartForm" method="POST" action="addtocart.php">
                        <input type="hidden" name="menuID" value="<?php echo $menu_item['menuID']; ?>">
                        
                        <div class="order-options">
                            <div class="input-group">
                                <label><span class="material-symbols-outlined">notes</span> Special Request</label>
                                <input type="text" name="special_request" placeholder="Any special request?">
                            </div>

                            <div class="qty-and-cart">
                                <div class="quantity-controls-modern">
                                    <button type="button" class="qty-btn decrease">-</button>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1" readonly>
                                    <button type="button" class="qty-btn increase">+</button>
                                </div>
                                
                                <button type="submit" class="btn-add-to-cart" 
                                        <?php echo ($menu_item['menuAvailability'] == 0) ? 'disabled' : ''; ?>>
                                    <span class="material-symbols-outlined">shopping_cart</span> Add to Cart
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <script>
            function showSuccessModal() {
                document.getElementById('successModal').style.display = 'flex';
            }

            function hideSuccessModal() {
                document.getElementById('successModal').style.display = 'none';
            }

            function continueBrowsing() {
                hideSuccessModal();
                window.location.href = 'menu.php';
            }

            function goToCart() {
                hideSuccessModal();
                window.location.href = 'cart.php';
            }

            // Close modal when clicking outside
            document.getElementById('successModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideSuccessModal();
                }
            });

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

            // Form submission with validation
            document.getElementById('addToCartForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('addtocart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    // Check if response is ok
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(text => {
                    // Try to parse as JSON
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            // Show custom modal instead of alert
                            showSuccessModal();
                        } else {
                            alert(data.message || 'Failed to add item to cart.');
                        }
                    } catch (e) {
                        console.error('Response text:', text);
                        alert('An error occurred. Please check the console for details.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred: ' + error.message);
                });
            });
        </script>
    </body>
</html>