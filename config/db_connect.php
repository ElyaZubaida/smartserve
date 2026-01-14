<?php
$host = 'localhost';
$username = 'root';  // Usually 'root' in Laragon
$password = '';      // Usually blank in Laragon
$database = 'smartservedb';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>