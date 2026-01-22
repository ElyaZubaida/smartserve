<?php
include 'config/db_connect.php';

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $query = "SELECT order_status FROM orders WHERE order_ID = $order_id";
    $result = $conn->query($query);
    
    if ($row = $result->fetch_assoc()) {
        echo trim($row['order_status']);
    }
}
?>