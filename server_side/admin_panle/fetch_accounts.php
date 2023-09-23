<?php
session_start();

if (!isset($_SESSION["logged_in_as_a_admin"]) || $_SESSION["logged_in_as_a_admin"] !== true) {
    header("Location: index.php");
    exit;
}

header('Content-Type: application/json');
include '../db_connection.php';  // Include the database connection functions and constants.

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if (isset($data->database)) {
        try {
            $conn = getDbConnection($data->database);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
            exit;
        }
        
        $searchTerm = $data->searchTerm ?? "";

        if ($searchTerm) {
            // If search term exists, we use it to filter results
            $sql = "SELECT id, username, password, irl_name FROM users WHERE username LIKE ?";
            $stmt = $conn->prepare($sql);
            $searchTerm = "%" . $searchTerm . "%";
            $stmt->bind_param('s', $searchTerm);
        } else {
            // Otherwise, fetch all data
            $sql = "SELECT id, username, password, irl_name FROM users";
            $stmt = $conn->prepare($sql);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode($data);

        $stmt->close();
        $conn->close();

    } else {
        echo json_encode(["error" => "No database specified"]);
    }
}
?>
