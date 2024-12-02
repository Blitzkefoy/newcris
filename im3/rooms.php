<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: auth.php");  // Redirect to login page if not logged in
    exit;
}

// Database connection details
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .room-section {
            padding: 40px 20px;
            text-align: center;
        }
        .room-grid {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .room {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        .room img {
            width: 100%;
            border-radius: 8px;
        }
        .room h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .room p {
            margin: 10px 0;
        }
        .btn-book {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .btn-book:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <section class="room-section">
            <h1>Our Rooms</h1>
            <div class="room-grid">
                <div class="room">
                    <h2>Single Room</h2>
                    <img src="images/single_room.jpg" alt="Single Room">
                    <p>Perfect for solo travelers.</p>
                    <p><strong>Price:</strong> PHP 1000.00 per night</p>
                    <a href="reservation.php?room_type=Single Room" class="btn-book">Book Now</a>
                </div>
                <div class="room">
                    <h2>Double Room</h2>
                    <img src="images/double_room.jpg" alt="Double Room">
                    <p>Ideal for couples or friends.</p>
                    <p><strong>Price:</strong> PHP 1500.00 per night</p>
                    <a href="reservation.php?room_type=Double Room" class="btn-book">Book Now</a>
                </div>
                <div class="room">
                    <h2>Suite Room</h2>
                    <img src="images/suite_room.jpg" alt="Suite Room">
                    <p>Luxury suite for families.</p>
                    <p><strong>Price:</strong> PHP 2000.00 per night</p>
                    <a href="reservation.php?room_type=Suite Room" class="btn-book">Book Now</a>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
