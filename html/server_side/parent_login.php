<?php
session_start();

include 'db_connection.php';  // Include the db_connection.php file

// Connect using the function from db_connection.php and the USER_PARENT constant
try {
    $conn = getDbConnection(USER_PARENT);
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, Password, irl_name, Username FROM users WHERE Username=?");
    $stmt->bind_param('s', $inputUsername);
    $stmt->execute();
    $stmt->bind_result($userId, $storedPassword, $irlName, $username);
    
    if ($stmt->fetch()) {
        if ($inputPassword === $storedPassword) {
            $_SESSION['logged_in_as_a_parent'] = true;
            $_SESSION['irl_name'] = $irlName;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $userId;
            header("Location: /server_side/parent_panel/parent_panel.php");
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
    <title>Parent Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">


<style>
    body {
        font-family: 'Montserrat', sans-serif;
        margin: 0;
        padding: 0;
        height: 100vh;
        background: url('https://images.pexels.com/photos/807598/pexels-photo-807598.jpeg?cs=srgb&dl=pexels-sohail-nachiti-807598.jpg&fm=jpg') center/cover no-repeat;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-container {
        width: 320px;
        padding: 30px;
        border-radius: 10px;
        background-color: rgba(255, 255, 255, 0.9);
        box-shadow: 0 10px 20px 0 rgba(0, 0, 0, 0.1);
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #a8d5b9;  /* Light green border */
        border-radius: 5px;
    }

    input[type="submit"] {
        width: 100%;
        padding: 10px 0;
        border: none;
        background-color: #4caf50; /* Primary green color */
        color: #fff;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    input[type="submit"]:hover {
        background-color: #388e3c; /* Darker shade of green for hover effect */
    }

    p {
        color: red;
        font-size: 14px;
        margin-top: -10px;
        margin-bottom: 10px;
    }

    h1 {
        text-align: center;
        color: #4caf50; /* Primary green color for the text */
        margin-bottom: 30px;
    }
</style>


</head>

<body>
    <div class="login-container">
        <h1>Welcome back Parent</h1>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <?php if ($message) { echo "<p>$message</p>"; } ?>

            <input type="submit" value="Login">
        </form>
    </div>
</body>

</html>
