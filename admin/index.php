<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'realestate');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aemail = $_POST['aemail'];
    $apass = $_POST['apass'];

    // Fetch admin details
    $sql = "SELECT * FROM admin WHERE aemail = '$aemail'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($apass, $row['apass'])) {
            // Start session and store admin info
            session_start();
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['auser'] = $row['auser'];
            echo "Login successful!";
            header("Location: dashboard.php");
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with this email.";
    }
    $conn->close();
}
?>

           



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #000066;
            --secondary-color: #3333cc;
            --accent-color: #4d4dff;
        }
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 400px;
            max-width: 100%;
            padding: 2.5rem;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.5s ease-out forwards;
        }
        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .login-header p {
            color: var(--secondary-color);
        }
        .form-floating {
            margin-bottom: 1.5rem;
        }
        .form-control {
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 102, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 50px;
            width: 100%;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 102, 0.2);
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
        }
        .input-icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .forgot-password {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .forgot-password:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: var(--primary-color);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <center>
        <div class="login-card">
            <div class="login-header">
                <h1 class="mb-4">Admin Login</h1>
                <p class="text-muted">Enter your credentials to access the dashboard</p>
            </div>
            <form action="" method="post">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="aemail" name="aemail" placeholder="Enter your email" required>
                    <label for="floatingInput">Email address</label>
                    <i class="bi bi-envelope input-icon"></i>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="apass" name="apass" placeholder="Password" required>
                    <label for="floatingPassword">Password</label>
                    <i class="bi bi-lock input-icon"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            Remember me
                        </label>
                    </div>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>
                <button type="submit" value="Login" class="btn btn-login">Login</button>
            </form>
            <div class="login-footer">
                <p>&copy; 2024 Admin Dashboard. All rights reserved.</p>
            </div>
        </div>
        </center>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</body>
</html>