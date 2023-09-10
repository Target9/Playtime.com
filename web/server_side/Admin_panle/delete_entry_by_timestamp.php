<?php
$servername = "localhost";
$username = "roblox_tracker";
$password = "R0bl0x_Tr4ck3r$2023";
$dbname = "roblox_tracking";

$response = ['status' => 'error', 'message' => 'Unknown error'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => "Connection failed: " . $conn->connect_error]));
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['timestamp'])) {
    die(json_encode(['status' => 'error', 'message' => "Timestamp parameter missing."]));
}

$timestamp = $data['timestamp'];

$sql = "DELETE FROM activity WHERE timestamp = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(['status' => 'error', 'message' => "Statement preparation failed: " . $conn->error]));
}

$stmt->bind_param("s", $timestamp);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $response = ['status' => 'success', 'message' => "Deleted entry with timestamp: {$timestamp}."];
    } else {
        $response = ['status' => 'error', 'message' => "No entry found with timestamp: {$timestamp}."];
    }
} else {
    $response['message'] = "Execution failed: " . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
