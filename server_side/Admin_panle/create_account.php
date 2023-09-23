<?php
include '../db_connection.php'; // Include the new db_connection.php instead of the old db_connect.php

$response = ["success" => false, "message" => "Unknown error"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"));

    if(isset($data->db) && isset($data->username) && isset($data->password) && isset($data->irl_name)) {
        $userType = ""; // This variable will be used to determine which database connection we should get

        switch($data->db) {
            case "Admin":
                $userType = USER_ADMIN; // Set to the constant for ADMIN user
                break;
            case "Parent":
                $userType = USER_PARENT; // Set to the constant for PARENT user
                break;
            case "Kid":
                $userType = USER_KID; // Set to the constant for KID user
                break;
            default:
                $response["message"] = "Invalid database selection!";
                echo json_encode($response);
                exit;
        }

        try {
            // Connect to the database using the new getDbConnection function
            $conn = getDbConnection($userType);
            
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
        } catch (Exception $e) {
            $response["message"] = $e->getMessage(); // Use the exception message as the error message
        }
    } else {
        $response["message"] = "Incomplete data!";
    }
}

echo json_encode($response);
?>
 