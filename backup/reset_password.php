<?php
ob_start();  // Start output buffering
require './config/connect.php';
include 'navbar.php';
$message = '';
$showForm = false;

if (isset($_GET['member_id'], $_GET['reset_code'])) {
    $member_id = filter_input(INPUT_GET, 'member_id', FILTER_VALIDATE_INT);
    $reset_code = filter_input(INPUT_GET, 'reset_code', FILTER_SANITIZE_STRING);

    if ($member_id && $reset_code) {
        $stmt = $conn->prepare("SELECT member_id, expires_at FROM password_resets WHERE member_id = ? AND reset_code = ?");
        $stmt->bind_param("is", $member_id, $reset_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $reset_data = $result->fetch_assoc();
            $expires_at = new DateTime($reset_data['expires_at']);
            $current_time = new DateTime();

            if ($expires_at > $current_time) {
                $showForm = true;
            } else {
                $message = 'error:Your reset link has expired. Please request a new password reset.';
            }
        } else {
            $message = 'error:Invalid reset request. Please ensure you\'re using the correct link or request a new password reset.';
        }
    } else {
        $message = 'error:Invalid request parameters. Please try resetting your password again.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'], $_POST['confirm_password'])) {
    $member_id = filter_input(INPUT_GET, 'member_id', FILTER_VALIDATE_INT);
    $reset_code = filter_input(INPUT_GET, 'reset_code', FILTER_SANITIZE_STRING);
    $new_password = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
    $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);

    if ($member_id && $reset_code && $new_password && $confirm_password) {
        if ($new_password === $confirm_password) {
            $stmt = $conn->prepare("SELECT member_id, expires_at FROM password_resets WHERE member_id = ? AND reset_code = ?");
            $stmt->bind_param("is", $member_id, $reset_code);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $reset_data = $result->fetch_assoc();
                $expires_at = new DateTime($reset_data['expires_at']);
                $current_time = new DateTime();

                if ($expires_at > $current_time) {
                    // Hash the new password using md5
                    $hashed_pass = md5($new_password);

                    // Update the user's password
                    $stmt = $conn->prepare("UPDATE user_info SET password = ? WHERE member_id = ?");
                    $stmt->bind_param("si", $hashed_pass, $reset_data['member_id']);
                    $stmt->execute();

                    // Delete the reset request
                    $stmt = $conn->prepare("DELETE FROM password_resets WHERE member_id = ?");
                    $stmt->bind_param("i", $member_id);
                    $stmt->execute();

                    $message = 'success:Your password has been reset successfully. You can now log in with your new password.';
                    $showForm = false;

                    // Redirect to login form
                    header("Location: login_form.php");
                    exit();
                } else {
                    $message = 'error:Your reset link has expired. Please request a new password reset.';
                }
            } else {
                $message = 'error:Invalid reset request. Please ensure you\'re using the correct link or request a new password reset.';
            }
        } else {
            $message = 'error:Passwords do not match. Please try again.';
            $showForm = true;
        }
    } else {
        $message = 'error:Invalid request parameters. Please try resetting your password again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #0B5E2B;
            --secondary-color: #50E3C2;
            --background-color: #F0F4F8;
            --text-color: #333;
            --error-color: #FF4757;
            --success-color: #4CAF50;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;

        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem;
            border-radius: 20px;
            margin: auto;
            margin-top: 150px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
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
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            font-weight: 600;
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
            border-radius: 20px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .form-btn:hover::after {
            width: 300px;
            height: 300px;
            margin-left: -150px;
            margin-top: -150px;
        }

        .popup {
            text-align: center;
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 4px;
            font-weight: bold;
        }

        .popup.error {
            background-color: var(--error-color);
            color: white;
        }

        .popup.success {
            background-color: var(--success-color);
            color: white;
        }

        .security-warning {
            background-color: #FFF3CD;
            border: 1px solid #FFEEBA;
            color: #856404;
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }

    </style>
</head>
<body>
    <div class="form-container">
        <h3>Reset Password</h3>

        <?php if ($showForm): ?>
            <form method="POST" action="reset_password.php?member_id=<?= htmlspecialchars($_GET['member_id']) ?>&reset_code=<?= htmlspecialchars($_GET['reset_code']) ?>">
                <div class="input-group">
                    <input type="password" name="new_password" id="new_password" required placeholder=" ">
                    <label for="new_password">New Password</label>
                </div>
                <div class="input-group">
                    <input type="password" name="confirm_password" id="confirm_password" required placeholder=" ">
                    <label for="confirm_password">Confirm New Password</label>
                </div>
                <button type="submit" class="form-btn">Reset Password</button>
            </form>
        <?php elseif (empty($message)): ?>
            <p>Invalid or missing request parameters. Please request a new password reset.</p>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="popup <?= strpos($message, 'success:') === 0 ? 'success' : 'error' ?>">
                <?= substr($message, strpos($message, ':') + 1) ?>
            </div>
            <?php if (strpos($message, 'error:') === 0): ?>
                <p>
                    <a href="forgot_password.php">Request a new password reset</a>
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
        <script>
            const passwordInputs = document.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.classList.add('focused');
                });

                input.addEventListener('blur', function() {
                    if (this.value === '') {
                        this.classList.remove('focused');
                    }
                });
            });
        </script>
    </body>
    </html>
    <?php ob_end_flush();  // Send output buffer to the browser ?>
