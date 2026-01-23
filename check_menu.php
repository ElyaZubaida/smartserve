<?php
include 'config/db_connect.php';

// We fetch the most recent 'updated_at' timestamp or the count of items
$query = "SELECT COUNT(*) as total, MAX(updated_at) as last_update FROM menus WHERE is_deleted = 0";
$result = $conn->query($query);
$data = $result->fetch_assoc();

// Return as a simple string for the JavaScript to compare
echo $data['total'] . "-" . $data['last_update'];
?>