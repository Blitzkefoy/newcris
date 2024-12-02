<?php
session_start();
ini_set('session.gc_maxlifetime', 3600);  // 1-hour session timeout
session_set_cookie_params(3600);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include 'DatabaseConnection.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: auth.php");
    exit;
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die("Error: 'user_id' is missing in the session. Please log in again.");
}

// Fetch user_id and validate
$user_id = (int)$_SESSION['user_id'];
$stmt = $conn->prepare("SELECT guest_id FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $guest_data = $result->fetch_assoc();
    $guest_id = $guest_data['guest_id'];

    if (empty($guest_id)) {
        $guest_id = $user_id; // Assume guest_id should match user_id if missing
        $updateStmt = $conn->prepare("UPDATE users SET guest_id = ? WHERE user_id = ?");
        $updateStmt->bind_param("ii", $guest_id, $user_id);
        if ($updateStmt->execute()) {
            echo "Guest ID populated successfully: $guest_id<br>";
        } else {
            echo "Failed to populate Guest ID.<br>";
        }
        $updateStmt->close();
    }
} else {
    die("User not found in the database.");
}
$stmt->close();

// Fetch available room types, prices, and availability (grouped by room_type)
$room_types = [];
$sql = "
    SELECT r.room_type, r.price, COUNT(r.room_id) AS total_rooms,
           COUNT(r.room_id) - COUNT(res.room_id) AS available_rooms
    FROM rooms r
    LEFT JOIN reservations res
    ON r.room_id = res.room_id
    AND (NOW() BETWEEN res.check_in_date AND res.check_out_date)
    GROUP BY r.room_type, r.price
";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $room_types[] = [
            'room_type' => $row['room_type'],
            'price' => $row['price'],
            'available' => $row['available_rooms']
        ];
    }
}

// Reservation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        die("Error: You must log in to make a reservation.");
    }

    $guest_id = $_SESSION['guest_id'] ?? null;
    if (!$guest_id) {
        die("Error: 'guest_id' is missing. Please log in again.");
    }

    $room_type = $_POST['room_type'] ?? null;
    $check_in_date = $_POST['check_in_date'] ?? null;
    $check_out_date = $_POST['check_out_date'] ?? null;
    $check_in_time = $_POST['check_in_time'] ?? null;
    $check_out_time = $_POST['check_out_time'] ?? null;

    if (!$room_type || !$check_in_date || !$check_out_date || !$check_in_time || !$check_out_time) {
        die("Error: Please fill in all required fields.");
    }

    $stmt = $conn->prepare("SELECT room_id FROM rooms WHERE room_type = ? 
                            AND room_id NOT IN (SELECT room_id FROM reservations
                            WHERE (? BETWEEN check_in_date AND check_out_date)
                            OR (? BETWEEN check_in_date AND check_out_date)) LIMIT 1");
    $stmt->bind_param("sss", $room_type, $check_in_date, $check_out_date);
    $stmt->execute();
    $room_result = $stmt->get_result();

    if ($room_result->num_rows > 0) {
        $room_id = $room_result->fetch_assoc()['room_id'];

        $stmt = $conn->prepare("INSERT INTO reservations (guest_id, room_id, check_in_date, check_out_date, check_in_time, check_out_time)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $guest_id, $room_id, $check_in_date, $check_out_date, $check_in_time, $check_out_time);

        if ($stmt->execute()) {
            echo "<script>alert('Reservation successful! Room ID: $room_id has been booked.');
                  window.location.href = 'index.php?page=home';</script>";
        } else {
            echo "<script>alert('Error processing your reservation. Please try again later.');</script>";
        }
    } else {
        echo "<script>alert('No rooms available for the selected type and dates.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Make a Reservation</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="room_type" class="form-label">Room Type</label>
                <select id="room_type" name="room_type" class="form-select" required>
                    <?php foreach ($room_types as $room): ?>
                        <option value="<?php echo htmlspecialchars($room['room_type']); ?>" <?php echo $room['available'] <= 0 ? 'disabled' : ''; ?>>
                            <?php echo htmlspecialchars($room['room_type']) . " - ₱" . htmlspecialchars($room['price']) . " per night (" . $room['available'] . " available)"; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="check_in_date" class="form-label">Check-in Date</label>
                <input type="date" id="check_in_date" name="check_in_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="check_in_time" class="form-label">Check-in Time</label>
                <input type="time" id="check_in_time" name="check_in_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="check_out_date" class="form-label">Check-out Date</label>
                <input type="date" id="check_out_date" name="check_out_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="check_out_time" class="form-label">Check-out Time</label>
                <input type="time" id="check_out_time" name="check_out_time" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Reserve</button>
        </form>

        <!-- Back to Rooms Button -->
        <div class="mt-3">
            <a href="index.php?page=rooms" class="btn btn-secondary">Back to Rooms</a>
        </div>
    </div>
</body>
</html>
