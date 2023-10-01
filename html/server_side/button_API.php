<?php
// button_API.php

ini_set('log_errors', 1);
ini_set('error_log', '/var/www/php_error_logs.txt');

require 'db_connection.php';

function addCustomButton($text, $action) {
    $conn = getDbConnection(USER_WEBAPP);

    $stmt = $conn->prepare("INSERT INTO buttons (text, action) VALUES (?, ?)");
    $stmt->bind_param("ss", $text, $action);

    if ($stmt->execute()) {
        return ["success" => true, "message" => "Button added successfully"];
    } else {
        return ["success" => false, "message" => "Error adding button: " . $stmt->error];
    }
}

function getCustomButtons() {
    $conn = getDbConnection(USER_WEBAPP);

    $result = $conn->query("SELECT id, text, action FROM buttons");
    
    $buttons = [];
    while($row = $result->fetch_assoc()) {
        $buttons[] = $row;
    }

    return ["success" => true, "buttons" => $buttons];
}

function deleteCustomButton($id) {
    $conn = getDbConnection(USER_WEBAPP);

    $stmt = $conn->prepare("DELETE FROM buttons WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        return ["success" => true, "message" => "Button deleted successfully"];
    } else {
        return ["success" => false, "message" => "Error deleting button"];
    }
}

function handleButtonAction($action) {
    switch($action) {
        case "startProcess":
            exec("your-command-to-start-process"); // Replace with actual command
            break;
        case "killProcess":
            exec("your-command-to-kill-process"); // Replace with actual command
            break;
        // ... add other actions as needed
        default:
            return ["success" => false, "message" => "Unknown action"];
    }

    return ["success" => true, "message" => "Action performed successfully"];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle the GET requests

    // Fetch custom buttons
    if (isset($_GET['fetch']) && $_GET['fetch'] == 'buttons') {
        echo json_encode(getCustomButtons());
        exit();
    }

    // Handle button actions
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        switch ($action) {
            case 'startProcess':
                // Code to start the process
                exec("your-command-to-start-process"); // Replace with the actual command
                echo json_encode(["message" => "Process started"]);
                break;

            case 'killProcess':
                // Code to kill the process
                exec("your-command-to-kill-process"); // Replace with the actual command
                echo json_encode(["message" => "Process killed"]);
                break;

            // ... you can add more cases here to handle other actions

            default:
                echo json_encode(["message" => "Unknown action"]);
                break;
        }
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the POST requests

    // Assuming you're sending JSON data in the POST request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if the required parameters for adding a new button are set
    if (isset($data['text']) && isset($data['action'])) {
        echo json_encode(addCustomButton($data['text'], $data['action']));
        exit();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid parameters"]);
        exit();
    }
}

// Added DELETE method handler
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['id'])) {
        echo json_encode(deleteCustomButton($_GET['id']));
        exit();
    } else {
        echo json_encode(["success" => false, "message" => "Button ID not provided"]);
        exit();
    }
}

?>
