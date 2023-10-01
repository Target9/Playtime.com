<?php
// db_connection.php

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define constants for user types
define('USER_ADMIN', 'Admin');
define('USER_KID', 'Kid');
define('USER_PARENT', 'Parent');
define('USER_ROBLOX', 'Game');
define('ChatApp', 'Chat');
define('USER_WEBAPP', 'WebApp');

function getDbConnection($userType) {
    // Ensure the server has the MySQLi extension enabled
    if (!extension_loaded('mysqli')) {
        throw new Exception("MySQLi extension is not loaded.");
    }

    $host = 'db'; // Change this if your database is not on the same server or container. E.g., 'localhost', '127.0.0.1', etc.

    // Define user types and their corresponding credentials and databases
    $userCredentials = [
        USER_ADMIN  => ['user' => 'adminUser', 'pass' => 'AdminPass123!', 'db' => 'Admin'],
        USER_KID    => ['user' => 'kidUser',   'pass' => 'KidPass123!',    'db' => 'Kid'],
        USER_PARENT => ['user' => 'parentUser','pass' => 'ParentPass123!', 'db' => 'Parent'],
        USER_ROBLOX => ['user' => 'roblox_tracker','pass' => 'R0bl0x_Tr4ck3r$2023', 'db' => 'roblox_tracking'],
        ChatApp     => ['user' => 'chat_user', 'pass' => 'p@ssw0rd1234',   'db' => 'chat_app'],
        USER_WEBAPP => ['user' => 'webAppUser', 'pass' => 'WebApp$2023Pass', 'db' => 'buttons_db']
    ];

    // Ensure the user type is valid
    if (!array_key_exists($userType, $userCredentials)) {
        throw new Exception("Invalid user type provided.");
    }

    $user = $userCredentials[$userType]['user'];
    $pass = $userCredentials[$userType]['pass'];
    $db   = $userCredentials[$userType]['db'];

    // Create connection
    $conn = new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($conn->connect_errno) {
        throw new Exception("Connection failed: (" . $conn->connect_errno . ") " . $conn->connect_error);
    }

    return $conn;
}

/*
// Test connection for one of the user types when the file is accessed directly. 
// This is just for debugging purposes and can be removed or commented out later.
if (isset($_GET['test'])) {
    try {
        $connection = getDbConnection(THE USER YOU WANT TO TEST); // Test with the WebApp user, can change this to another user type.
        echo "Connection successful!";
        $connection->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
*/

?>
