<?php
$servername = "localhost";
$username = "roblox_tracker";
$password = "R0bl0x_Tr4ck3r$2023";
$dbname = "roblox_tracking";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
