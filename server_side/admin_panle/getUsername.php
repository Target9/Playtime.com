<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['username'])) {
    echo json_encode(['username' => $_SESSION['username']]);
} else {
    echo json_encode(['error' => 'Username not set in session. That means you cant type in the chat. Try loging out']);
}
?>
