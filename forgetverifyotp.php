<?php
session_start(); // Start the session

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "realestate";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper function to validate input data
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$response = ['message' => '', 'not_verified' => false]; // Added 'not_verified' for SweetAlert

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resend_otp'])) {
        $email = validate_input($_POST['email']);
        $role = validate_input($_POST['role']);
        $new_otp = generateOTP();

        if ($role == 'user') {
            $stmt = $conn->prepare("UPDATE users SET otp = ? WHERE uemail = ?");
        } else if ($role == 'agent') {
            $stmt = $conn->prepare("UPDATE agents SET otp = ? WHERE aemail = ?");
        }

        $stmt->bind_param("ss", $new_otp, $email);
        if ($stmt->execute()) {
            // Send the new OTP to the user's email (you need to implement this part)
            if (sendOTPEmail($email, $new_otp)) {
                echo json_encode(['status' => 'success', 'message' => 'New OTP has been sent to your email.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error: Unable to send OTP.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
        }
        $stmt->close();
        exit;
    } else {
        $email = validate_input($_POST['email']);
        $role = validate_input($_POST['role']);
        $otp = implode('', $_POST['otp']); // Combine the OTP digits into a single string

        if ($role == 'user') {
            $stmt = $conn->prepare("SELECT otp FROM users WHERE uemail = ?");
            $stmt->bind_param("s", $email);
        } else if ($role == 'agent') {
            $stmt = $conn->prepare("SELECT otp FROM agents WHERE aemail = ?");
            $stmt->bind_param("s", $email);
        }

        $stmt->execute();
        $stmt->bind_result($db_otp);
        $stmt->fetch();
        $stmt->close();

        if ($db_otp == $otp) {
            // Redirect to change password page
            header("Location: changepassword.php?email=" . urlencode($email) . "&role=" . urlencode($role));
            exit;
        } else {
            // Redirect back to the verify_otp.php page with the email and role parameters
            header("Location: forgetverifyotp.php?email=" . urlencode($email) . "&role=" . urlencode($role) . "&error=invalid_otp");
            exit;
        }
    }
}

function generateOTP($length = 6) {
    $characters = '0123456789';
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $otp;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email OTP Verification</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #45aaf2;
            --background-color: #f7f9fc;
            --text-color: #333;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .otp-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 5), 0 3px 6px rgba(0, 0, 0, 5);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }

        .otp-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .otp-header h1 {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: bold;
        }

        .otp-form .form-control {
            border-radius: 4px;
            padding: 0.75rem 1rem;
        }

        .otp-input {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }

        .otp-input input {
            width: 3rem;
            height: 3rem;
            text-align: center;
            font-size: 1.5rem;
            border: 2px solid #ddd;
            border-radius: 4px;
            margin: 0 0.25rem;
        }

        .otp-input input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        }

        .btn-verify {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
            font-weight: bold;
            padding: 0.75rem 1rem;
            width: 100%;
            margin-top: 1.5rem;
        }

        .btn-verify:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .resend-otp {
            text-align: center;
            margin-top: 1rem;
        }

        .resend-otp a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
        }

        .resend-otp a:hover {
            color: var(--secondary-color);
        }

        .email-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 1rem;
        }

        .success-message {
            color: green;
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <center>
        <div class="otp-container">
            <div class="otp-header">
                <div class="email-icon">✉️</div>
                <h1>Email Verification</h1>
                <p>Enter the OTP sent to your email</p>
            </div>
            <form class="otp-form" action="forgetverifyotp.php" method="post">
                <div class="otp-input">
                    <input type="text" maxlength="1" class="form-control" name="otp[]" required>
                    <input type="text" maxlength="1" class="form-control" name="otp[]" required>
                    <input type="text" maxlength="1" class="form-control" name="otp[]" required>
                    <input type="text" maxlength="1" class="form-control" name="otp[]" required>
                    <input type="text" maxlength="1" class="form-control" name="otp[]" required>
                    <input type="text" maxlength="1" class="form-control" name="otp[]" required>
                    
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">
                    <input type="hidden" name="role" value="<?php echo htmlspecialchars($_GET['role']); ?>">
                </div>
                <button type="submit" class="btn btn-verify">Verify OTP</button>
            </form>
            <div class="resend-otp">
                <span>Didn't receive the code?</span>
                <form action="forgetverifyotp.php" method="post" style="display:inline;">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">
                    <input type="hidden" name="role" value="<?php echo htmlspecialchars($_GET['role']); ?>">
                    <input type="hidden" name="resend_otp" value="1">
                    <button type="button" class="btn btn-link" id="resendOtp">Resend OTP</button>
                </form>
            </div>
        </div>
    </center>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.otp-form');
            const otpInputs = document.querySelectorAll('.otp-input input');
            const resendOtp = document.getElementById('resendOtp');

            // Auto-focus next input in OTP
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (this.value.length === this.maxLength) {
                        if (index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }
                    }
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !this.value) {
                        if (index > 0) {
                            otpInputs[index - 1].focus();
                        }
                    }
                });
            });

            resendOtp.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to resend the OTP to this email: <?php echo htmlspecialchars($_GET['email']); ?>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, resend it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const resendForm = document.querySelector('.resend-otp form');
                        const formData = new FormData(resendForm);

                        fetch('forgetverifyotp.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: data.message,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: data.message,
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'An error occurred while resending the OTP.',
                            });
                        });
                    }
                });
            });

            <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_otp'): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Invalid OTP. Please try again.',
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>