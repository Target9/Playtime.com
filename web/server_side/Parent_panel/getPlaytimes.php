<?php

$servername = "localhost";
$username = "roblox_tracker";
$password = "R0bl0x_Tr4ck3r$2023";
$dbname = "roblox_tracking";

// Check if the 'date' parameter is set
if (!isset($_GET["date"])) {
    echo json_encode(["error" => "Date not provided"]);
    exit;
}

$date = $_GET["date"];
$nextDate = date('Y-m-d', strtotime($date . ' +1 day'));  // Calculate the next date

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Using prepared statements to prevent SQL injection. Also, order by person and timestamp to correctly calculate time played.
$stmt = $conn->prepare("SELECT person, status, timestamp FROM activity WHERE timestamp >= ? AND timestamp < ? ORDER BY person, timestamp ASC");
$stmt->bind_param("ss", $date, $nextDate);

$stmt->execute();

$result = $stmt->get_result();

$playtimes = [];
$previousTimestamp = null;
$previousPerson = null;

while ($row = $result->fetch_assoc()) {
    if (!isset($playtimes[$row["person"]])) {
        $playtimes[$row["person"]] = 0;
    }

    // Calculate the time difference between two consecutive timestamps if the status is "playing" and the person is the same
    if ($previousTimestamp && $previousPerson == $row["person"] && $row["status"] == "playing" && $previousStatus == "playing") {
        $timeDifference = (strtotime($row["timestamp"]) - strtotime($previousTimestamp)) / 60;
        $playtimes[$row["person"]] += $timeDifference;
    }

    $previousTimestamp = $row["timestamp"];
    $previousPerson = $row["person"];
    $previousStatus = $row["status"]; // Keep track of the status of the previous row
}


$stmt->close();
$conn->close();

echo json_encode($playtimes);

?>
