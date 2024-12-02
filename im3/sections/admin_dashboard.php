<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    // Redirect non-admins and non-logged-in users to the login page
    header("Location: ../auth.php");
    exit;
}

// Include your database connection file
include __DIR__ . '/../DatabaseConnection.php';

// Fetch users from the database
$users_query = "SELECT user_id, username, role, created_at FROM users";
$users_result = mysqli_query($conn, $users_query);

// Fetch all reservations from the database
$reservations_query = "SELECT reservation_id, room_id, check_in_date, check_out_date
                      FROM reservations
                      ORDER BY check_in_date DESC";
$reservations_result = $conn->query($reservations_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="wrapper">
        <header>
            <h1>Admin Dashboard</h1>
            <nav>
                <ul>
                    
                </ul>
            </nav>
        </header>

        <div class="dashboard-content">
            <!-- Users Section -->
            <section>
                <h2>Website Users</h2>
                <table>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Created At</th>
                    </tr>
                    <?php
                    while ($row = mysqli_fetch_assoc($users_result)) {
                        echo "<tr>
                                <td>{$row['username']}</td>
                                <td>{$row['role']}</td>
                                <td>{$row['created_at']}</td>
                              </tr>";
                    }
                    ?>
                </table>
            </section>

            <!-- User Reservations Section -->
            <section>
                <h2>User Reservations</h2>
                <?php if ($reservations_result->num_rows > 0): ?>
                    <table>
                        <tr>
                            <th>Reservation ID</th>
                            <th>Room ID</th>
                            <th>Check-in Date</th>
                            <th>Check-out Date</th>
                        </tr>
                        <?php while ($reservation = $reservations_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['reservation_id']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['room_id']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['check_in_date']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['check_out_date']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>No reservations found.</p>
                <?php endif; ?>
            </section>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
