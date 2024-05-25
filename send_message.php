<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'drug-dispensing-system';
$user = 'root';
$password = '';
$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id']; // Assuming user ID is stored in session after login
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    // Check if sender_id exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $sender_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        echo "Error: Sender ID does not exist.";
        exit();
    }
    $stmt->close();

    // Check if receiver_id exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $receiver_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        echo "Error: Receiver ID does not exist.";
        exit();
    }
    $stmt->close();

    // Insert the message
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $sender_id, $receiver_id, $message);

    if ($stmt->execute()) {
        echo "Message sent successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>