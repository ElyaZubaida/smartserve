<?php
session_start();
include 'config/db_connect.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$foods = [];
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $url = 'http://127.0.0.1:5000/recommend';
    $data = [
        'menu_category' => $_POST['menuCategory'] ?? '',
        'food_type'     => $_POST['foodType'] ?? '',
        'meal_type'     => $_POST['mealType'] ?? '',
        'cuisine'       => $_POST['cuisine'] ?? '',
        'flavour'       => $_POST['flavour'] ?? '',
        'portion'       => $_POST['portion'] ?? '',
        'budget'        => !empty($_POST['budget']) ? $_POST['budget'] : 999
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error_message = "AI Service is offline.";
    } elseif ($http_code !== 200) {
        $error_message = "AI Server Error. Check Python Console.";
    } else {
        $foods = json_decode($response, true);
    }
    curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SmartServe - AI Food Recommendation</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
</head>
<body class="ai-page">
    <header>
        <div class="menubar">
            <div class="logo"><img src="img/logo.png" alt="Logo"></div>
            <nav>
                <ul>
                    <li><a href="menu.php"><span class="material-symbols-outlined">home</span> Home</a></li>
                    <li><a href="ai.php" class="active"><span class="material-symbols-outlined">psychology</span> Food Recommendation</a></li>
                    <li><a href="myorders.php"><span class="material-symbols-outlined">receipt_long</span> Orders</a></li>
                    <li><a href="cart.php"><span class="material-symbols-outlined">shopping_cart</span> Cart</a></li>
                    <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="logout.php"><span class="material-symbols-outlined">logout</span></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="ai-container">
        <div class="ai-header-box">
            <span class="material-symbols-outlined icon-main">psychology</span>
            <h1>AI Food Recommendation</h1>
            <p>Tell us your cravings and needs. We'll build the perfect match.</p>
        </div>

        <form method="POST" class="ai-recommendation-form">
            <div class="ai-form-grid">
                <div class="form-group">
                    <label><span class="material-symbols-outlined">category</span> Category</label>
                    <select name="menuCategory" required>
                        <option value="rice">Rice</option>
                        <option value="noodles">Noodles</option>
                        <option value="soup">Soup</option>
                        <option value="wrapnbuns">Wrap & Buns</option>
                        <option value="snacks">Snacks</option>
                        <option value="dessert">Dessert</option>
                        <option value="drinks">Drinks</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><span class="material-symbols-outlined">track_changes</span> Daily Goal</label>
                    <select name="foodType" required>
                        <option value="healthy">Stay Healthy</option>
                        <option value="energy-boosting">Boost Energy</option>
                        <option value="refreshing">Be Refreshed</option>
                        <option value="fastneasy">Quick Bite</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><span class="material-symbols-outlined">schedule</span> Meal Time</label>
                    <select name="mealType" required>
                        <option value="anytime">Anytime</option>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><span class="material-symbols-outlined">public</span> Cuisine</label>
                    <select name="cuisine" required>
                        <option value="malay">Malay</option>
                        <option value="chinese">Chinese</option>
                        <option value="indian">Indian</option>
                        <option value="western">Western</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><span class="material-symbols-outlined">restaurant</span> Flavour</label>
                    <select name="flavour" required>
                        <option value="spicy">Spicy</option>
                        <option value="sweet">Sweet</option>
                        <option value="savoury">Savoury</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><span class="material-symbols-outlined">monitor_weight</span> Hunger Level</label>
                    <select name="portion" required>
                        <option value="light">Light</option>
                        <option value="regular">Regular</option>
                        <option value="large">Large</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label><span class="material-symbols-outlined">payments</span> Max Budget (RM)</label>
                    <input type="number" name="budget" placeholder="Enter your max budget (e.g. 15.00)" step="0.50">
                </div>
            </div>
            <button type="submit" class="ai-btn">Analyze & Architect <span class="material-symbols-outlined">bolt</span></button>
        </form>

        <?php if ($error_message): ?>
            <div class="error-box"><span class="material-symbols-outlined">error</span> <?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($foods)): ?>
            <div class="results-wrapper">
                <h2 class="results-title"><span class="material-symbols-outlined">verified</span> AI Top Recommendations</h2>
                <div class="menu-grid">
                    <?php foreach ($foods as $food): 
                        $imgPath = $food['menuImage'];
                        if (strpos($imgPath, 'img/') === false) { $imgPath = 'img/' . $imgPath; }
                    ?>
                        <div class="menu-item ai-card">
                            <div class="match-badge">
                                <span class="material-symbols-outlined">star_rate</span>
                                <?php echo htmlspecialchars($food['match_percentage']); ?><?php echo is_numeric($food['match_percentage']) ? '%' : ''; ?> Match
                            </div>
                            <img src="<?php echo htmlspecialchars($imgPath); ?>" class="food-img">
                            <div class="item-info">
                                <h3><?php echo htmlspecialchars($food['name']); ?></h3>
                                <div class="tag-row">
                                    <span class="food-tag"><span class="material-symbols-outlined">language</span> <?php echo ucfirst($food['cuisine']); ?></span>
                                    <span class="food-tag"><span class="material-symbols-outlined">palette</span> <?php echo ucfirst($food['flavour']); ?></span>
                                </div>
                                <div class="card-footer">
                                    <span class="price">RM <?php echo number_format($food['price'], 2); ?></span>
                                    <a href="menudetails.php?id=<?php echo $food['menuID']; ?>" class="view-btn">View Details <span class="material-symbols-outlined">arrow_forward</span></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
            <div class="empty-state-container">
                <span class="material-symbols-outlined">robot_2</span>
                <h3>The AI Engine is Waiting</h3>
                <p>Fill out the preferences above to see your personalized recommendations!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>