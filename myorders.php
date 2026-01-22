<!-- 
 Frontend: Insyirah 
 Backend: Aleesya 
 -->

<?php
    session_start();
    date_default_timezone_set('Asia/Kuala_Lumpur');
    include 'config/db_connect.php';

    // Check if user is logged in
    if (!isset($_SESSION['student_id'])) {
        header('Location: login.php');
        exit();
    }

    $student_id = $_SESSION['student_id'];

    // Fetch all orders for this student
    $query = "SELECT * FROM orders 
            WHERE student_ID = ? 
            ORDER BY order_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SmartServe - My Orders</title>
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
                        <li><a href="ai.php"><span class="material-symbols-outlined">psychology</span> Food Recommendation</a></li>
                        <li><a href="myorders.php"><span class="material-symbols-outlined">receipt_long</span> Orders</a></li>
                        <li><a href="cart.php"><span class="material-symbols-outlined">shopping_cart</span> Cart</a></li>
                        <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                        <li><a href="logout.php"><span class="material-symbols-outlined">logout</span></a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <div class="orders-container">
            <div class="section-header-box">
                <div class="header-title-group">
                    <span class="material-symbols-outlined">receipt_long</span>
                    <h1>My Order History</h1>
                </div>
                <p>Track your past meals and reorder your favorites easily.</p>
            </div>

            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <?php
                        $status_class = strtolower($order['order_status']); // e.g., pending, preparing, ready, completed
                        $order_date = date('d/m/Y, h:i A', strtotime($order['order_date']));
                        $completed_datetime = null;

                        if (
                            strtolower(trim($order['order_status'])) === 'completed'
                            && !empty($order['completed_date'])
                        ) {
                            $completed_datetime = date(
                                'd/m/Y, h:i A',
                                strtotime($order['completed_date'])
                            );
                        }

                        $cancel_disabled = strtolower($order['order_status']) === 'completed' || strtolower($order['order_status']) === 'cancelled' ? 'disabled btn-dimmed' : '';
                        $cancel_title = strtolower($order['order_status']) === 'completed' ? 'Completed orders cannot be cancelled' : '';
                    ?>
                    <div class="order-card <?php echo ($status_class === 'completed') ? 'order-completed-style' : ''; ?>">
                        <div class="order-left">
                            <div class="order-info-group">
                                <span class="order-number">Order #<?php echo $order['order_ID']; ?></span>
                                <div class="status-badge <?php echo $status_class; ?>">
                                    <?php
                                        switch ($status_class) {
                                            case 'pending': echo '<span class="material-symbols-outlined">check_circle</span>'; break;
                                            case 'preparing': echo '<span class="material-symbols-outlined">restaurant</span>'; break;
                                            case 'ready': echo '<span class="material-symbols-outlined">shopping_bag</span>'; break;
                                            case 'completed': echo '<span class="material-symbols-outlined">task_alt</span>'; break;
                                        }
                                    ?>
                                    <?php echo htmlspecialchars($order['order_status']); ?>
                                </div>
                            </div>
                            <span class="order-date">Ordered on <?php echo $order_date; ?></span>

                            <?php if ($completed_datetime): ?>
                                <span class="order-completed-time">
                                    Completed on <?php echo $completed_datetime; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="order-right-stack">
                            <button class="order-details-btn" onclick="location.href='orderdetails.php?order_id=<?php echo $order['order_ID']; ?>'">
                                <span class="material-symbols-outlined">visibility</span> Details
                            </button>
                            <button class="cancel-order-btn <?php echo $cancel_disabled; ?>" 
                                    <?php echo $cancel_disabled ? 'disabled' : ''; ?> 
                                    title="<?php echo $cancel_title; ?>"
                                    onclick="showCancelModal(<?php echo $order['order_ID']; ?>)">
                                <span class="material-symbols-outlined"><?php echo $cancel_disabled ? 'block' : 'delete'; ?></span> Cancel
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have no orders yet. <a href="menu.php">Start ordering!</a></p>
            <?php endif; ?>
        </div>

        <!-- Confirmation Modal -->
        <div id="confirmModal" class="modal">
            <div class="modal-content">
                <div class="modal-icon error">
                    <span class="material-symbols-outlined" style="font-size: 64px;">warning</span>
                </div>
                <h2>Cancel Order?</h2>
                <p>Are you sure you want to cancel this order? This action cannot be undone.</p>
                <div class="modal-actions">
                    <button class="modal-btn-cancel" onclick="closeConfirmModal()">No, Keep Order</button>
                    <button class="modal-btn-confirm" onclick="confirmCancelOrder()">Yes, Cancel Order</button>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div id="successModal" class="modal">
            <div class="modal-content">
                <div class="modal-icon success">
                    <span class="material-symbols-outlined" style="font-size: 64px;">check_circle</span>
                </div>
                <h2>Order Cancelled</h2>
                <p>Your order has been successfully cancelled.</p>
                <button class="modal-btn" onclick="closeSuccessModal()">OK</button>
            </div>
        </div>

        <!-- Error Modal -->
        <div id="errorModal" class="modal">
            <div class="modal-content">
                <div class="modal-icon error">
                    <span class="material-symbols-outlined" style="font-size: 64px;">error</span>
                </div>
                <h2>Error</h2>
                <p id="errorMessage">Unable to cancel order. Please try again.</p>
                <button class="modal-btn" onclick="closeErrorModal()">OK</button>
            </div>
        </div>

        <script>
            let orderToCancel = null;

            function showCancelModal(orderId) {
                orderToCancel = orderId;
                console.log('Order to cancel:', orderToCancel); // Debug log
                document.getElementById('confirmModal').style.display = 'block';
            }

            function closeConfirmModal() {
                document.getElementById('confirmModal').style.display = 'none';
                // Don't reset orderToCancel here - we might need it
            }

            function closeSuccessModal() {
                document.getElementById('successModal').style.display = 'none';
                orderToCancel = null; // Reset here instead
                location.reload(); // Reload to show updated order status
            }

            function closeErrorModal() {
                document.getElementById('errorModal').style.display = 'none';
                orderToCancel = null; // Reset here instead
            }

            function confirmCancelOrder() {
                if (!orderToCancel) {
                    console.error('No order ID to cancel');
                    document.getElementById('errorMessage').textContent = 'No order selected';
                    document.getElementById('errorModal').style.display = 'block';
                    return;
                }

                console.log('Cancelling order ID:', orderToCancel); // Debug log

                // Store the order ID before closing modal
                const orderIdToCancel = orderToCancel;

                // Close confirmation modal
                closeConfirmModal();

                // Send AJAX request to cancel order
                const formData = new FormData();
                formData.append('order_id', orderIdToCancel);

                console.log('Sending order_id:', orderIdToCancel); // Debug log

                fetch('cancelorder.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'  // This tells the server it's an AJAX request
                    },
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status); // Debug log
                    // Check if response is ok
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data); // Debug log
                    if (data.success) {
                        // Show success modal
                        document.getElementById('successModal').style.display = 'block';
                    } else {
                        // Show error modal with the message from server
                        document.getElementById('errorMessage').textContent = data.message || 'Unable to cancel order. Please try again.';
                        document.getElementById('errorModal').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('errorMessage').textContent = 'An error occurred. Please try again.';
                    document.getElementById('errorModal').style.display = 'block';
                });
            }

            // Close modal when clicking outside of it
            window.onclick = function(event) {
                const confirmModal = document.getElementById('confirmModal');
                const successModal = document.getElementById('successModal');
                const errorModal = document.getElementById('errorModal');
                
                if (event.target == confirmModal) {
                    closeConfirmModal();
                }
                if (event.target == successModal) {
                    closeSuccessModal();
                }
                if (event.target == errorModal) {
                    closeErrorModal();
                }
            }
        </script>
    </body>
</html>