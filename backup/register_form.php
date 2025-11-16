<?php
include('./config/connect.php');
include('navbar.php');

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $user_type = 'user';

    $select = "SELECT * FROM user_info WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $error = 'User already exists!';
    } else {

        if (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long!';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $error = 'Password must contain at least one uppercase letter!';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $error = 'Password must contain at least one lowercase letter!';
        } elseif ($password != $cpassword) {
            $error = 'Passwords do not match!';
        } else {
            $pass = md5($password);
            $insert = "INSERT INTO user_info (name, email, contact, password, role, address) VALUES ('$name', '$email', '$contact', '$pass', '$user_type', '$address')";

            if (mysqli_query($conn, $insert)) {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Registration successful!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'login_form.php';
                            }
                        });
                    });
                </script>";
            } else {
                $error = "Insertion failed: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <style>
     @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

     * {
         margin: 0;
         padding: 0;
         box-sizing: border-box;
     }

     .register-form-container {
         background: #fff;
         padding: 3rem;
         border-radius: 20px;
         box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
         width: 90%;
         max-width: 500px;
         margin: 100px auto 140px;
         position: relative;
     }

     .registerh3 {
         text-align: center;
         font-size: 2.5rem;
         margin-bottom: 1.5rem;
         color: #06402A;
         font-weight: 600;
         font-family: 'Poppins', sans-serif;
     }

     .register-input-group {
         position: relative;
         margin-bottom: 1.5rem;
     }

     .register-input-group input {
         width: 100%;
         padding: 1rem;
         border: none;
         border-bottom: 2px solid #ddd;
         font-size: 1rem;
         transition: all 0.3s ease;
         background: transparent;
         color: #333;
     }

     .register-input-group input:focus {
         outline: none;
         border-color: #06402A;
     }

     .register-input-group label {
         position: absolute;
         top: 1rem;
         left: 0;
         font-size: 13px;
         color: #999;
         transition: all 0.3s ease;
         pointer-events: none;
         font-family: 'Poppins', sans-serif;
     }

     .register-input-group input:focus + label,
     .register-input-group input:not(:placeholder-shown) + label {
         top: -0.5rem;
         font-size: 0.8rem;
         color: #06402A;
     }

     .register-form-row {
         display: flex;
         gap: 1rem;
         flex-wrap: wrap;
     }

     .register-form-row .register-input-group {
         flex: 1 1 calc(50% - 0.5rem);
         min-width: 120px;
     }

     .register-form-btn {
         width: 100%;
         padding: 1rem;
         background-color: #06402A;
         color: white;
         border: none;
         border-radius: 20px;
         font-size: 1.1rem;
         cursor: pointer;
         transition: all 0.3s ease;
         font-family: 'Poppins', sans-serif;
     }

     .register-form-btn:hover {
         background-color:#FD8418;
     }

     .already {
         text-align: center;
         margin-top: 1.5rem;
         color: #333;
         font-family: 'Poppins', sans-serif;
     }

     a {
         color: #FD8418;
         text-decoration: none;
         font-weight: 600;
         transition: color 0.3s ease;
     }

     a:hover {
         color: #06402A;
     }

     @media (max-width: 768px) {
         .register-form-container {
             width: 95%;
             padding: 2rem;
             margin: 50px auto 70px;
             font-family: 'Poppins', sans-serif;
         }

         .registerh3 {
             font-size: 2rem;
             font-family: 'Poppins', sans-serif;
         }

         .register-input-group input {
             font-size: 0.9rem;
             font-family: 'Poppins', sans-serif;
         }

         .register-form-btn {
             font-size: 1rem;
             font-family: 'Poppins', sans-serif;
         }
     }

     @media (max-width: 480px) {
         .register-form-container {
             padding: 1.5rem;
             margin: 30px auto 50px;
             margin-top: 50px;
             margin-bottom: 100px;
             font-family: 'Poppins', sans-serif;
         }

         .registerh3 {
             font-size: 1.8rem;
             margin-bottom: 1rem;
             font-family: 'Poppins', sans-serif;
         }

         .register-input-group {
             margin-bottom: 1rem;
             font-family: 'Poppins', sans-serif;
         }

         .register-input-group input {
             padding: 0.8rem;
             font-size: 0.85rem;
             font-family: 'Poppins', sans-serif;
         }

         .register-input-group label {
             font-size: 12px;
             font-family: 'Poppins', sans-serif;
         }

         .register-form-row .register-input-group {
             flex: 1 1 100%;
         }

         .register-form-btn {
             padding: 0.8rem;
             font-size: 0.9rem;
             font-family: 'Poppins', sans-serif;
         }

         .already {
             font-size: 0.9rem;
             font-family: 'Poppins', sans-serif;
         }
     }

     @media (max-width: 320px) {
         .register-form-container {
             padding: 1rem;
         }

         .registerh3 {
             font-size: 1.5rem;
         }

         .register-input-group input {
             padding: 0.7rem;
             font-size: 0.8rem;
         }

         .register-form-btn {
             padding: 0.7rem;
             font-size: 0.85rem;
         }
     }
     .modal4 {
         display: none;
         position: fixed;
         z-index: 1000;
         left: 0;
         top: 0;
         width: 100%;
         height: 100%;
         overflow: auto;
         background-color: rgba(0, 0, 0, 0.5);
         backdrop-filter: blur(5px);
         font-family: 'Poppins', sans-serif;
     }

     .modal-content {
         background-color: #f9f9f9;
         margin: 10% auto;
         padding: 20px;
         border-radius: 10px;
         width: 100%;
         height: 650px;
         max-width: 600px;
         box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
         position: relative;
         font-family: 'Poppins', sans-serif;
     }

     .modal-content .close {
         color: #aaa;
         float: right;
         font-size: 28px;
         font-weight: bold;
         cursor: pointer;
         transition: color 0.3s ease;
     }

     .modal-content .close:hover,
     .modal-content .close:focus {
         color: #333;
         text-decoration: none;
     }

     .modal-content h2 {
         color: #0B4208;
         border-bottom: 2px solid #0B4208;
         padding-bottom: 10px;
         margin-bottom: 20px;
         text-align: center;
         font-family: 'Poppins', sans-serif;
     }

     .modal-body {
         padding: 0 15px;
     }

     .modal-body h3 {
         color: #0B4208;
         margin-top: 20px;
         margin-bottom: 10px;
         border-left: 4px solid #FD8418;
         padding-left: 10px;
         font-family: 'Poppins', sans-serif;
     }

     .modal-body p {
         color: #34495e;
         line-height: 1.6;
         margin-bottom: 15px;
         font-family: 'Poppins', sans-serif;
     }

     .tos {
       font-size: 12px;
       margin-bottom: 10px;
       font-family: 'Poppins', sans-serif;
     }

     .checkbox-group {
       margin-bottom: 20px;
       font-size: 12px;
       font-family: 'Poppins', sans-serif;
     }
    </style>
</head>
<body>

    <!-- Register Form -->
    <div class="register-form-container">
        <form action="" method="post" onsubmit="return validatePassword()">
            <h3 class="registerh3">Register</h3>
            <div class="register-form-row">
                <div class="register-input-group">
                    <input type="text" name="name" required placeholder=" ">
                    <label for="name">Username</label>
                </div>
                <div class="register-input-group">
                    <input type="text" name="contact" required placeholder=" ">
                    <label for="contact">Contact Info</label>
                </div>
            </div>

            <div class="register-form-row">
                <div class="register-input-group">
                    <input type="password" name="password" id="password" required placeholder=" ">
                    <label for="password">Password</label>
                </div>
                <div class="register-input-group">
                    <input type="password" name="cpassword" id="cpassword" required placeholder=" ">
                    <label for="cpassword">Confirm Password</label>
                </div>
            </div>

            <div class="register-input-group">
                <input type="email" name="email" required placeholder=" ">
                <label for="email">Email</label>
            </div>

            <div class="register-input-group">
                <input type="text" name="address" required placeholder=" ">
                <label for="address">Address</label>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" name="terms"required>
                <label for="terms">By creating an account you agree to our</label>
            <a class="tos" href="terms_and_condition.php" >terms and condition</a> and our <a class="tos" href="privacy_policy.php">privacy policy</a>
            </div>

            <input type="submit" name="submit" value="Register" class="register-form-btn">
            <p class="already">Already have an account? <a href="login_form.php">Log In</a></p>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script>
        function validatePassword() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("cpassword").value;

            if (password.length < 8) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Password must be at least 8 characters long!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            if (!/[A-Z]/.test(password)) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Password must contain at least one uppercase letter!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            if (!/[a-z]/.test(password)) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Password must contain at least one lowercase letter!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            if (password !== confirmPassword) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Passwords do not match!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            return true;
        }

        const inputs = document.querySelectorAll('.register-input-group input');
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

    <?php
    if (isset($error)) {
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error!',
                    text: '$error',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        </script>
        ";
    }
    ?>
</body>
</html>


<?php include 'guest/footer.php'; ?>
