<?php
$servername = "localhost";
$username = "chat_user";
$password = "p@ssw0rd1234";
$dbname = "chat_app";
$tablename = "messages";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
