<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: auth.php");
    exit;
}

// Database connection details
$servername = "localhost";
$username = "u895272250_crisinn1";
$password = "Yelnik123!";
$dbname = "u895272250_crisinn";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle reservation deletion
if (isset($_GET['delete_reservation'])) {
    $reservation_id = intval($_GET['delete_reservation']); // Sanitize the input
    $guest_id = $_SESSION['user_id'];

    // Check if the reservation belongs to the user
    $checkStmt = $conn->prepare("SELECT reservation_id FROM reservations WHERE reservation_id = ? AND guest_id = ?");
    $checkStmt->bind_param("ii", $reservation_id, $guest_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Reservation belongs to the user, delete it
        $deleteStmt = $conn->prepare("DELETE FROM reservations WHERE reservation_id = ?");
        $deleteStmt->bind_param("i", $reservation_id);
        if ($deleteStmt->execute()) {
            echo "<script>alert('Reservation deleted successfully.');</script>";
        } else {
            echo "<script>alert('Error deleting reservation. Please try again later.');</script>";
        }
        $deleteStmt->close();
    } else {
        echo "<script>alert('Invalid reservation or you do not have permission to delete this reservation.');</script>";
    }
    $checkStmt->close();
}

// Fetch user reservations
$guest_id = $_SESSION['user_id'];

$reservationsSql = "
    SELECT r.reservation_id, r.room_id, ro.room_type, r.check_in_date, r.check_out_date, r.check_in_time, r.check_out_time
    FROM reservations r
    JOIN rooms ro ON r.room_id = ro.room_id
    WHERE r.guest_id = ?";
$stmt = $conn->prepare($reservationsSql);
$stmt->bind_param("i", $guest_id);
$stmt->execute();
$reservationsResult = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Your Reservations</h2>
        <?php if ($reservationsResult->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Room ID</th>
                        <th>Room Type</th>
                        <th>Check-in Date</th>
                        <th>Check-in Time</th>
                        <th>Check-out Date</th>
                        <th>Check-out Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $reservationsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['room_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['check_in_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['check_in_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['check_out_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['check_out_time']); ?></td>
                            <td>
                                <!-- Delete Button -->
                                <a href="user_reservations.php?delete_reservation=<?php echo htmlspecialchars($row['reservation_id']); ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this reservation?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No reservations found.</p>
        <?php endif; ?>
       
    </div>
</body>
</html>
<?php
$conn->close();
?>
