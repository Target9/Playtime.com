<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "chat_user";
$password = "p@ssw0rd1234";
$dbname = "chat_app";
$tableName = "feedback";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

$id = $_POST['id'];

$sql = "DELETE FROM $tableName WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);  // 'i' denotes an integer.

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error deleting feedback: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
