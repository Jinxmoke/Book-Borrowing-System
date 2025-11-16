<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Updated Footer Design</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        body {
            margin: 0;
            font-family: 'Poppins', Arial, sans-serif;
            line-height: 1.6;
        }

        .footer {
            background: linear-gradient(to bottom, #06402A, #052e1f);
            color: #e0e0e0;
            padding: 60px 20px 30px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-branding,
        .footer-links,
        .footer-newsletter {
            margin-bottom: 30px;
        }

        .footer-branding {
            flex-basis: 25%;
        }

        .footer-links {
            flex-basis: 20%;
        }

        .footer-newsletter {
            flex-basis: 35%;
        }

        .footer h3 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 600;
            position: relative;
            padding-bottom: 10px;
        }

        .footer h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background-color: #FD8418;
        }

        .footer p,
        .footer ul {
            margin: 0;
            padding: 0;
            margin-top: 10px;
            list-style: none;
            line-height: 1.8;
            font-size: 14px;
        }

        .footer ul li {
            margin-bottom: 8px;
            transition: transform 0.3s ease;
        }

        .footer ul li:hover {
            transform: translateX(5px);
        }

        .footer a {
            color: #e0e0e0;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: #FFA550;
        }

        .newsletter-form {
            display: flex;
            margin-top: 15px;
        }

        .newsletter-form textarea {
            padding: 12px;
            border: none;
            border-radius: 5px 0 0 5px;
            flex: 1;
            font-size: 14px;
            background-color: #0a5c3d;
            color: #fff;
            resize: none;
        }

        .newsletter-form button {
            padding: 12px 25px;
            border: none;
            background-color: #FD8418;
            color: white;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .newsletter-form button:hover {
            background-color: #FFA550;
        }

        .footer-bottom {
            border-top: 1px solid #0a5c3d;
            padding-top: 20px;
            text-align: center;
            color: #a0a0a0;
            font-size: 12px;
            max-width: 1200px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
            }

            .footer-branding,
            .footer-links,
            .footer-newsletter {
                flex-basis: 100%;
                margin-bottom: 30px;
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
                       Welcome to CPLBLS, a seamless platform for borrowing and sharing books. Whether you're looking for physical books or digital e-books, our system makes it easy to explore, borrow, and enjoy a wide range of titles.
                   </p>
                   <div class="social-icons">
                       <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                   </div>
               </div>
               <nav class="footer-links">
                   <h3>QUICK LINKS</h3>
                   <ul>
                     <li><a href="user_catalogForm.php">Catalog</a></li>
                     <li><a href="user_myborrowedForm.php">My Borrowed Books</a></li>
                     <li><a href="user_historyForm.php">History</a></li>
                     <li><a href="user_bookmarkForm.php">Bookmarks</a></li>
                   </ul>
               </nav>
               <div class="footer-newsletter">
                   <h3>GIVE US A FEEDBACK</h3>
                   <form class="newsletter-form" id="feedbackForm" method="POST">
                       <textarea name="feedback" placeholder="Send Us your feedback" aria-label="Your feedback" required></textarea>
                       <button type="submit">SEND</button>
                   </form>
                   <p>If you enjoy our website, we'd love to hear from you! Your feedback helps us to continually improve and provide the best experience possible.</p>
               </div>
           </div>
           <div class="footer-bottom">
               <p>Caloocan Public Library Borrowing System</p>
           </div>
       </footer>

       <script>
           document.getElementById("feedbackForm").addEventListener("submit", function(event) {
               event.preventDefault(); // Prevent normal form submission

               const formData = new FormData(this);

               fetch('user_submitFeedback.php', {
                   method: 'POST',
                   body: formData
               })
               .then(response => response.json())
               .then(data => {
                   if (data.status === 'success') {
                       Swal.fire({
                           title: 'Success!',
                           text: data.message,
                           icon: 'success',
                           confirmButtonText: 'OK'
                       });
                       document.getElementById("feedbackForm").reset(); // Reset the form
                   } else {
                       Swal.fire({
                           title: 'Error!',
                           text: data.message,
                           icon: 'error',
                           confirmButtonText: 'OK'
                       });
                   }
               })
               .catch(error => {
                   Swal.fire({
                       title: 'Error!',
                       text: 'An unexpected error occurred.',
                       icon: 'error',
                       confirmButtonText: 'OK'
                   });
               });
           });
       </script>
   </body>
   </html>
