<?php
ob_start();  // Start output buffering
include 'navbar.php';

require './vendor/autoload.php';
require './config/connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resetCode = isset($_POST['reset_code']) ? trim($_POST['reset_code']) : '';

    if (!empty($resetCode)) {
        $stmt = $conn->prepare("SELECT member_id, email, expires_at FROM password_resets WHERE reset_code = ?");
        $stmt->bind_param("s", $resetCode);
        $stmt->execute();
        $resetResult = $stmt->get_result();

        if ($resetResult->num_rows > 0) {
            $resetRecord = $resetResult->fetch_assoc();
            $currentTime = new DateTime();
            $expiresAt = new DateTime($resetRecord['expires_at']);

            if ($expiresAt > $currentTime) {

                header("Location: reset_password?member_id=" . $resetRecord['member_id'] . "&reset_code=" . urlencode($resetCode));
                exit();
            } else {
                $message = 'error:Reset code has expired. Please request a new one.';
            }
        } else {
            $message = 'error:Invalid reset code.';
        }
    } else {
        $message = 'error:Please enter the reset code.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
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
        font-family: 'Poppins', sans-serif;
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
        font-family: 'Poppins', sans-serif;
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
        overflow: hidden;
        position: relative;
    }

    .form-btn:hover::after {
        width: 300px;
        height: 300px;
        margin-left: -150px;
        margin-top: -150px;
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
        <h3>Verify Code</h3>

        <form action="" method="POST">
            <div class="input-group">
                <input type="text" name="reset_code" id="reset_code" required placeholder=" ">
                <label for="reset_code">Enter Reset Code</label>
            </div>

            <button type="submit" class="form-btn">Verify Code</button>
        </form>

        <?php if (!empty($message)) { ?>
            <div class="popup show" style="background-color: var(--error-color);"><?= $message ?></div>
        <?php } ?>
    </div>

    <script>
        const resetInput = document.querySelector('#reset_code');
        resetInput.addEventListener('focus', function() {
            resetInput.classList.add('focused');
        });

        resetInput.addEventListener('blur', function() {
            if (resetInput.value === '') {
                resetInput.classList.remove('focused');
            }
        });
    </script>
</body>
</html>
<?php ob_end_flush();  // Send output buffer to the browser ?>
