<?php
$servername = "localhost";
$username = "chat_user";
$password = "p@ssw0rd1234";
$dbname = "chat_app";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
