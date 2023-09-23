<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include '../db_connection.php'; // Include db_connection.php to get the getDbConnection function

$data = json_decode(file_get_contents("php://input"));

$idToDelete = $data->id;
$database = $data->database;
$userType = null;

switch ($database) {
    case 'Admin':
        $userType = USER_ADMIN;
        break;
    case 'Parent':
        $userType = USER_PARENT;
        break;
    case 'Kid':
        $userType = USER_KID;
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid database selected.']);
        exit;
}

try {
    // Using getDbConnection function to get the database connection
    $conn = getDbConnection($userType);

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

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
