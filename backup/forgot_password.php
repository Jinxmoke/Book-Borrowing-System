<?php
include 'navbar.php';
require './vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './config/connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    if ($email) {
        $stmt = $conn->prepare("SELECT member_id FROM user_info WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $resetCode = bin2hex(random_bytes(8));
            $expires_at = date("Y-m-d H:i:s", strtotime('+1 hour'));

            // Delete any existing reset request for this user
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE member_id = ?");
            $stmt->bind_param("i", $user['member_id']);
            if (!$stmt->execute()) {
                $message = 'error:Failed to delete previous reset request.';
            } else {
                // Insert new reset request
                $stmt = $conn->prepare("INSERT INTO password_resets (member_id, email, reset_code, expires_at) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $user['member_id'], $email, $resetCode, $expires_at);

                if ($stmt->execute()) {
                    $mail = new PHPMailer(true);

                    try {
                        $mail->isSMTP();
                        $mail->Host       = 'mail.bsit-ucc.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'bookborrowing@bsit-ucc.com'; 
                        $mail->Password   = '9mR_aKw{CB#M';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        $mail->setFrom('bookborrowing@bsit-ucc.com', 'Caloocan Public Library');
                        $mail->addAddress($email);

                        $mail->isHTML(true);
                        $mail->Subject = 'Password Reset Request';
                        $mail->Body = "
                        <html>
                        <head>
                            <style>
                                body {
                                    font-family: 'Segoe UI', Arial, sans-serif;
                                    background-color: #f8f9fc;
                                    padding: 40px 20px;
                                    margin: 0;
                                    line-height: 1.6;
                                }
                                .email-container {
                                    background-color: #ffffff;
                                    border-radius: 16px;
                                    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
                                    padding: 40px;
                                    max-width: 600px;
                                    margin: 0 auto;
                                }
                                .logo-container {
                                    text-align: center;
                                    margin-bottom: 32px;
                                }
                                .logo {
                                    font-size: 24px;
                                    font-weight: bold;
                                    color: #45A049;
                                    text-transform: uppercase;
                                    letter-spacing: 2px;
                                }
                                h2 {
                                    color: #2D3748;
                                    text-align: center;
                                    font-size: 28px;
                                    margin: 0 0 24px 0;
                                    font-weight: 600;
                                }
                                p {
                                    color: #4A5568;
                                    font-size: 16px;
                                    margin: 16px 0;
                                }
                                .reset-code-container {
                                    background-color: #F7FAFC;
                                    border-radius: 12px;
                                    padding: 24px;
                                    margin: 32px 0;
                                    text-align: center;
                                }
                                .reset-code {
                                    font-size: 32px;
                                    font-weight: 600;
                                    -webkit-line-clamp: 2;
                                    color: #4C51BF;
                                    letter-spacing: 4px;
                                    margin: 0;
                                }
                                .info-box {
                                    background-color: #EBF8FF;
                                    border-left: 4px solid #4299E1;
                                    padding: 16px;
                                    margin: 24px 0;
                                    border-radius: 4px;
                                }
                                .divider {
                                    height: 1px;
                                    background-color: #E2E8F0;
                                    margin: 32px 0;
                                }
                                .footer {
                                    text-align: center;
                                    color: #718096;
                                    font-size: 14px;
                                }
                                .support-button {
                                    display: inline-block;
                                    background-color: #4C51BF;
                                    color: #ffffff;
                                    padding: 12px 24px;
                                    border-radius: 8px;
                                    text-decoration: none;
                                    font-weight: 500;
                                    margin-top: 16px;
                                }
                                .footer-links {
                                    margin-top: 16px;
                                }
                                .footer-links a {
                                    color: #4C51BF;
                                    text-decoration: none;
                                    margin: 0 8px;
                                }
                            </style>
                        </head>
                        <body>
                            <div class='email-container'>
                                <div class='logo-container'>
                                    <div class='logo'>Caloocan Public Library Borrowing Platform</div>
                                </div>

                                <h2>Password Reset Request</h2>

                                <p>Hello,</p>
                                <p>We received a request to reset your password. For your security, use the verification code below to complete the password reset process.</p>

                                <div class='reset-code-container'>
                                    <div class='reset-code'>$resetCode</div>
                                </div>

                                <div class='info-box'>
                                    <p style='margin: 0;'><strong>Important:</strong> This code will expire in 1 hour for security reasons.</p>
                                </div>

                                <div class='divider'></div>

                            </div>
                        </body>
                        </html>
                        ";

                        $mail->send();
                        $message = 'success:A password reset code has been sent to your email.';
                    } catch (Exception $e) {
                        $message = 'error:Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
                    }
                } else {
                    $message = 'error:Failed to insert reset request into database.';
                }
            }
        } else {
            $message = 'error:No account found with that email address.';
        }
    } else {
        $message = 'error:Invalid email address.';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #0B5E2B;
        --secondary-color: #50E3C2;
        --background-color: #F0F4F8;
        --text-color: #333;
        --error-color: #FF4757;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }


    .form-container {
        background: rgba(255, 255, 255, 0.95);
        padding: 3rem;
        margin: auto;
        margin-top: 150px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 450px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        position: relative;
        font-family: 'Poppins', sans-serif;
    }

    .form-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
    }

    h3 {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        color: var(--primary-color);
        font-weight: 600;
    }

    .sign-in-text {
        text-align: center;
        color: var(--text-color);
        margin-bottom: 2rem;
        font-size: 1.1rem;
    }

    .input-group {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .input-group input {
        width: 100%;
        padding: 1rem;
        border: none;
        border-bottom: 2px solid #ddd;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: transparent;
        color: var(--text-color);
    }

    .input-group input:focus {
        outline: none;
        border-color: var(--primary-color);
    }

    .input-group label {
        position: absolute;
        top: 1rem;
        left: 0;
        font-size: 1rem;
        color: #999;
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .input-group input:focus + label,
    .input-group input:not(:placeholder-shown) + label {
        top: -0.5rem;
        font-size: 0.8rem;
        color: var(--primary-color);
    }

    .form-btn {
        width: 100%;
        padding: 1rem;
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    .form-btn::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transition: width 0.3s ease, height 0.3s ease;
    }

    .form-btn:hover::after {
        width: 300px;
        height: 300px;
        margin-left: -150px;
        margin-top: -150px;
    }

    p {
        text-align: center;
        margin-top: 1.5rem;
        color: var(--text-color);
    }

    .form-btn1 {
        display: block;
        width: 100%;
        padding: 1rem;
        background-color: var(--secondary-color);
        color: white;
        border: none;
        border-radius: 50px;
        font-size: 1.1rem;
        text-align: center;
        text-decoration: none;
        margin-top: 1rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .form-btn1::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transition: width 0.3s ease, height 0.3s ease;
    }

    .form-btn1:hover::after {
        width: 300px;
        height: 300px;
        margin-left: -150px;
        margin-top: -150px;
    }

    .popup {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem;
        background-color: var(--error-color);
        color: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        opacity: 0;
        transition: all 0.3s ease;
        transform: translateY(-20px) scale(0.95);
    }

    .popup.show {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    @media (max-width: 480px) {
        .form-container {
            width: 95%;
            padding: 2rem;
        }
    }



        .reset-instructions {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .links-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .back-to-login, .enter-code {
            text-align: center;
            color: #FD8418;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .back-to-login:hover, .enter-code:hover {
            color: var(--secondary-color);
        }

        .popup {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 2rem;
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transition: all 0.3s ease;
            transform: translateY(-20px) scale(0.95);
            z-index: 2000;
            font-size: 0.9rem;
            max-width: 300px;
            text-align: center;
        }

        .popup.show {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    </style>
</head>
<body>


  <div class="form-container">
      <form action="forgot_password.php" method="POST">
          <h3>Forgot Password</h3>
          <p class="reset-instructions">
              Enter your email address and we'll send you code to reset your password.
          </p>
          <div class="input-group">
              <input type="email" name="email" required placeholder=" ">
              <label for="email">Email Address</label>
          </div>
          <input type="submit" name="submit" value="Send Reset Code" class="form-btn">
          <div class="links-container">
              <a href="reset_code.php" class="back-to-login">Enter Code</a>
          </div>
      </form>
  </div>

    <div id="popup" class="popup"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($message)): ?>
        const [type, msg] = '<?php echo $message; ?>'.split(':');
        showPopup(msg, type);
        <?php endif; ?>

        function showPopup(message, type = 'error') {
            const popup = document.getElementById('popup');
            popup.textContent = message;
            popup.style.backgroundColor = type === 'success' ? '#4CAF50' : '#FF4757';
            popup.classList.add('show');
            setTimeout(() => {
                popup.classList.remove('show');
            }, 3000);
        }

        const inputs = document.querySelectorAll('.input-group input');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentNode.classList.add('focus');
            });
            input.addEventListener('blur', () => {
                if (input.value === '') {
                    input.parentNode.classList.remove('focus');
                }
            });
        });
    });
    </script>
</body>
</html>
