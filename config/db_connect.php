<?php
$host = 'localhost';
$username = 'root';  // Usually 'root' in Laragon
$password = '';      // Usually blank in Laragon
$database = 'smartservedb';
$port = '3307';

// Use Malaysia time across the app
date_default_timezone_set('Asia/Kuala_Lumpur');

// Create connection
$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
