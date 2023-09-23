<?php
// Include the database connection functions and constants.
include '../db_connection.php';

header('Content-Type: application/json');

// Use the getDbConnection function to get the connection for the 'ChatApp' database
try {
    $conn = getDbConnection(ChatApp); // ChatApp is the constant you defined in db_connection.php for the chat app database
} catch (Exception $e) {
    die(json_encode(["success" => false, "message" => $e->getMessage()]));
}

$tableName = "feedback";

$sql = "SELECT * FROM $tableName";
$result = $conn->query($sql);

$feedbacks = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}

echo json_encode(["success" => true, "feedbacks" => $feedbacks]);

$conn->close();
?>
