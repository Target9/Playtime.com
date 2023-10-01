<?php

include '../db_connection.php';

try {
    $conn = getDbConnection(USER_ROBLOX);
} catch (Exception $e) {
    die(json_encode(["success" => false, "message" => $e->getMessage()]));
}

$sql = "SELECT id, timestamp, status, person, game, ip_address FROM activity ORDER BY timestamp DESC";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} 

echo json_encode($data);

$conn->close();
?>