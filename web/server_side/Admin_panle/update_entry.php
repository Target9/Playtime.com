<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "roblox_tracker";
$password = "R0bl0x_Tr4ck3r$2023";
$dbname = "roblox_tracking";

$response = ['status' => 'error', 'message' => 'Unknown error'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    $response['message'] = "Connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Validate received data
if (!isset($data['name']) || !isset($data['status']) || !isset($data['game'])) {
    die(json_encode(['status' => 'error', 'message' => "Incomplete data sent to the server."]));
}

$name = $data['name'];
$status = $data['status'];
$game = $data['game'];
$timestamp = isset($data['timestamp']) ? $data['timestamp'] : null;

$sql = "UPDATE activity SET status=?, game=? WHERE person=?";
$params = [$status, $game, $name];

if ($timestamp) {
    $sql .= " AND timestamp=?";
    $params[] = $timestamp;
}

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(['status' => 'error', 'message' => "Statement preparation failed: " . $conn->error]));
}

$stmt->bind_param(str_repeat("s", count($params)), ...$params);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $response = ['status' => 'success', 'message' => "Updated {$stmt->affected_rows} row(s)."];
    } else {
        $response = ['status' => 'info', 'message' => "No rows with the specified conditions were found or no changes were made."];
    }
} else {
    $response['message'] = "Execution failed: " . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
