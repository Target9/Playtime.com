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
    $response['message'] = "Connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name'])) {
    $response['message'] = "Name parameter missing.";
    echo json_encode($response);
    exit;
}

$name = $data['name'];
$sql = "DELETE FROM activity WHERE person = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $response['message'] = "Statement preparation failed: " . $conn->error;
    echo json_encode($response);
    $conn->close();
    exit;
}

$stmt->bind_param("s", $name);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $response = ['status' => 'success', 'message' => "{$stmt->affected_rows} row(s) deleted.", 'changed' => true];
    } else {
        $response = ['status' => 'info', 'message' => "Username '{$name}' was not found.", 'changed' => false];
    }
} else {
    $response['message'] = "Execution failed: " . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);

?>
