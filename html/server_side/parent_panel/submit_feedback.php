<?php
// Include the database connection functions and constants.
include '../db_connection.php';

// Use the getDbConnection function to get the connection for the 'ChatApp' database
try {
    $conn = getDbConnection(ChatApp); // ChatApp is the constant you defined in db_connection.php for the chat_app database
} catch (Exception $e) {
    die(json_encode(['error' => "Connection failed: " . $e->getMessage()]));
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['name']) && isset($data['feedback'])) {
    $name = $data['name'];
    $feedback = $data['feedback'];

    // Use prepared statements for security
    $stmt = $conn->prepare("INSERT INTO feedback (user, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $feedback);
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Feedback received!']);
    } else {
        echo json_encode(['error' => 'Database error']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid data']);
}

// Close the connection
$conn->close();
?>
