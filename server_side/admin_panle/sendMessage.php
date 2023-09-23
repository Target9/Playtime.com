<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection functions and constants.
include '../db_connection.php';

// Use the getDbConnection function to get the connection for the 'ChatApp' database
try {
    $conn = getDbConnection(ChatApp); // ChatApp is the constant you defined in db_connection.php for the chat app database
} catch (Exception $e) {
    die(json_encode(["success" => false, "message" => $e->getMessage()]));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if username is set in the session
    if (!isset($_SESSION['username'])) {
        echo json_encode(['success' => false, 'message' => 'Username not set in session.']);
        exit;
    }
    
    $user = $_POST['username'];  // Get the username from POST data
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (user, message, timestamp) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $user, $message);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "Error" => $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
