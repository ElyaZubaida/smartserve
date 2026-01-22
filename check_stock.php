<?php
include 'config/db_connect.php';
$query = "SELECT menuID FROM menus WHERE menuAvailability = 0 AND is_deleted = 0";
$result = $conn->query($query);
$outOfStockIDs = [];
while($row = $result->fetch_assoc()) {
    $outOfStockIDs[] = (int)$row['menuID'];
}
echo json_encode($outOfStockIDs);
?>