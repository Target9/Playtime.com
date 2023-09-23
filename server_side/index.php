<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Choice</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('https://images.hdqwalls.com/wallpapers/purple-flowers-background-5k-at.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-section {
            background-color: rgba(255,255,255,0.8);
            padding: 40px 50px;
            border-radius: 20px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
        }

        h1 {
            font-weight: 700;
            font-size: 2.5em;
            margin-bottom: 30px;
            color: #333;
        }

        .login-btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            font-size: 18px;
            border: none;
            background-image: linear-gradient(135deg, #71b7e6, #9b59b6);
            color: #fff;
            cursor: pointer;
            border-radius: 50px;
            transition: transform 0.3s;
            text-decoration: none;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
        }

        .login-btn:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div class="login-section">
        <h1>Login As:</h1>
        <a class="login-btn" href="kid_login.php">Kid</a>
        <a class="login-btn" href="parent_login.php">Parent</a>
        <a class="login-btn" href="admin_login.php">Admin</a>
    </div>
</body>
</html>
