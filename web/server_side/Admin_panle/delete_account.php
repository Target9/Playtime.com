<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$servername = "localhost";
$data = json_decode(file_get_contents("php://input"));

$usernameOrIRLNameToDelete = $data->username ?? null;
$database = $data->database;

switch ($database) {
    case 'Admin':
        $dbUsername = "adminUser";
        $password = "AdminPass123!";
        $dbname = "Admin";
        break;

    case 'Parent':
        $dbUsername = "parentUser";
        $password = "ParentPass123!";
        $dbname = "Parent";
        break;

    case 'Kid':
        $dbUsername = "kidUser";
        $password = "KidPass123!";
        $dbname = "Kid";
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid database selected.']);
        exit;
}

// Create connection
$conn = new mysqli($servername, $dbUsername, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Try deleting by username first
$sql = "DELETE FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usernameOrIRLNameToDelete);

if (!$stmt->execute() || $stmt->affected_rows == 0) {
    // If no rows were affected, try deleting by IRL name
    $sql = "DELETE FROM users WHERE irl_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usernameOrIRLNameToDelete);
    
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => $conn->error]);
        exit;
    }
}

// Check if any rows were affected (i.e., if any account was actually deleted)
if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Account deleted successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'No account found with the provided details.']);
}

$stmt->close();
$conn->close();
?>
