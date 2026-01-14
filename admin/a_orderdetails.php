<!-- 
 Frontend: Elya 
 Backend: Amirah
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Order Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="../staff/sastyle.css">
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
                    <li class="active"><a href="a_dashboard.php"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                    <li><a href="a_menu_management.php"><span class="material-symbols-outlined">restaurant_menu</span> Menu Management</a></li>
                    <li><a href="a_order_management.php"><span class="material-symbols-outlined">order_approve</span> Order Management</a></li>
                    <li><a href="user_management.php"><span class="material-symbols-outlined">manage_accounts</span> User Management</a></li>
                    <li><a href="a_report.php"><span class="material-symbols-outlined">monitoring</span> Reports</a></li>
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
                <h2>Order Details: #7721</h2>
            <p>Customer: Qai binti Yuyu</p>
        </div>
        <a href="a_order_management.php" class="btn-back">
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
        <tr>
            <td>
                <div class="item-info">
                    <strong>Nasi Lemak Special</strong>
                    <span class="special-request">
                        <span class="material-symbols-outlined">notes</span>
                        Request: Taknak sayur
                    </span>
                </div>
            </td>
            <td>2</td>
            <td>RM 10.00</td>
        </tr>
        <tr>
            <td>
                <div class="item-info">
                    <strong>Teh Tarik Ais</strong>
                    <span class="special-request">
                        <span class="material-symbols-outlined">notes</span>
                        Request: Kurang gula
                    </span>
                </div>
            </td>
            <td>1</td>
            <td>RM 2.50</td>
        </tr>
    </tbody>
</table>
        </div>

        <div class="order-status-card">
            <h3>Manage Status</h3>
            <form method="POST" action="update_status.php">
                <div class="profile-form-group">
                    <label>Current Status</label>
                    <select name="status" class="input-field status-select">
                        <option value="Pending">Pending</option>
                        <option value="Preparing" selected>Preparing</option>
                        <option value="Ready">Ready For Pickup</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn-update">Update Status</button>
            </form>
            
            <div class="order-info-footer">
                <p><strong>Order Time:</strong> 09:30 AM</p>
                <p><strong>Pickup Time:</strong> 11:30 AM</p>
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
</body>
</html>