<?php
include '../db_connection.php';

// Fetch distinct persons from the database
function fetchPersons() {
    $conn = getDbConnection(USER_ROBLOX);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT DISTINCT person FROM activity";
    $result = $conn->query($sql);

    $persons = [];
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $persons[] = $row['person'];
        }
    } else {
        die("Error executing query: " . $conn->error);
    }

    $conn->close();
    return $persons;
}

$persons = fetchPersons();

// Output the persons as a JSON response
header('Content-Type: application/json');
echo json_encode($persons);
?>
