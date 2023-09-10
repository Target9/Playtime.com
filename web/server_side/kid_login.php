<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database Configuration
$servername = "localhost";
$username = "kidUser"; // Admin user
$password = "KidPass123!"; // Admin password
$dbname = "Kid"; // Admin database

// Create a new MySQL connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Verify the provided username and password (Basic check)
    if ($inputUsername === $username && $inputPassword === $password) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $inputUsername;
        $_SESSION['logged_in_as'] = 'kid';  // Specify user type as 'admin'
        
        header("Location: admin_panel.php"); // Redirect to the Admin dashboard
        exit();
    } else {
        $message = "Incorrect username or password.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background: url('https://images3.alphacoders.com/278/27807.jpg') center/cover no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        form {
            width: 350px;
            padding: 30px;
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.6);  /* Semi-transparent black */
            box-shadow: 0 10px 20px 0 rgba(0, 0, 0, 0.2);
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.9);
            transition: background-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            background-color: rgba(255, 255, 255, 0.7);
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px 0;
            border: none;
            background: linear-gradient(45deg, #FF0000, #FF7F00, #FFFF00, #7FFF00, #00FF00, #00FF7F, #00FFFF, #007FFF, #0000FF, #7F00FF, #FF00FF, #FF007F);
            color: #000;
            font-weight: bold;
            border-radius: 50px; /* Fully rounded corners */
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        input[type="submit"]:hover {
            transform: scale(1.05);  /* Zoom in effect */
            box-shadow: 0 10px 20px 0 rgba(0, 0, 0, 0.3);
        }

        p {
            color: red;
            font-size: 16px;
            text-shadow: 2px 2px 4px #000000;
            margin-top: -15px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <?php if ($message) { echo "<p>$message</p>"; } ?>

        <input type="submit" value="Login">
    </form>
</body>

</html>
