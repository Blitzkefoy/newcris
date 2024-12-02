<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: auth.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "u895272250_crisinn1";
$password = "Yelnik123!";
$dbname = "u895272250_crisinn";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$user_query = "SELECT username, first_name, last_name, email, phone_no FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Handle password change
$password_change_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch the current password from the database
    $password_query = "SELECT password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($password_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $password_result = $stmt->get_result();
    $password_data = $password_result->fetch_assoc();
    $stored_password = $password_data['password'];

    // Validate current password
    if (!password_verify($current_password, $stored_password)) {
        $password_change_message = "<p style='color: red;'>Current password is incorrect.</p>";
    } elseif ($new_password !== $confirm_password) {
        $password_change_message = "<p style='color: red;'>New password and confirm password do not match.</p>";
    } elseif (strlen($new_password) < 6) {
        $password_change_message = "<p style='color: red;'>New password must be at least 6 characters long.</p>";
    } else {
        // Hash the new password and update the database
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_password_query = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_password_query);
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            $password_change_message = "<p style='color: green;'>Password changed successfully.</p>";
        } else {
            $password_change_message = "<p style='color: red;'>Failed to update password. Please try again.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>My Account</h2>
        <div class="card p-4">
            <h4>Account Details</h4>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user_data['username']); ?></p>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($user_data['first_name']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user_data['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user_data['phone_no']); ?></p>
        </div>

        <div class="card p-4 mt-4">
            <h4>Change Password</h4>
            <?php echo $password_change_message; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
            </form>
        </div>

        <a href="index.php" class="btn btn-secondary mt-4">Go Back</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
