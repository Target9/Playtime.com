<?php
session_start();

if ((!isset($_SESSION["logged_in_as_a_admin"]) || $_SESSION["logged_in_as_a_admin"] !== true) && 
    (!isset($_SESSION["logged_in_as_a_parent"]) || $_SESSION["logged_in_as_a_parent"] !== true)) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="robots" content="noindex">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div id="sidebar">
        <h3>Dashboard</h3>
        <ul>
            <li><a href="#" onclick="loadHomePage()">Info panel</a></li>
            <li><a href="#" onclick="controlpanel()">Control panel</a></li>
            <li><a href="#" onclick="initializeChat()">Chat</a></li>
            <li><a href="#" onclick="Feedback()">Give Feedback</a></li>
            <li><a href="#" onclick="initializePlaytimePage()">Show Playtime</a></li>
            <li><a href="#" onclick="initializeSearchBarAndData()">Timestamps</a></li>
        </ul>
        
    </div>
    <div id="content">
        Welcome to the Parent Panel Select an option from the left to view details.
    </div>
    
    
    <script src="scripts.js"></script>

</body>

</html>
