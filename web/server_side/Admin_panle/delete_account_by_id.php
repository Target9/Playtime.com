<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$servername = "localhost";
$data = json_decode(file_get_contents("php://input"));

$idToDelete = $data->id;
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

$conn = new mysqli($servername, $dbUsername, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idToDelete);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Account deleted successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
?>
