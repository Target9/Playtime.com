<?php
// Include the database connection file
include '../db_connection.php';

try {
    // Check if the 'date' parameter is set
    if (!isset($_GET["date"])) {
        throw new Exception("Date not provided");
    }

    $date = $_GET["date"];
    $nextDate = date('Y-m-d', strtotime($date . ' +1 day'));  // Calculate the next date

    // Get a connection using the getDbConnection function
    $conn = getDbConnection('Game');

    // Using prepared statements to prevent SQL injection.
    $stmt = $conn->prepare("SELECT person, status, timestamp FROM activity WHERE timestamp >= ? AND timestamp < ? ORDER BY person, timestamp ASC");
    $stmt->bind_param("ss", $date, $nextDate);

    $stmt->execute();
    $result = $stmt->get_result();

    $playtimes = [];
    $previousTimestamp = null;
    $previousPerson = null;
    $previousStatus = null; // Initialize this variable

    while ($row = $result->fetch_assoc()) {
        if (!isset($playtimes[$row["person"]])) {
            $playtimes[$row["person"]] = 0;
        }

        // Calculate the time difference if the status is "playing" and the person is the same
        if ($previousTimestamp && $previousPerson == $row["person"] && $row["status"] == "playing" && $previousStatus == "playing") {
            $timeDifference = (strtotime($row["timestamp"]) - strtotime($previousTimestamp)) / 60;
            $playtimes[$row["person"]] += $timeDifference;
        }

        $previousTimestamp = $row["timestamp"];
        $previousPerson = $row["person"];
        $previousStatus = $row["status"];
    }

    $stmt->close();
    $conn->close();

    echo json_encode($playtimes);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}
?>
