<!-- 
 Frontend: Qai 
 Backend: Amirah
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartServe - Staff Report</title>
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
    <div class="main-content staff-report-content">
    <div class="header">
        <div class="title">
            <h2>Reports</h2>
            <p>Generated: <?php echo date('d/m/Y'); ?></p>
        </div>
        <button onclick="window.print()" class="export-btn">
            <span class="material-symbols-outlined" style="font-size: 16px;">print</span> Print Report
        </button>
    </div>

    <form method="GET" action="" class="filters-container">
        <div class="filters-row">
            <div class="filter-group">
                <label>Report Type</label>
                <select name="report_type" class="filter-dropdown">
                    <option value="inventory">Sales Report</option>
                    <option value="sales">Sales Performance</option>
                    <option value="orders">Order Summary</option>
                </select>
            </div>

            <div class="filter-group">
                <label>From Date</label>
                <input type="date" name="date_from" class="filter-input">
            </div>

            <div class="filter-group">
                <label>To Date</label>
                <input type="date" name="date_to" class="filter-input">
            </div>

            <div class="filter-group">
                <label>&nbsp;</label> <button type="submit" class="filter-submit-btn">Apply Filter</button>
            </div>
        </div>
    </form>

    <div class="report-table-container">
    <table class="report-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Menu Item(s)</th>
                <th>Qty</th>
                <th>Amount Paid</th>
                <th>Status</th>
                <th>Staff</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Dummy data
            $sales_reports = [
                ['id' => 7721, 'date' => '11/01/2026', 'items' => 'Nasi Lemak Special', 'qty' => 2, 'paid' => 10.00, 'status' => 'Completed', 'staff' => 'Amirah'],
                ['id' => 7722, 'date' => '11/01/2026', 'items' => 'Teh Tarik Ais', 'qty' => 1, 'paid' => 2.50, 'status' => 'Completed', 'staff' => 'Qis'],
                ['id' => 7723, 'date' => '10/01/2026', 'items' => 'Mee Goreng Mamak', 'qty' => 3, 'paid' => 13.50, 'status' => 'Completed', 'staff' => 'Elya']
            ];

            foreach($sales_reports as $report): ?>
            <tr>
                <td>#<?php echo $report['id']; ?></td>
                <td><?php echo $report['date']; ?></td>
                <td><strong><?php echo $report['items']; ?></strong></td>
                <td><?php echo $report['qty']; ?></td>
                <td>RM <?php echo number_format($report['paid'], 2); ?></td>
                <td>
                    <span class="status-badge status-completed"><?php echo $report['status']; ?></span>
                </td>
                <td><?php echo $report['staff']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
</body>
</html>