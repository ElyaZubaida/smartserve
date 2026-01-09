<!-- 
 Frontend: Elya 
 Backend: Aleesya 
 -->
<?php
session_start();
$_SESSION['cart'] = [
    [
        'pid' => 1,
        'name' => 'Nasi Lemak',
        'price' => 5.00,
        'image' => 'nasilemak.jpg',
        'qty' => 1
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Cart</title>
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
      <!-- Cart Section -->
    <div class="cart-container">
    <div class="cart-title">
        <h1>My Cart</h1>
    </div>

    <section class="cart-layout">
        <div class="cart-items-wrapper">
            <?php if (count($_SESSION['cart']) > 0): ?>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="cart-card">
                        <div class="cart-card-img">
                            <img src="img/<?= $item['image']; ?>" alt="<?= $item['name']; ?>">
                        </div>
                        <div class="cart-card-info">
                            <div class="info-top">
                                <h3><?= $item['name']; ?></h3>
                                <p class="price-tag">RM <?= number_format($item['price'], 2); ?></p>
                            </div>
                            
                            <form action="cart.php" method="POST" class="cart-card-actions">
                                <div class="qty-selector">
                                    <label>Qty:</label>
                                    <input type="number" name="qty" value="<?= $item['qty']; ?>" min="1" max="99" onchange="this.form.submit()">
                                </div>
                                <input type="hidden" name="pid" value="<?= $item['pid']; ?>">
                                <input type="hidden" name="remove_item" value="<?= $item['pid']; ?>">
                                <button type="submit" class="text-remove-btn">
                                    <span class="material-symbols-outlined">delete_outline</span> Remove
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <p>Your cart is empty.</p>
                    <a href="menu.php" class="btn-outline">Browse Menu</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if (count($_SESSION['cart']) > 0): ?>
        <aside class="cart-summary">
            <h3>Order Summary</h3>
            <div class="summary-details">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>RM <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $item) { $total += $item['price'] * $item['qty']; }
                        echo number_format($total, 2); 
                    ?></span>
                </div>
                <div class="summary-row">
                    <span>Service Fee</span>
                    <span>Free</span>
                </div>
                <hr>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>RM <?= number_format($total, 2); ?></span>
                </div>
            </div>
            <a href="placeorder.php" class="place-order-btn-full">Confirm Order</a>
        </aside>
        <?php endif; ?>
    </section>
</div>
</body>
</html>