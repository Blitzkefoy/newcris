<?php
session_start();
ini_set('session.gc_maxlifetime', 3600);  // 1-hour session timeout
session_set_cookie_params(3600);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include 'DatabaseConnection.php';

// Handle the Login Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate inputs
    if (empty($username) || empty($password)) {
        echo "Username and password are required.";
        exit;
    }

    // Check if the user is an admin
    $adminQuery = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($adminQuery);
    if (!$stmt) {
        die("Admin query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $adminResult = $stmt->get_result();

    if ($adminResult->num_rows == 1) {
        $admin = $adminResult->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            // Admin login successful
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'admin';
            $_SESSION['user_id'] = $admin['id'];  // Admin ID for tracking

            header("Location: index.php?page=admin_dashboard");
            exit;
        } else {
            echo "Invalid login credentials for admin.";
        }
    }

    // Check if the user is a regular user
    $userQuery = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($userQuery);
    if (!$stmt) {
        die("User query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $userResult = $stmt->get_result();

    if ($userResult->num_rows == 1) {
        $user = $userResult->fetch_assoc();

        // Step 5: Check and dynamically populate `guest_id` if missing
        if (empty($user['guest_id'])) {
            // Assign `guest_id` the same value as `user_id`
            $guest_id = $user['user_id'];
            $updateGuestIdQuery = "UPDATE users SET guest_id = ? WHERE user_id = ?";
            $updateStmt = $conn->prepare($updateGuestIdQuery);
            $updateStmt->bind_param("ii", $guest_id, $user['user_id']);
            $updateStmt->execute();
            $updateStmt->close();

            // Update the user array to reflect the new `guest_id`
            $user['guest_id'] = $guest_id;
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'user';
            $_SESSION['user_id'] = $user['user_id'];  // Ensure this matches your DB column
            $_SESSION['guest_id'] = $user['guest_id'];  // Add guest_id for reservations

            header("Location: index.php?page=user_dashboard");
            exit;
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
}

// Handle the User Sign-Up Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup_user'])) {
    // Collect the new user info from the form
    $guest_id = $_POST['guest_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_no = $_POST['phone_no'];
    $new_username = $_POST['new_username'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);  // Securely hash the password

    // Validate email and phone number
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    if (!preg_match('/^[0-9]{10,15}$/', $phone_no)) {
        echo "Invalid phone number format.";
        exit;
    }

    // Check if the username already exists
    $checkQuery = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $new_username);
    $stmt->execute();
    $checkResult = $stmt->get_result();

    if ($checkResult->num_rows == 0) {
        // Insert into users with the new fields
        $signupQuery = "INSERT INTO users (guest_id, first_name, last_name, email, phone_no, username, password) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($signupQuery);
        $stmt->bind_param("sssssss", $guest_id, $first_name, $last_name, $email, $phone_no, $new_username, $new_password);
        if ($stmt->execute()) {
            echo "User registration successful. You can now log in.";
        } else {
            echo "Error: Could not register user. Please try again.";
        }
    } else {
        echo "Username already taken. Please choose another.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Sign Up</title>
    <link rel="stylesheet" href="style.css"> <!-- Ensure you have a style.css -->
</head>
<body>
    <div class="auth-container">

        <?php
        // Toggle between the login and sign-up forms based on a query parameter
        if (isset($_GET['action']) && $_GET['action'] === 'signup') {
            // Show the User Sign-Up Form
        ?>
            <h2>User Sign Up</h2>
            <form action="auth.php" method="POST">
                <!-- Guest details -->
                <label for="guest_id">Guest ID:</label>
                <input type="text" name="guest_id" id="guest_id" required>

                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" id="first_name" required>

                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" id="last_name" required>

                <!-- Email and Phone Number in one row -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone_no">Phone Number:</label>
                        <input type="tel" name="phone_no" id="phone_no" pattern="[0-9]{10,15}" required>
                        <small>Format: 10 to 15 digits</small>
                    </div>
                </div>

                <!-- Username and Password placed below -->
                <label for="new_username">Username:</label>
                <input type="text" name="new_username" id="new_username" required>

                <label for="new_password">Password:</label>
                <input type="password" name="new_password" id="new_password" required>

                <button type="submit" name="signup_user">Sign Up</button>
            </form>
            <p>Already have an account? <a href="auth.php">Login here</a></p>

        <?php
        } else {
            // Show the Login Form by default
        ?>
            <h2>Login</h2>
            <form action="auth.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>

                <button type="submit" name="login">Login</button>
            </form>
            <p>Don't have an account? <a href="auth.php?action=signup">Sign up here</a></p>

        <?php
        }
        ?>

    </div>
</body>
</html>
