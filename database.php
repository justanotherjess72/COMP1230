<?php
$servername = "localhost:3307";
$username = "admin";
$password = "password1";
$dbname = "group12database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

