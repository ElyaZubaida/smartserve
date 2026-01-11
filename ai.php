<!-- 
 Frontend: Elya 
 Backend: Elya 
 -->
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $food_type = $_POST['food_type'];
    $meal_type = $_POST['meal_type'];
    $cuisine = $_POST['cuisine'];
    $taste = $_POST['taste'];
    $hunger = $_POST['hunger'];
    $budget = $_POST['budget'];

    $url = 'http://localhost:5000/recommend';

    $budget = $_POST['budget'];

    if (empty($budget)) {
        $budget = 999; 
    }

    $data = [
        'food_type' => $food_type,
        'meal_type' => $meal_type,
        'cuisine' => $cuisine,
        'taste' => $taste,
        'hunger' => $hunger,
        'budget' => $budget
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    $foods = json_decode($response, true);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Food Recommendation</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="style.css"> 
</head>
<body class="ai-page">

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

    <!-- Form for Food Recommendation -->
    <div class="ai-container">
        <div class="ai-title">
            <h1>AI Food Recommendation</h1>
            <h3>Find Your Perfect Meal!</h3>
        </div>
        <form method="POST" class="food-recommendation-form">
            <div class="form-group">
                <label for="food_type">Food Type:</label>
                <select name="food_type" id="food_type">
                    <option value="">Select Food Type</option>
                    <option value="Rice">Rice</option>
                    <option value="Noodles">Noodles</option>
                    <option value="Western">Soup</option>
                    <option value="Western">Dessert</option>
                    <option value="Fast Food">Drinks</option>
                </select>
            </div>

            <div class="form-group">
                <label for="meal_type">Meal Type:</label>
                <select name="meal_type" id="meal_type">
                    <option value="">Select Meal Type</option>
                    <option value="Breakfast">Breakfast</option>
                    <option value="Lunch">Lunch</option>
                    <option value="Dinner">Dinner</option>
                </select>
            </div>

            <div class="form-group">
                <label for="cuisine">Cuisine:</label>
                <select name="cuisine" id="cuisine">
                    <option value="">Select Cuisine</option>
                    <option value="Malay">Malay</option>
                    <option value="Chinese">Chinese</option>
                    <option value="Indian">Indian</option>
                    <option value="Western">Western</option>
                </select>
            </div>

            <div class="form-group">
                <label for="taste">Taste Preference:</label>
                <select name="taste" id="taste">
                    <option value="">Select Taste</option>
                    <option value="Spicy">Spicy</option>
                    <option value="Sweet">Sweet</option>
                    <option value="Savoury">Savoury</option>
                </select>
            </div>

            <div class="form-group">
                <label for="hunger">Hunger Level:</label>
                <select name="hunger" id="hunger">
                    <option value="">Select Hunger Level</option>
                    <option value="Light">Light</option>
                    <option value="Normal">Normal</option>
                    <option value="Very Hungry">Hungry</option>
                </select>
            </div>

            <div class="form-group">
                <label for="budget">Budget (RM):</label>
                <input type="number" name="budget" id="budget">
            </div>

            <button type="submit" class="recommendation-btn">Get Recommendation</button>
        </form>
    </div>

    <!-- Food Recommendation Table -->
    <div id="food-recommendation" class="food-recommendation-container">
        <hr>
        <?php if (!empty($foods)): ?>
            <h3>Food Recommendation:</h3>
            <table border="1" cellpadding="8">
                <tr>
                    <th>Name</th>
                    <th>Food Type</th>
                    <th>Meal</th>
                    <th>Cuisine</th>
                    <th>Taste</th>
                    <th>Price (RM)</th>
                    <th>AI Match %</th> </tr>
                <?php foreach ($foods as $food): ?>
                <tr>
                    <td><?php echo $food['name']; ?></td>
                    <td><?php echo $food['food_type']; ?></td>
                    <td><?php echo $food['meal_type']; ?></td>
                    <td><?php echo $food['cuisine']; ?></td>
                    <td><?php echo $food['taste']; ?></td>
                    <td><?php echo $food['price']; ?></td>
                    <td><strong><?php echo $food['match_percentage']; ?>%</strong></td> </tr>
                <?php endforeach; ?>
            </table>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <p>No food matches your preference.</p>
        <?php endif; ?>
    </div>

</body>
</html>
