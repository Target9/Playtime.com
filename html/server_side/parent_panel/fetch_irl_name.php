<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['irl_name'])) {
    echo json_encode(['irl_name' => $_SESSION['irl_name']]);
} else {
    echo json_encode(['error' => 'Not logged in']);
}
?>
