<?php
$host = "localhost";
$user = "root";  // your database username
$pass = "";      // your database password
$dbname = "realestate";  // your database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>