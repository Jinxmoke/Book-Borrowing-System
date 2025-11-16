<?php
include 'navbar.php';
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>How Book Requests Work</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            margin-top: 50px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        .header {
            background-color: #f4ab42;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            font-weight: 300;
            font-size: 2rem;
        }
        .steps {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            padding: 30px;
        }
        .step {
            flex-basis: calc(50% - 20px);
            margin-bottom: 30px;
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .step:hover {
            transform: translateY(-5px);
        }
        .step-icon {
            text-align: center;
            font-size: 3rem;
            color: #4285F4;
            margin-bottom: 15px;
        }
        .step h2 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        .step p {
            color: #666;
            text-align: center;
        }
        @media (max-width: 768px) {
            .step {
                flex-basis: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>How it Works</h1>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-icon">📖</div>
                <h2>Book Request</h2>
                <p>Submit your book request through our library system. The librarian will review and process your request.</p>
            </div>
            <div class="step">
                <div class="step-icon">✅</div>
                <h2>Approval Process</h2>
                <p>The first member to request a specific book gets priority. The librarian will verify and approve the request.</p>
            </div>
            <div class="step">
                <div class="step-icon">📚</div>
                <h2>Physical Book Pickup</h2>
                <p>For physical books, you'll need to visit the library to collect your approved book. The Librarian will have it ready for you.</p>
            </div>
            <div class="step">
                <div class="step-icon">💻</div>
                <h2>E-Book Delivery</h2>
                <p>Electronic books are conveniently delivered directly to you. The librarian will send a PDF copy of the book you request.</p>
            </div>
        </div>
    </div>
</body>
</html>
