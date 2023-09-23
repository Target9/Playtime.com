<?php
session_start();

// Include the database connection functions and constants.
include 'db_connection.php';

// Use the getDbConnection function to get the connection for the 'USER_ADMIN' database
try {
    $conn = getDbConnection(USER_ADMIN); // USER_ADMIN is the constant you defined in db_connection.php for the Admin database
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
            $_SESSION['logged_in_as_a_admin'] = true;
            $_SESSION['irl_name'] = $irlName; 
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $userId;   
            header("Location: /server_side/admin_panle/adminpanle.php");
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
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background: linear-gradient(45deg, #0099f7, #f11712);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        form {
            width: 300px;
            padding: 30px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 10px 20px 0 rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="Password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px 0;
            border: none;
            background-color: #0099f7;
            color: #fff;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0075c2;
        }

        p {
            color: red;
            font-size: 14px;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #0099f7;
        }
    h2 {
        background-image: linear-gradient(to left, violet, indigo, blue, deepskyblue, green, orange, red);
        -webkit-background-clip: text;
        color: transparent;
        text-align: center;
        margin-bottom: 20px;
        font-size: 24px;
    }

    input[type="submit"] {
        background-image: linear-gradient(to left, violet, indigo, blue, deepskyblue, green, orange, red);
        color: #fff;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
        border: none;
        padding: 10px 0;
        width: 100%;
    }

    input[type="submit"]:hover {
        background-image: linear-gradient(to right, violet, indigo, blue, deepskyblue, green, orange, red);
    }
    </style>
</head>

<body>
    <form method="post" action="">
        <h2>Welcome back Admin</h2>
        
        <label for="Username">Username:</label>
        <input type="text" id="Username" name="Username" required>

        <label for="Password">Password:</label>
        <input type="Password" id="Password" name="Password" required>

        <?php if ($message) { echo "<p>$message</p>"; } ?>

        <input type="submit" value="Login">
    </form>
</body>

</html>
