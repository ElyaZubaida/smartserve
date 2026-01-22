<?php
session_start();
include 'config/db_connect.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$foods = [];
$error_message = "";

// Helper function to keep dropdowns selected after submission
function is_selected($field, $value) {
    if (isset($_POST[$field]) && $_POST[$field] == $value) {
        return 'selected';
    }
    return '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $url = 'http://127.0.0.1:5000/recommend';
    
    // Map PHP POST names to the keys Python expects
    $data = [
        'menu_category' => $_POST['menuCategory'] ?? '',
        'food_type'     => $_POST['foodType'] ?? '',
        'meal_type'     => $_POST['mealType'] ?? '',
        'cuisine'       => $_POST['cuisine'] ?? '',
        'flavour'       => $_POST['flavour'] ?? '',
        'portion'       => $_POST['portion'] ?? '',
        'budget'        => !empty($_POST['budget']) ? $_POST['budget'] : 999
    ];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $budget = $_POST['budget'] ?? '';

    // 1. Validate Budget is numeric and positive
    if (!empty($budget) && (!is_numeric($budget) || $budget < 0)) {
        $error_message = "Please enter a valid positive budget amount.";
    } 
    // 2. Ensure Category is selected (if you add a 'Select Category' placeholder)
    elseif (empty($_POST['menuCategory'])) {
        $error_message = "Please select a food category.";
    }
    else {
        // ... proceed with curl_init and the AI request ...
    }
}

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Increased timeout to avoid errors

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error_message = "AI Service is offline. Please start your Python script. Error: " . curl_error($ch);
    } elseif ($http_code !== 200) {
        $error_message = "AI Server Error (Code: $http_code). Check Python terminal.";
    } else {
        $foods = json_decode($response, true);
        if ($foods === null) {
            $error_message = "Failed to decode AI recommendations response.";
        }
    }
    curl_close($ch);
}

