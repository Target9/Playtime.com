<?php
// Use the constants and function from the previous block
define('USER_ADMIN', 'ADMIN');
define('USER_KID', 'KID');
define('USER_PARENT', 'PARENT');
define('Roblox', 'Game');

function getDbConnection($userType) {
    $host = 'localhost';
    $userCredentials = [
        USER_ADMIN  => ['user' => 'adminUser', 'pass' => 'AdminPass123!', 'db' => 'Admin'],
        USER_KID    => ['user' => 'kidUser',   'pass' => 'KidPass123!',    'db' => 'Kid'],
        USER_PARENT => ['user' => 'parentUser','pass' => 'ParentPass123!', 'db' => 'Parent'],
        Roblox => ['user' => 'roblox_tracker','pass' => 'R0bl0x_Tr4ck3r$2023', 'db' => 'roblox_tracking']
    ];
    if (!array_key_exists($userType, $userCredentials)) {
        throw new Exception("Invalid user type provided.");
    }
    $user = $userCredentials[$userType]['user'];
    $pass = $userCredentials[$userType]['pass'];
    $db   = $userCredentials[$userType]['db'];
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

$response = ['status' => 'error', 'message' => 'Unknown error'];

try {
    $conn = getDbConnection(Roblox);

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['name'])) {
        throw new Exception("Name parameter missing.");
    }

    $name = $data['name'];
    $sql = "DELETE FROM activity WHERE person = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Statement preparation failed: " . $conn->error);
    }
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = ['status' => 'success', 'message' => "{$stmt->affected_rows} row(s) deleted.", 'changed' => true];
        } else {
            $response = ['status' => 'info', 'message' => "Username '{$name}' was not found.", 'changed' => false];
        }
    } else {
        $response['message'] = "Execution failed: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
?>
