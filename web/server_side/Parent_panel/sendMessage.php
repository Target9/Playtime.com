<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "chat_user";
$password = "p@ssw0rd1234";
$dbname = "chat_app";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
