<?php
session_start();

// Ensure only admins can access this page
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth.php"); // Redirect to login page if not logged in
    exit;
}

// Include the database connection
include __DIR__ . '/../DatabaseConnection.php';  // Ensure this path is correct

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch all users from the database
$users_query = "SELECT user_id, username, role, guest_id, first_name, last_name, email, phone_no, created_at FROM users";
$users_result = mysqli_query($conn, $users_query);

if (!$users_result) {
    die('Query failed: ' . mysqli_error($conn));  // Display an error if the query fails
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Manage Users</h1>
    </header>

    <div class="dashboard-content">
        <section>
            <h2>Manage Website Users</h2>

            <!-- Show success or error messages when actions are performed -->
            <?php if (isset($_GET['status'])): ?>
                <?php if ($_GET['status'] === 'deleted'): ?>
                    <p style="color: green;">User deleted successfully.</p>
                <?php elseif ($_GET['status'] === 'admin_error'): ?>
                    <p style="color: red;">Error: You cannot delete an admin user.</p>
                <?php elseif ($_GET['status'] === 'error'): ?>
                    <p style="color: red;">Error deleting user. Please try again.</p>
                <?php elseif ($_GET['status'] === 'not_found'): ?>
                    <p style="color: red;">User not found. Please check the user ID.</p>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Display the user list -->
            <table border="1">
                <tr>
                    <th>Guest ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Role</th>
                    <th>Created At</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($users_result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['guest_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </section>
    </div>
</body>
</html>
