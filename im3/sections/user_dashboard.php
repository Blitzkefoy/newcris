
<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth.php");
    exit;
}
header("Location: ../index.php?page=home");
exit;
?>

