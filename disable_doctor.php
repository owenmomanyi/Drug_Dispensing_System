<?php
session_start();

// Check if user is logged in as a doctor
if (isset($_SESSION['role']) && $_SESSION['role'] === 'Doctor') {
    $user_id = $_SESSION['user_id'];

    // Include the database connection file
    $host = 'localhost';
    $db_name = 'drug-dispensing-system';
    $user = 'root';
    $password = '';

    $conn = new mysqli($host, $user, $password, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the query to fetch doctor's details from both users and doctors tables
    $stmt = $conn->prepare("SELECT u.*, d.* FROM users u JOIN doctors d ON u.user_id = d.user_id WHERE u.user_id = ?");
    $stmt->bind_param('s', $user_id);
    $stmt->execute();

    // Fetch the doctor's details
    $result = $stmt->get_result();
    $doctor = $result->fetch_assoc();

    // Handle disable/enable process
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Disable doctor's account
        if (isset($_POST['disable_doctor'])) {
            $update_stmt = $conn->prepare("UPDATE doctors SET is_disabled = 1 WHERE user_id = ?");
            $update_stmt->bind_param('s', $user_id);
            $update_result = $update_stmt->execute();

            if ($update_result) {
                echo "<p>Your account has been disabled. Please contact the admin to enable it again.</p>";
            } else {
                echo "Error disabling account: " . $conn->error;
            }
        }

        // Enable doctor's account (Admin approval required)
        if (isset($_POST['enable_doctor'])) {
            // Check if the current user is an admin (you may need to adjust this based on your user roles implementation)
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin') {
                $update_stmt = $conn->prepare("UPDATE doctors SET is_disabled = 0 WHERE user_id = ?");
                $update_stmt->bind_param('s', $user_id);
                $update_result = $update_stmt->execute();

                if ($update_result) {
                    echo "<p>Your account has been enabled.</p>";
                } else {
                    echo "Error enabling account: " . $conn->error;
                }
            } else {
                echo "<p>Only admins can approve enabling the account. Please contact the admin to enable it.</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Disable/Enable Doctor Account</title>
</head>
<body>
    <?php if (isset($doctor)) : ?>
    <?php if ($doctor['is_disabled']) : ?>
        <h2>Your Account is Disabled</h2>
        <p>Your account is currently disabled. Please contact the admin to enable it again.</p>
    <?php else : ?>
        <h2>Disable Account</h2>
        <p>By clicking the "Disable Account" button, your account will be temporarily disabled. You will not be able to access your account or use the drug dispensing tool until an admin approves the enabling.</p>
        <form method="post" action="disable_doctor.php">
            <input type="submit" name="disable_doctor" value="Disable Account">
        </form>
    <?php endif; ?>

    <?php if (!$doctor['is_disabled'] && isset($_SESSION['role']) && $_SESSION['role'] === 'Admin') : ?>
        <h2>Enable Account</h2>
        <p>By clicking the "Enable Account" button, your account will be enabled again, but admin approval is required.</p>
        <form method="post" action="disable_doctor.php">
            <input type="submit" name="enable_doctor" value="Enable Account">
        </form>
    <?php endif; ?>

    <?php else : ?>
        <p>You must be logged in as a doctor to access this page.</p>
    <?php endif; ?>
</body>
</html>
