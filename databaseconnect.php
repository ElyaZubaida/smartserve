<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "smartserve";
$port = "3306";

$connection = mysqli_connect($servername, $username, $password, $dbname, $port);
if (!$connection) {
    echo 'Database connection failed!';
}
