<?php
$host = 'localhost';
$db = 'drug-dispensing-system';
$user = 'root';
$password = '';


$user_id = $_SESSION['user_id']; // Assuming user ID is stored in session after login

$stmt = $conn->prepare("SELECT * FROM messages WHERE sender_id = ? OR receiver_id = ? ORDER BY timestamp DESC");
$stmt->bind_param("ii", $user_id, $user_id);

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<div>";
    echo "<p><strong>From:</strong> " . getUserById($row['sender_id']) . "</p>";
    echo "<p><strong>To:</strong> " . getUserById($row['receiver_id']) . "</p>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($row['message']) . "</p>";
    echo "<p><strong>Timestamp:</strong> " . $row['timestamp'] . "</p>";
    echo "</div><hr>";
}

$stmt->close();
$conn->close();

function getUserById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    $stmt->close();
    return $name;
}
?>

