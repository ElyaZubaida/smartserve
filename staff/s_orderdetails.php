<?php
session_start();
// Include database connection
include '../config/db_connect.php';

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No order ID provided");
}

$order_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch order details
$query = "
    SELECT 
        `ORDER`.`ORDER_ID`, 
        `ORDER`.`ORDER_DATE`, 
        `ORDER`.`ORDER_STATUS`, 
        `ORDER`.`ORDER_TOTAMOUNT`,
        `STUDENT`.`STUDENT_NAME`
    FROM 
        `ORDER`
    JOIN 
        `STUDENT` ON `ORDER`.`STUDENT_ID` = `STUDENT`.`STUDENT_ID`
    WHERE 
        `ORDER`.`ORDER_ID` = '$order_id'
";

$order_result = mysqli_query($conn, $query);

// Fetch order items
$items_query = "
    SELECT 
        `MENU`.`MENU_NAME`, 
        `ORDER_MENU`.`OM_QUANTITY`, 
        `MENU`.`MENU_PRICE`,
        (`ORDER_MENU`.`OM_QUANTITY` * `MENU`.`MENU_PRICE`) AS subtotal
    FROM 
        `ORDER_MENU`
    JOIN 
        `MENU` ON `ORDER_MENU`.`MENU_ID` = `MENU`.`MENU_ID`
    WHERE 
        `ORDER_MENU`.`ORDER_ID` = '$order_id'
";

$items_result = mysqli_query($conn, $items_query);

// Check if queries were successful
if (!$order_result || !$items_result) {
    die("Query failed: " . mysqli_error($conn));
}

$order = mysqli_fetch_assoc($order_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Order Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="sastyle.css">
    <style>
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: #2e7d32;
        color: white;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .modal-content h2 {
        margin-bottom: 20px;
    }

    .close-btn {
        background-color: white;
        color: #2e7d32;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 15px;
    }
</style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-top">
            <div class="logo-container">
                <img src="../img/logo.png" alt="SmartServe Logo">
                <h3>Smart<span>Serve</span></h3>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li><a href="dashboard.php"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                    <li><a href="menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li class="active"><a href="order_management.php"><span class="material-symbols-outlined">order_approve</span> Orders</a></li>
                    <li><a href="report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
                    <li class="nav-divider"></li>
                    <li><a href="profile.php"><span class="material-symbols-outlined">account_circle</span> Profile</a></li>
                    <li><a href="../logout.php" class="logout-link"><span class="material-symbols-outlined">logout</span> Log Out</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Main content area -->
    <div class="main-content orders-menu-content">
        <div class="header">
            <div class="title">
                <h2>Order Details: #<?php echo htmlspecialchars($order['ORDER_ID']); ?></h2>
                <p>Customer: <?php echo htmlspecialchars($order['STUDENT_NAME']); ?></p>
            </div>
            <a href="order_management.php" class="btn-back">
                <span class="material-symbols-outlined">arrow_back</span> Back to List
            </a>
        </div>

        <div class="order-details-grid">
            <div class="order-items-card">
                <h3>Items Ordered</h3>
                <table class="details-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        while ($item = mysqli_fetch_assoc($items_result)) {
                            $total += $item['subtotal'];
                        ?>
                        <tr>
                            <td>
                                <div class="item-info">
                                    <strong><?php echo htmlspecialchars($item['MENU_NAME']); ?></strong>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($item['OM_QUANTITY']); ?></td>
                            <td>RM <?php echo number_format($item['MENU_PRICE'], 2); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="order-status-card">
                <h3>Manage Status</h3>
                <form method="POST" action="update_order_status.php">
                    <input type="hidden" name="order_id" value="<?php echo $order['ORDER_ID']; ?>">
                    <div class="profile-form-group">
                        <label>Current Status</label>
                        <select name="status" class="input-field status-select">
                            <option value="Pending" <?php echo ($order['ORDER_STATUS'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="Preparing" <?php echo ($order['ORDER_STATUS'] == 'Preparing') ? 'selected' : ''; ?>>Preparing</option>
                            <option value="Ready for Pickup" <?php echo ($order['ORDER_STATUS'] == 'Ready for Pickup') ? 'selected' : ''; ?>>Ready For Pickup</option>
                            <option value="Completed" <?php echo ($order['ORDER_STATUS'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="Cancelled" <?php echo ($order['ORDER_STATUS'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-update">Update Status</button>
                </form>
                
                <div class="order-info-footer">
                    <p><strong>Order Time:</strong> <?php echo date('h:i A', strtotime($order['ORDER_DATE'])); ?></p>
                    <p><strong>Total Amount:</strong> RM <?php echo number_format($order['ORDER_TOTAMOUNT'], 2); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const statusSelect = document.querySelector('.status-select');

        function updateDropdownColor() {
            statusSelect.classList.remove('status-pending', 'status-preparing', 'status-ready', 'status-completed', 'status-cancelled');
            const status = statusSelect.value.toLowerCase().replace(/\s+/g, '-'); 
            statusSelect.classList.add('status-' + status);
        }

        window.onload = updateDropdownColor;
        statusSelect.addEventListener('change', updateDropdownColor);
    </script>

    <?php
// Check for success message
if (isset($_SESSION['success_message'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showSuccessModal('" . $_SESSION['success_message'] . "');
        });
    </script>";
    // Clear the session message
    unset($_SESSION['success_message']);
}
?>

<div id="successModal" class="modal">
    <div class="modal-content">
        <h2>Status Updated Successfully</h2>
        <button class="close-btn" onclick="closeModal()">Close</button>
    </div>
</div>

<script>
    function showSuccessModal(message) {
        const modal = document.getElementById('successModal');
        modal.style.display = 'flex';
    }

    function closeModal() {
        const modal = document.getElementById('successModal');
        modal.style.display = 'none';
    }

    // Close modal if clicked outside
    window.onclick = function(event) {
        const modal = document.getElementById('successModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>