<?php
include 'config/db_connect.php';
// Updated Query: Check for BOTH availability = 0 OR is_deleted = 1
$query = "SELECT menuID FROM menus WHERE (menuAvailability = 0 OR is_deleted = 1)";
$result = $conn->query($query);
$unavailableIDs = [];
while($row = $result->fetch_assoc()) {
    $unavailableIDs[] = (int)$row['menuID'];
}
echo json_encode($unavailableIDs);
?>