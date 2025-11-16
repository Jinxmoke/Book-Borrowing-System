<?php
ob_start();
session_start();
include('navbar.php');
include('./config/connect.php');

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];


    $select = "SELECT * FROM user_info WHERE email = '$email' AND status = 'enabled'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);


        $hashed_pass = md5($pass);


        if ($hashed_pass === $row['password']) {

            $_SESSION['member_id'] = $row['member_id'];
            $_SESSION['name'] = $row['name'];


            if ($row['role'] == 'admin') {
                header('Location: admin/homepage.php');
                exit();
            } elseif ($row['role'] == 'user') {
                header('Location: user/user_catalogForm.php');
                exit();
            } elseif ($row['role'] == 'lender') {
                header('Location: lender/ldr_dashboardForm.php');
                exit();
            }
        } else {
            $error[] = 'Incorrect email or password!';
        }
    } else {
        $error[] = 'No user found with that email or your account is disabled!';
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');


        .login-form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 450px;
            margin: auto;
            margin-top: 100px;
            margin-bottom: 180px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .login-form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
        }

        .login-h3 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: #06402A;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
        }

        .login-sign-in-text {
            text-align: center;
            color: #333;
            margin-bottom: 2rem;
            font-size: 1.1rem;
            font-family: 'Poppins', sans-serif;
        }

        .login-input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .login-input-group input {
            width: 100%;
            padding: 1rem;
            border: none;
            border-bottom: 2px solid #ddd;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: transparent;
            color: #333;
        }

        .login-input-group input:focus {
            outline: none;
            border-color: #06402A;
        }

        .login-input-group label {
            position: absolute;
            top: 1rem;
            left: 0;
            font-size: 1rem;
            color: #999;
            transition: all 0.3s ease;
            pointer-events: none;
            font-family: 'Poppins', sans-serif;
        }

        .login-input-group input:focus + label,
        .login-input-group input:not(:placeholder-shown) + label {
            top: -0.5rem;
            font-size: 0.8rem;
            color: #06402A;
            font-family: 'Poppins', sans-serif;
        }

        .login-form-btn {
            width: 100%;
            padding: 1rem;
            background-color: #06402A;
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            font-family: 'Poppins', sans-serif;
        }

        .login-form-btn::after {
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

        .login-form-btn:hover::after {
            width: 300px;
            height: 300px;
            margin-left: -150px;
            margin-top: -150px;
        }

        .login-p {
            text-align: center;
            margin-top: 1.5rem;
            color: #333;
            font-family: 'Poppins', sans-serif;
        }

        .login-form-btn1 {
            display: block;
            width: 100%;
            padding: 1rem;
            background-color: #FD8418;
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

        .login-form-btn1::after {
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

        .login-form-btn1:hover::after {
            width: 300px;
            height: 300px;
            margin-left: -150px;
            margin-top: -150px;
        }

        .login-button-funct {
            color: #FD8418;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-forgot-password-container {
            text-align: center;
            margin-top: 10px;
            font-family: 'Poppins', sans-serif;
        }

        .login-forgot-password-container a {
            text-decoration: none;
            color: #FD8418;
            font-family: 'Poppins', sans-serif;
        }

        @media screen and (max-width: 600px) {
    .login-form-container {
        width: 95%;
        max-width: 95%;
        padding: 2rem 1.5rem;
        margin-top: 50px;
        margin-bottom: 100px;
        border-radius: 15px;
    }

    .login-h3 {
        font-size: 2rem;
        margin-bottom: 0.3rem;
    }

    .login-sign-in-text {
        font-size: 1rem;
        margin-bottom: 1.5rem;
    }

    .login-input-group input {
        font-size: 0.95rem;
        padding: 0.8rem;
    }

    .login-input-group label {
        font-size: 0.9rem;
    }

    .login-input-group input:focus + label,
    .login-input-group input:not(:placeholder-shown) + label {
        top: -0.5rem;
        font-size: 0.75rem;
    }

    .login-form-btn,
    .login-form-btn1 {
        font-size: 1rem;
        padding: 0.9rem;
    }

    .login-p {
        font-size: 0.9rem;
    }

    .login-forgot-password-container a {
        font-size: 0.9rem;
    }
}

@media screen and (max-width: 375px) {
    .login-form-container {
        width: 98%;
        max-width: 98%;
        padding: 1.5rem 1rem;
        margin-top: 30px;
        margin-bottom: 50px;
    }

    .login-h3 {
        font-size: 1.8rem;
    }

    .login-sign-in-text {
        font-size: 0.9rem;
    }

    .login-input-group input {
        font-size: 0.9rem;
        padding: 0.7rem;
    }

    .login-input-group label {
        font-size: 0.85rem;
    }

    .login-form-btn,
    .login-form-btn1 {
        font-size: 0.95rem;
        padding: 0.8rem;
    }

    .login-p {
        font-size: 0.85rem;
    }
}

a {
    color: #FD8418;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}
    </style>
</head>
<body>

<!-- Login Form -->
<div class="login-form-container">
    <form action="" method="post">
        <h3 class="login-h3">Log In</h3>
        <p class="login-sign-in-text">Sign in to your account</p>
        <div class="login-input-group">
            <input type="email" name="email" required placeholder=" ">
            <label for="email">Email</label>
        </div>
        <div class="login-input-group">
            <input type="password" name="password" required placeholder=" ">
            <label for="password">Password</label>
        </div>
        <input type="submit" name="submit" value="Log in" class="login-form-btn">
        <div class="login-forgot-password-container">
            <a href="forgot_password.php">Forgot your password?</a>
        </div>
        <p class="login-p">Don't have an account? <a href="register_form.php">Register</a></p>
    </form>
</div>

<script>

    function showSweetAlert(message) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: message,
            confirmButtonText: 'Try Again'
        });
    }

    <?php
    if (isset($error)) {
        foreach ($error as $err) {
            echo "showSweetAlert('" . addslashes($err) . "');";
        }
    }
    ?>

    const inputs = document.querySelectorAll('.login-input-group input');
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
</script>

</body>
</html>

<?php include 'guest/footer.php'; ?>
