<?php
header("HTTP/1.0 404 Not Found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - House Not Found</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        .container {
            text-align: center;
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }
        h1 {
            color: #e74c3c;
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        p {
            color: #34495e;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        .house {
            width: 100px;
            height: 100px;
            background-color: #3498db;
            position: relative;
            margin: 0 auto 2rem;
            animation: peek 3s infinite;
        }
        .roof {
            width: 0;
            height: 0;
            border-left: 50px solid transparent;
            border-right: 50px solid transparent;
            border-bottom: 50px solid #e74c3c;
            position: absolute;
            top: -50px;
            left: 0;
        }
        .door {
            width: 30px;
            height: 50px;
            background-color: #34495e;
            position: absolute;
            bottom: 0;
            left: 35px;
        }
        .window {
            width: 20px;
            height: 20px;
            background-color: #f1c40f;
            position: absolute;
            top: 20px;
            left: 15px;
            animation: wink 1.5s infinite;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #2980b9;
        }
        @keyframes peek {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        @keyframes wink {
            0%, 100% { height: 20px; }
            50% { height: 2px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="house">
            <div class="roof"></div>
            <div class="door"></div>
            <div class="window"></div>
        </div>
        <h1>404 - House Not Found</h1>
        <p>Oops! It looks like this property is playing hide and seek.</p>
        <a href="index.php" class="button">Back to Home</a>
    </div>
</body>
</html>