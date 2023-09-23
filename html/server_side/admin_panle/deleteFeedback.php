<?php
header('Content-Type: application/json');

include '../db_connection.php';  // Include the database connection functions and constants.

$tableName = "feedback";

try {
    // Use the getDbConnection function to get a connection for ChatApp.
    $conn = getDbConnection(ChatApp);
} catch (Exception $e) {
    die(json_encode(["success" => false, "message" => $e->getMessage()]));
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
