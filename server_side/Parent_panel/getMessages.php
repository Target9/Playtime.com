<?php
// Include the database connection functions and constants.
include '../db_connection.php';

// Use the getDbConnection function to get the connection for the 'ChatApp' database
try {
    $conn = getDbConnection(ChatApp); // ChatApp is the constant you defined in db_connection.php for the chat app database
} catch (Exception $e) {
    die(json_encode(["success" => false, "message" => $e->getMessage()]));
}

$sql = "SELECT user, message, timestamp FROM messages ORDER BY timestamp DESC LIMIT 50";
$result = $conn->query($sql);

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);

$conn->close();
?>