$ai_online = false;
$fp = @fsockopen("127.0.0.1", 5000, $errno, $errstr, 1);
if ($fp) {
    $ai_online = true;
    fclose($fp);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <li><a href="ai.php" class="active"><span class="material-symbols-outlined">psychology</span>Food Recommendation</a></li>
                    <li><a href="myorders.php"><span class="material-symbols-outlined">receipt_long</span> Orders</a></li>
                    <li><a href="cart.php"><span class="material-symbols-outlined">shopping_cart</span> Cart</a></li>
                    <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="logout.php"><span class="material-symbols-outlined">logout</span></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="ai-container">
        <div class="section-header-box">
            <div class="header-title-group">
                <span class="material-symbols-outlined pulse-icon">psychology</span>
                <h1>AI Food Recommendation</h1>
            </div>
            <p>Smart recommendations based on your unique taste.</p>
        </div>
        <form method="POST" class="ai-recommendation-form">
            <div class="ai-form-grid">
                <div class="form-group">
                    <label><span class="material-symbols-outlined">category</span> Category</label>
                    <select name="menuCategory" required>
                        <option value="rice" <?php echo is_selected('menuCategory', 'rice'); ?>>Rice</option>
                        <option value="noodles" <?php echo is_selected('menuCategory', 'noodles'); ?>>Noodles</option>
                        <option value="soup" <?php echo is_selected('menuCategory', 'soup'); ?>>Soup</option>
                        <option value="wrapnbuns" <?php echo is_selected('menuCategory', 'wrapnbuns'); ?>>Wrap & Buns</option>
                        <option value="snacks" <?php echo is_selected('menuCategory', 'snacks'); ?>>Snacks</option>
                        <option value="dessert" <?php echo is_selected('menuCategory', 'dessert'); ?>>Dessert</option>
                        <option value="drinks" <?php echo is_selected('menuCategory', 'drinks'); ?>>Drinks</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><span class="material-symbols-outlined">track_changes</span> Daily Goal</label>
                    <select name="foodType" required>
                        <option value="healthy" <?php echo is_selected('foodType', 'healthy'); ?>>Stay Healthy</option>
                        <option value="energy-boosting" <?php echo is_selected('foodType', 'energy-boosting'); ?>>Boost Energy</option>
                        <option value="refreshing" <?php echo is_selected('foodType', 'refreshing'); ?>>Be Refreshed</option>
                        <option value="fastneasy" <?php echo is_selected('foodType', 'fastneasy'); ?>>Quick Bite</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><span class="material-symbols-outlined">schedule</span> Meal Time</label>
                    <select name="mealType" required>
                        <option value="anytime" <?php echo is_selected('mealType', 'anytime'); ?>>Anytime</option>
                        <option value="breakfast" <?php echo is_selected('mealType', 'breakfast'); ?>>Breakfast</option>
                        <option value="lunch" <?php echo is_selected('mealType', 'lunch'); ?>>Lunch</option>
                        <option value="dinner" <?php echo is_selected('mealType', 'dinner'); ?>>Dinner</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><span class="material-symbols-outlined">public</span> Cuisine</label>
                    <select name="cuisine" required>
                        <option value="malay" <?php echo is_selected('cuisine', 'malay'); ?>>Malay</option>
                        <option value="chinese" <?php echo is_selected('cuisine', 'chinese'); ?>>Chinese</option>
                        <option value="indian" <?php echo is_selected('cuisine', 'indian'); ?>>Indian</option>
                        <option value="western" <?php echo is_selected('cuisine', 'western'); ?>>Western</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><span class="material-symbols-outlined">restaurant</span> Flavour</label>
                    <select name="flavour" required>
                        <option value="spicy" <?php echo is_selected('flavour', 'spicy'); ?>>Spicy</option>
                        <option value="sweet" <?php echo is_selected('flavour', 'sweet'); ?>>Sweet</option>
                        <option value="savoury" <?php echo is_selected('flavour', 'savoury'); ?>>Savoury</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><span class="material-symbols-outlined">monitor_weight</span> Hunger Level</label>
                    <select name="portion" required>
                        <option value="light" <?php echo is_selected('portion', 'light'); ?>>Light</option>
                        <option value="regular" <?php echo is_selected('portion', 'regular'); ?>>Regular</option>
                        <option value="large" <?php echo is_selected('portion', 'large'); ?>>Large</option>
                    </select>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label><span class="material-symbols-outlined">payments</span> Max Budget (RM)</label>
                    <input type="number" name="budget" placeholder="Enter your max budget (e.g. 15.00)" step="0.01" min="0" oninput="validity.valid||(value='');" value="<?php echo isset($_POST['budget']) ? htmlspecialchars($_POST['budget']) : ''; ?>">
                </div>
            </div>
            <div style="display: flex; gap: 15px;">
                <button type="submit" class="ai-btn" style="flex: 3.5;">Recommend Me <span class="material-symbols-outlined">bolt</span></button>
                <a href="ai.php" class="ai-btn" style="flex: 0.5; background: #f4f4f4; color: #666; text-decoration: none;">Reset <span class="material-symbols-outlined">restart_alt</span></a>
            </div>
        </form>

        <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">

        <?php if ($error_message): ?>
            <div class="error-box">
                <span class="material-symbols-outlined">error</span> 
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($foods)): ?>
            <div class="results-wrapper">
                <?php $is_fallback = isset($foods[0]['match_type']) && $foods[0]['match_type'] === 'fallback'; ?>

                <?php if ($is_fallback): ?>
                    <div class="ai-apology-box" style="text-align: center; margin: 20px 0; padding: 25px; background: #fff5f5; border-radius: 12px; border: 1px solid #ffcccc;">
                        <span class="material-symbols-outlined" style="font-size: 48px; color: #e53935; margin-bottom: 10px;">sentiment_dissatisfied</span>
                        <h3 style="color: #333;">Sorry, nothing matched your preferences perfectly (70% Threshold).</h3>
                        <p style="color: #666;">Showing our most popular items instead!</p>
                    </div>
                <?php else: ?>
                    <h2 class="results-title"><span class="material-symbols-outlined">verified</span> AI Top Recommendations</h2>
                <?php endif; ?>

                <div class="menu-grid">
                    <?php foreach ($foods as $food): 
                        $m_id    = $food['menuID'] ?? 0;
                        $m_name  = $food['name'] ?? $food['menuName'] ?? 'Unknown Item';
                        $m_price = $food['price'] ?? $food['menuPrice'] ?? 0.00;
                        $m_img   = $food['menuImage'] ?? 'default.png';
                        $m_desc  = $food['menuDescription'] ?? 'Freshly prepared SmartServe meal.';
                        $m_match = $food['match_percentage'] ?? 0;

                        $imgPath = (strpos($m_img, 'img/') === false) ? 'img/' . $m_img : $m_img;
                    ?>
                        <a href="menudetails.php?id=<?php echo $m_id; ?>" class="ai-card-link">
                            <div class="menu-item ai-card">
                                <div class="match-badge" style="<?php echo ($is_fallback || $m_match < 1) ? 'background: #ffa000;' : ''; ?>">
                                    <span class="material-symbols-outlined">star_rate</span>
                                    <?php echo ($is_fallback || $m_match < 1) ? "Popular" : htmlspecialchars($m_match) . "% Match"; ?>
                                </div>
                                 
                                <img src="<?php echo htmlspecialchars($imgPath); ?>" onerror="this.src='img/default_food.png'" class="food-img" alt="Food">
                                 
                                <div class="item-info">
                                    <h3><?php echo htmlspecialchars($m_name); ?></h3>
                                    <p class="food-desc"><?php echo htmlspecialchars(strlen($m_desc) > 80 ? substr($m_desc, 0, 77) . '...' : $m_desc); ?></p>

                                    <div class="card-footer">
                                        <span class="price">RM <?php echo number_format($m_price, 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif ($_SERVER['REQUEST_METHOD'] !== 'POST' && !$error_message): ?>
            <div class="empty-state-container" style="text-align: center; padding: 60px 20px;">
                <span class="material-symbols-outlined" style="font-size: 80px; color: #eee; margin-bottom: 20px;">robot_2</span>
                <h3 style="color: #aaa;">The AI Engine is Waiting</h3>
                <p style="color: #bbb;">Fill out the preferences above to see your personalized recommendations!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
                     