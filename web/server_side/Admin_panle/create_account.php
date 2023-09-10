<?php
// Assuming you have a connection logic in a separate file (e.g., db_connect.php)
include 'db_connect.php';

$response = ["success" => false, "message" => "Unknown error"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"));

    if(isset($data->db) && isset($data->username) && isset($data->password) && isset($data->irl_name)) {
        $dbname = "";
        $dbUser = "";
        $dbPass = "";

        switch($data->db) {
            case "Admin":
                $dbUser = "adminUser";
                $dbPass = "AdminPass123!";
                $dbname = "Admin";
                break;
            case "Parent":
                $dbUser = "parentUser";
                $dbPass = "ParentPass123!";
                $dbname = "Parent";
                break;
            case "Kid":
                $dbUser = "kidUser";
                $dbPass = "KidPass123!";
                $dbname = "Kid";
                break;
            default:
                $response["message"] = "Invalid database selection!";
                echo json_encode($response);
                exit;
        }

        // Connect to the database with the selected credentials
        $conn = new mysqli("localhost", $dbUser, $dbPass, $dbname);
        if ($conn->connect_error) {
            $response["message"] = "Connection error: " . $conn->connect_error;
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password, irl_name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $data->username, $data->password, $data->irl_name);

            if($stmt->execute()) {
                $response["success"] = true;
                $response["message"] = "Account created successfully!";
            } else {
                $response["message"] = "Error: " . $stmt->error;
            }

            $stmt->close();
            $conn->close();
        }
    } else {
        $response["message"] = "Incomplete data!";
    }
}

echo json_encode($response);
?>
