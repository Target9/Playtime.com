<?php

include 'db_connection.php';

try {
    $conn = getDbConnection(USER_ROBLOX);
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
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
        echo "Thanks for the data!";
    } else {
        echo "Error: " . $stmt->error;
    }    
    
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

$conn->close();
?>
