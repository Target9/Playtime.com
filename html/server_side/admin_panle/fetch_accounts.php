<?php
// fetch_accounts.php

// Include the database connection script
require '../db_connection.php';

// Set the content type to application/json to indicate the returned content type
header('Content-Type: application/json');

try {
    // Get a database connection for the Roblox user
    $conn = getDbConnection(USER_ROBLOX);
    
    // Define the SQL query to fetch distinct persons with their IP addresses
    $sql = "
    SELECT a.person, a.ip_address 
    FROM activity AS a
    INNER JOIN (
        SELECT person, MAX(timestamp) AS max_timestamp
        FROM activity
        GROUP BY person
    ) AS subq
    ON a.person = subq.person AND a.timestamp = subq.max_timestamp
    ORDER BY a.timestamp DESC
    ";
    // Execute the SQL query
    $result = $conn->query($sql);

    // Array to hold the fetched activities
    $activities = [];

    // If there are results, fetch each row into the activities array
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $activities[] = $row;
        }
    }

    // Send the activities array as JSON to be processed by the frontend JS
    echo json_encode(['success' => true, 'data' => $activities]);
} catch(Exception $e) {
    // In case of an error, send a JSON response with the error message
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
