<?php

include 'db_connection.php';

$tablename = "messages";

try {
    $conn = getDbConnection(ChatApp);
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

// Delete all rows from the table
$sql = "DELETE FROM $tablename";

if ($conn->query($sql) === TRUE) {
    echo "All records deleted successfully from $tablename.";
} else {
    echo "Error deleting records: " . $conn->error;
}

$conn->close();
?>
