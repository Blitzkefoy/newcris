<?php
$servername = "localhost";
$username = "u895272250_crisinn1";
$password = "Yelnik123!";
$dbname = "u895272250_crisinn";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>
