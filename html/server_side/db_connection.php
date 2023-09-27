<?php
// db_connection.php

// Define constants for user types
define('USER_ADMIN', 'Admin');
define('USER_KID', 'Kid');
define('USER_PARENT', 'Parent');
define('USER_ROBLOX', 'Game');
define('ChatApp', 'Chat');

function getDbConnection($userType) {
    $host = 'db';

    // Define user types and their corresponding credentials and databases
    $userCredentials = [
        USER_ADMIN  => ['user' => 'adminUser', 'pass' => 'AdminPass123!', 'db' => 'Admin'],
        USER_KID    => ['user' => 'kidUser',   'pass' => 'KidPass123!',    'db' => 'Kid'],
        USER_PARENT => ['user' => 'parentUser','pass' => 'ParentPass123!', 'db' => 'Parent'],
        USER_ROBLOX      => ['user' => 'roblox_tracker','pass' => 'R0bl0x_Tr4ck3r$2023', 'db' => 'roblox_tracking'],
        ChatApp     => ['user' => 'chat_user', 'pass' => 'p@ssw0rd1234',   'db' => 'chat_app']
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
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
