<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caloocan Public Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');


        .footer {
            background: linear-gradient(to bottom, #06402A, #052e1f);
            color: #e0e0e0;
            padding: 40px 15px 20px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
            gap: 20px;
        }

        .footer-branding,
        .footer-links,
        .footer-newsletter {
            margin-bottom: 20px;
            flex: 1;
            min-width: 250px;
        }

        .footer h3 {
            color: #fff;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
            position: relative;
            padding-bottom: 8px;
            font-family: 'Poppins', Arial, sans-serif;
        }

        .footer h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 2px;
            background-color: #FD8418;
            font-family: 'Poppins', Arial, sans-serif;
        }

        .footer p,
        .footer ul {
            margin: 0;
            padding: 0;
            margin-top: 10px;
            list-style: none;
            line-height: 1.8;
            font-size: 14px;
            font-family: 'Poppins', Arial, sans-serif;
        }

        .footer ul li {
            margin-bottom: 8px;
            transition: transform 0.3s ease;
            font-family: 'Poppins', Arial, sans-serif;
        }

        .footer ul li:hover {
            transform: translateX(5px);
            font-family: 'Poppins', Arial, sans-serif;
        }

        .footer a {
            color: #e0e0e0;
            text-decoration: none;
            transition: color 0.3s ease;
            font-family: 'Poppins', Arial, sans-serif;
        }

        .footer a:hover {
            color: #FFA550;
            font-family: 'Poppins', Arial, sans-serif;
        }

        .newsletter-form {
            display: flex;
            margin-top: 15px;
            font-family: 'Poppins', Arial, sans-serif;
        }

        .newsletter-form input[type="text"] {
            padding: 10px;
            border: none;
            border-radius: 5px 0 0 5px;
            flex: 1;
            font-size: 14px;
            background-color: #0a5c3d;
            color: #fff;
            font-family: 'Poppins', Arial, sans-serif;
        }

        .newsletter-form button {
            padding: 10px 20px;
            border: none;
            background-color: #FD8418;
            color: white;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            font-family: 'Poppins', Arial, sans-serif;
        }

        .social-icons {
            display: flex;
            margin-top: 15px;
        }

        .social-icons a {
            color: #a0a0a0;
            margin-right: 15px;
            text-decoration: none;
            font-size: 18px;
            transition: color 0.3s ease, transform 0.3s ease;
            font-family: 'Poppins', Arial, sans-serif;
        }

        .footer-bottom {
            border-top: 1px solid #0a5c3d;
            padding: 15px;
            text-align: center;
            color: #a0a0a0;
            font-size: 12px;
            max-width: 1200px;
            margin: 0 auto;
            font-family: 'Poppins', Arial, sans-serif;
        }

        /* Enhanced Mobile Responsiveness */
        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
                align-items: center;
                text-align: center;
                font-family: 'Poppins', Arial, sans-serif;
            }

            .footer-branding,
            .footer-links,
            .footer-newsletter {
                width: 100%;
                max-width: 400px;
                margin-bottom: 30px;
                font-family: 'Poppins', Arial, sans-serif;
            }

            .footer h3::after {
                left: 50%;
                transform: translateX(-50%);
                font-family: 'Poppins', Arial, sans-serif;
            }

            .newsletter-form {
                justify-content: center;
                font-family: 'Poppins', Arial, sans-serif;
            }

            .social-icons {
                justify-content: center;
                font-family: 'Poppins', Arial, sans-serif;
            }

            .footer ul li {
                text-align: center;
                font-family: 'Poppins', Arial, sans-serif;
            }
        }

        @media (max-width: 375px) {
            .footer-content {
                padding: 0 10px;
            }

            .newsletter-form input[type="text"],
            .newsletter-form button {
                font-size: 12px;
                padding: 8px;
                font-family: 'Poppins', Arial, sans-serif;
            }

            .footer h3 {
                font-size: 16px;
                font-family: 'Poppins', Arial, sans-serif;
            }

            .footer p,
            .footer ul {
                font-size: 13px;
                font-family: 'Poppins', Arial, sans-serif;
            }
        }
    </style>
</head>
<body>
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-branding">
                <h3>ABOUT US</h3>
                <p>
                    Welcome to CPLBLS a seamless platform for borrowing and sharing books. Whether you're looking for physical books or digital e-books, our system makes it easy to explore, borrow, and enjoy a wide range of titles.
                </p>
                <div class="social-icons">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>
            <nav class="footer-links">
                <h3>QUICK LINKS</h3>
                <ul>
                  <li><a href="../index.php">Home</a></li>
                  <li><a href="#features">Features</a></li>
                  <li><a href="#catalog">Catalog</a></li>
                  <li><a href="../index.php#testimonial">Testimonials</a></li>
                  <li><a href="../login_form.php">Join Now</a></li>
                </ul>
            </nav>
            <div class="footer-newsletter">
                <h3>GIVE US A FEEDBACK</h3>
                <form id="feedbackForm" class="newsletter-form">
                    <input type="text" id="feedbackInput" placeholder="Send Us a feedback">
                    <button type="submit">SEND</button>
                </form>
                <p>If you enjoy our website, we'd love to hear from you! Your feedback helps us to continually improve and provide the best experience possible.</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Caloocan Public Library Book Borrowing</p>
        </div>
    </footer>

    <!-- SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent form from submitting

            // Check if user is logged in (you would replace this with your actual login check)
            const isLoggedIn = false; // Set this to true when user is logged in

            if (!isLoggedIn) {
                // Show SweetAlert login prompt
                Swal.fire({
                    title: 'Login Required',
                    text: 'Please log in before sending feedback.',
                    icon: 'warning',
                    confirmButtonText: 'Go to Login',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to login page
                        window.location.href = 'login_form.php';
                    }
                });
            } else {
                // Proceed with feedback submission
                // Add your feedback submission logic here
                Swal.fire({
                    title: 'Feedback Sent!',
                    text: 'Thank you for your feedback.',
                    icon: 'success'
                });
            }
        });
    </script>
</body>
</html>
