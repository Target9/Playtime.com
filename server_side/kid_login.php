<?php
session_start();

// Include the database connection functions and constants.
include 'db_connection.php';

// Use the getDbConnection function to get the connection for the 'USER_ADMIN' database
try {
    $conn = getDbConnection(USER_KID); // USER_KID is the constant you defined in db_connection.php for the Kid database
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = $_POST['Username'];
    $inputPassword = $_POST['Password'];

    $stmt = $conn->prepare("SELECT id, Password, irl_name, Username FROM users WHERE Username=?");
    $stmt->bind_param('s', $inputUsername);
    $stmt->execute();
    $stmt->bind_result($userId, $storedPassword, $irlName, $username);
    
    if ($stmt->fetch()) {
        if ($inputPassword === $storedPassword) {
            $_SESSION['logged_in_as_a_kid'] = true;
            $_SESSION['irl_name'] = $irlName; 
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $userId;   
            header("Location: /server_side/Kid_panel/kid_Panle.php");
            exit();
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "Username not found.";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kid Login</title>
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
