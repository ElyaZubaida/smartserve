<!-- 
 Frontend: Elya 
 Backend: ? 
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navigation Bar -->
    <header>
        <div class="menubar">
            <!-- Logo -->
            <div class="logo">
                <img src="logo.png" alt="Smart Serve Logo"> <!-- Replace with your logo image -->
            </div>

            <!-- Menu Links -->
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
      <!-- Cart Section -->
    <section class="cart">
        <h2>Your Cart</h2>

        <!-- Cart Items -->
        <div class="cart-items">
            <?php if (count($_SESSION['cart']) > 0): ?>
                <form action="cart.php" method="POST">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <img src="img/<?= $item['image']; ?>" alt="<?= $item['name']; ?>">
                            </div>
                            <div class="cart-item-details">
                                <h3><?= $item['name']; ?></h3>
                                <p>Price: RM <?= $item['price']; ?></p>
                                <p>Quantity: 
                                    <input type="number" name="qty" value="<?= $item['qty']; ?>" min="1" max="99" onchange="this.form.submit()">
                                </p>
                                <input type="hidden" name="pid" value="<?= $item['pid']; ?>">
                                <input type="hidden" name="remove_item" value="<?= $item['pid']; ?>">
                                <button type="submit" class="remove-btn">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </form>

                <!-- Total Price Calculation -->
                <div class="total-price">
                    <h3>Total: RM 
                        <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] as $item) {
                            $total += $item['price'] * $item['qty'];
                        }
                        echo $total;
                        ?>
                    </h3>
                </div>

                <!-- Proceed to Checkout -->
                <div class="checkout-btn">
                    <a href="placeorder.php" class="btn">Confirm Order</a>
                </div>

            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <p>Smart Serve - Your Food Ordering System</p>
    </footer>

</body>
</html>