<?php
session_start();

// Check if user is logged in as a pharmacist
if (isset($_SESSION['role']) && $_SESSION['role'] === 'Pharmacist') {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    // Retrieve the pharmacist's details from the database
    $host = 'localhost';
    $db = 'drug-dispensing-system';
    $user = 'root';
    $password = '';

    // Create a new mysqli connection
    $conn = new mysqli($host, $user, $password, $db);

    // Check if the connection was successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the query to fetch pharmacist's details
    $stmt = $conn->prepare("SELECT u.*, ph.* FROM users u JOIN pharmacists ph ON u.user_id = ph.user_id WHERE u.user_id = ?");
    $stmt->bind_param('s', $user_id);
    $stmt->execute();

    // Fetch the pharmacist's details
    $result = $stmt->get_result();
    $pharmacist = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacist Dashboard</title>
    <link rel="stylesheet" type="text/css" href="pharmacist_dashboard.css">
</head>
<body>
<?php include "header.html";?>
    <?php if (isset($pharmacist)) : ?>
    <h2>Welcome, <?php echo $username; ?>!</h2>
    <div class="dropdown">
        <button>Actions</button>
        <div class="dropdown-content">
           
            <!-- Link to update pharmacist details -->
            <a href="update_pharmacist.php">Update Details</a>
            <!-- Link to delete pharmacist account -->
            <a href="delete_pharmacist.php" onclick="return confirm('Are you sure you want to delete your account?')">Delete Account</a>
            <!-- Link to disable pharmacist account -->
            <a href="disable_pharmacist.php" onclick="return confirm('Are you sure you want to disable your account?')">Disable Account</a>
            <!-- Link to view all patients -->
            <a href="view_patients.php">View All Patients</a>
            <!-- Link to view all drugs -->
            <a href="view_drugs.php">View All Drugs</a>
            <!-- Link to add new drug -->
            <a href="add_drug.php">Add New Drug</a>
            <!-- Link to view prescriptions (not dispensed) -->
            <a href="view_prescriptions.php?status=not_dispensed">View Prescriptions (Not Dispensed)</a>
            <!-- Link to view all prescriptions -->
            <a href="view_prescriptions.php">View All Prescriptions</a>
           
           
        </div>
    </div>
    

    <!-- Pharmacist Profile -->
    <div class="main" id="pharmacist_profile">
        <h2>Pharmacist Profile</h2>
        <table>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Age</th>
            </tr>
            <tr>
                <td><?php echo $pharmacist['full_name']; ?></td>
                <td><?php echo $pharmacist['email']; ?></td>
                <td><?php echo $pharmacist['contact_number']; ?></td>
                <td><?php echo $pharmacist['age']; ?></td>
            </tr>
        </table>
    </div>
    <br>
    <br>
    <div style="margin-left: 20px;">
        <form method="POST" action="send_message.php">
        <label for="receiver_id">Recipient User ID:</label>
        <br>
        <input type="number" id="receiver_id" name="receiver_id" required>
        <br>
    
        <label for="message">Message:</label>
        <br>
        <textarea id="message" name="message" placeholder="Enter your message here" required style="width: 400px; height: 100px;"></textarea>
        <br>
    
        <button type="submit">Send Message</button>
        </form>
        <br>
        <p><u><b>My Messages</u></b></p>
        <?php include 'fetch_messages.php'; ?>
    </div>

    <?php else : ?>
        <p>You must be logged in as a pharmacist to access this page.</p>
    <?php endif; ?>
    <?php include "footer.html";?>
</body>
</html>
