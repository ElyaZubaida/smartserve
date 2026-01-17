<?php
session_start();

// Check if staff is logged in
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit;
}

// Check for the success flag immediately and clear it
$showUpdateSuccess = false;
if (isset($_SESSION['order_status_updated'])) {
    $showUpdateSuccess = true;
    unset($_SESSION['order_status_updated']);
    unset($_SESSION['success_message']);
}

// Check for error message
$showError = false;
$errorMessage = '';
if (isset($_SESSION['error_message'])) {
    $showError = true;
    $errorMessage = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Include database connection
include '../config/db_connect.php';

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No order ID provided");
}

$order_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch order details (added staff name who updated)
$query = "
    SELECT 
        o.order_ID, 
        o.order_date, 
        o.order_status, 
        o.order_totalAmount,
        o.updated_at,
        s.student_name,
        st.staffName AS updated_by
    FROM 
        orders o
    JOIN 
        students s ON o.student_ID = s.student_ID
    LEFT JOIN
        staff st ON o.staffID = st.staffID
    WHERE 
        o.order_ID = '$order_id'
";

$order_result = mysqli_query($conn, $query);

// Fetch order items (added request)
$items_query = "
    SELECT 
        m.menuName, 
        om.om_quantity, 
        m.menuPrice,
        om.request,
        (om.om_quantity * m.menuPrice) AS subtotal
    FROM 
        order_menu om
    JOIN 
        menus m ON om.menuID = m.menuID
    WHERE 
        om.order_ID = '$order_id'
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
                <h2>Order Details: #<?php echo htmlspecialchars($order['order_ID']); ?></h2>
                <p>Customer: <?php echo htmlspecialchars($order['student_name']); ?></p>
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
                                    <strong><?php echo htmlspecialchars($item['menuName']); ?></strong>
                                    <?php if (!empty($item['request'])): ?>
                                    <span class="special-request">
                                        <span class="material-symbols-outlined">notes</span>
                                        Request: <?php echo htmlspecialchars($item['request']); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($item['om_quantity']); ?></td>
                            <td>RM <?php echo number_format($item['menuPrice'], 2); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="order-status-card">
                <h3>Manage Status</h3>
                
                <?php 
                // Check if status is final (cannot be changed)
                $is_final = ($order['order_status'] == 'Completed' || $order['order_status'] == 'Cancelled');
                ?>
                
                <form method="POST" action="update_order_status.php">
                    <input type="hidden" name="order_ID" value="<?php echo $order['order_ID']; ?>">
                    <div class="profile-form-group">
                        <label>Current Status</label>
                        
                        <?php if ($is_final): ?>
                            <!-- Status is final - show disabled dropdown -->
                            <select class="input-field status-select" disabled>
                                <option selected><?php echo $order['order_status']; ?></option>
                            </select>
                            <p class="status-locked-msg">
                                <span class="material-symbols-outlined">lock</span>
                                This order is <?php echo strtolower($order['order_status']); ?> and cannot be changed.
                            </p>
                        <?php else: ?>
                            <!-- Status can be changed -->
                            <select name="status" class="input-field status-select">
                                <option value="Pending" <?php echo ($order['order_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Preparing" <?php echo ($order['order_status'] == 'Preparing') ? 'selected' : ''; ?>>Preparing</option>
                                <option value="Ready for Pickup" <?php echo ($order['order_status'] == 'Ready for Pickup') ? 'selected' : ''; ?>>Ready For Pickup</option>
                                <option value="Completed" <?php echo ($order['order_status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="Cancelled" <?php echo ($order['order_status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!$is_final): ?>
                        <button type="submit" class="btn-update">Update Status</button>
                    <?php endif; ?>
                </form>
                
                <div class="order-info-footer">
                    <p><strong>Order Time:</strong> <?php echo date('h:i A', strtotime($order['order_date'])); ?></p>
                    <p><strong>Total Amount:</strong> RM <?php echo number_format($order['order_totalAmount'], 2); ?></p>
                    
                    <!-- Display who last updated the order -->
                    <div class="updated-by">
                        <?php if (!empty($order['updated_by'])): ?>
                            <p><strong>Last Updated By:</strong> <?php echo htmlspecialchars($order['updated_by']); ?></p>
                            <p><strong>Updated At:</strong> <?php echo date('d M Y, h:i A', strtotime($order['updated_at'])); ?></p>
                        <?php else: ?>
                            <p><strong>Status:</strong> Not yet updated by staff</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="material-symbols-outlined">check_circle</span>
            <h2>Status Updated Successfully</h2>
            <button class="close-btn" onclick="closeModal()">Close</button>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content error">
            <span class="material-symbols-outlined">error</span>
            <h2 id="errorMessage">Error</h2>
            <button class="close-btn" onclick="closeErrorModal()">Close</button>
        </div>
    </div>

    <script>
        const statusSelect = document.querySelector('.status-select');

        function updateDropdownColor() {
            if (!statusSelect) return;
            statusSelect.classList.remove('status-pending', 'status-preparing', 'status-ready-for-pickup', 'status-completed', 'status-cancelled');
            const status = statusSelect.value.toLowerCase().replace(/\s+/g, '-'); 
            statusSelect.classList.add('status-' + status);
        }

        if (statusSelect) {
            window.onload = updateDropdownColor;
            statusSelect.addEventListener('change', updateDropdownColor);
        }

        function showSuccessModal() {
            document.getElementById('successModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('successModal').style.display = 'none';
        }

        function showErrorModal(message) {
            document.getElementById('errorMessage').innerText = message;
            document.getElementById('errorModal').style.display = 'flex';
        }

        function closeErrorModal() {
            document.getElementById('errorModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const successModal = document.getElementById('successModal');
            const errorModal = document.getElementById('errorModal');
            if (event.target == successModal) {
                successModal.style.display = 'none';
            }
            if (event.target == errorModal) {
                errorModal.style.display = 'none';
            }
        }
    </script>

    <?php if ($showUpdateSuccess): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showSuccessModal();
        });
    </script>
    <?php endif; ?>

    <?php if ($showError): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showErrorModal('<?php echo addslashes($errorMessage); ?>');
        });
    </script>
    <?php endif; ?>

</body>
</html>

<?php
mysqli_close($conn);
?>