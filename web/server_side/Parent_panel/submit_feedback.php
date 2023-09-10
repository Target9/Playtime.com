<?php
$servername = "localhost";
$username = "chat_user";
$password = "p@ssw0rd1234";
$dbname = "chat_app";

$conn = new mysqli($servername, $username, $password, $dbname);

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
?>