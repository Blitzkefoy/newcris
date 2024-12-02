<?php
session_start();
ini_set('session.gc_maxlifetime', 3600);  // 1-hour session timeout
session_set_cookie_params(3600);
include 'DatabaseConnection.php';  // Ensure the path to your DatabaseConnection.php is correct

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: auth.php");  // Redirect to login page if not logged in
    exit;
}

// Determine the user's role and default page
$page = isset($_GET['page']) ? $_GET['page'] : ($_SESSION['role'] === 'admin' ? 'admin_dashboard' : 'user_dashboard');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cris Inn Resort</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <ul>
                <!-- Home link -->
                <li><a href="index.php?page=home">Home</a></li>

                <?php
                // Navigation based on user role
                if ($_SESSION['role'] === 'admin') {
                    echo '<li><a href="index.php?page=admin_dashboard">Admin Dashboard</a></li>';
                    echo '<li><a href="index.php?page=manage_users">Manage Users</a></li>';
                } elseif ($_SESSION['role'] === 'user') {
                    echo '<li><a href="index.php?page=rooms">Rooms</a></li>';
                    echo '<li><a href="index.php?page=user_reservations">My Reservations</a></li>';
                    echo '<li><a href="user_account.php">My Account</a></li>';
                    echo '<li><a href="index.php?page=contact">Contact</a></li>';
                }

                // Common logout link
                echo '<li><a href="auth_logout.php">Logout</a></li>';
                ?>
            </ul>
        </nav>
    </header>

    <main>
        <?php
        // Load the appropriate content based on the 'page' parameter
        switch ($page) {
            case 'home':
                include 'sections/home.php';
                break;
            case 'rooms':
                include 'rooms.php';
                break;
            case 'reservation':
                include 'reservation.php';
                break;
            case 'user_reservations':
                include 'user_reservations.php';
                break;
            case 'contact':
                include 'sections/contact.php';
                break;
            case 'login':
                include 'auth.php';  // Load the login/signup page
                break;
            case 'admin_dashboard':
                if ($_SESSION['role'] === 'admin') {
                    include 'sections/admin_dashboard.php';
                } else {
                    header("Location: index.php?page=home");
                    exit;
                }
                break;
            case 'manage_users':
                if ($_SESSION['role'] === 'admin') {
                    include 'sections/manage_users.php';
                } else {
                    header("Location: index.php?page=home");
                    exit;
                }
                break;
            case 'user_dashboard':
                if ($_SESSION['role'] === 'user') {
                    include 'sections/user_dashboard.php';
                } else {
                    header("Location: index.php?page=home");
                    exit;
                }
                break;
            default:
                include 'sections/home.php';
                break;
        }
        ?>
    </main>

    <footer>
        <p>&copy; 2024 Cris Inn Resort</p>
    </footer>
</body>
</html>
