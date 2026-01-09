<?php
// Database connection
include('../databaseconnect.php');

// Check connection
if (!$connection) {
    die('Database connection failed');
}

// Fetch data
$query = "SELECT menu_id, name, price, description FROM menu";
$result = mysqli_query($connection, $query);

// Generate HTML for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #228B22;
            margin-bottom: 10px;
        }
        .report-date {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #228B22;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .price {
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>SmartServe Menu Report</h1>
    <div class="report-date">Generated on: ' . date('d/m/Y H:i:s') . '</div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Menu ID</th>
                <th style="width: 25%;">Menu Name</th>
                <th style="width: 15%;">Menu Type</th>
                <th style="width: 40%;">Description</th>
                <th style="width: 10%;">Price</th>
            </tr>
        </thead>
        <tbody>';

$counter = 1;
while($row = mysqli_fetch_assoc($result)) {
    $menu_type = (strpos(strtolower($row['name']), 'teh') !== false || 
                  strpos(strtolower($row['name']), 'ais') !== false) ? 'Beverages' : 'Breakfast';
    
    $html .= '<tr>
                <td>' . str_pad($counter++, 3, '0', STR_PAD_LEFT) . '</td>
                <td>' . htmlspecialchars($row['name']) . '</td>
                <td>' . $menu_type . '</td>
                <td>' . htmlspecialchars($row['description']) . '</td>
                <td class="price">RM ' . number_format($row['price'], 2) . '</td>
              </tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

mysqli_close($connection);

// Output as PDF using DomPDF-like approach or use browser print
// For simple approach, we'll use browser's print to PDF functionality
header('Content-Type: text/html; charset=utf-8');
echo $html;
echo '
<script>
    window.onload = function() {
        window.print();
    }
</script>';
?>