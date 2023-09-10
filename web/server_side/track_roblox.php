<?php

$servername = "localhost";
$username = "roblox_tracker";
$password = "R0bl0x_Tr4ck3r$2023";
$dbname = "roblox_tracking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve POST data
$status = $_POST["status"];
$person = $_POST["person"];
$game = (isset($_POST["game"]) && $_POST["game"] != "") ? $_POST["game"] : "Nothing";

// Validate status value
if ($status !== "playing" && $status !== "not_playing") {
    die("Invalid status value.");
}

// Get the current timestamp
$timestamp = date('Y-m-d H:i:s');

// Get the IP address of the client
$ip_address = $_SERVER['REMOTE_ADDR'];

// Insert into the database
$sql = "INSERT INTO activity (timestamp, status, person, game, ip_address) VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("sssss", $timestamp, $status, $person, $game, $ip_address);
    
    if ($stmt->execute()) {
        echo "Data successfully inserted.";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

$conn->close();

?>
