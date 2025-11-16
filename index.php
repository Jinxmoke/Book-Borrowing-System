<?php
include 'navbar.php';
include('./config/connect.php'); // Database connection

$sql = "SELECT * FROM testimonial ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

$testimonials = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $testimonials[] = [
            'text' => $row['content'],
            'name' => $row['name'],
            'job' => 'Reader',
            'image' => $row['profile_picture'] ? "uploads/" . $row['profile_picture'] : './uploads/',
        ];
    }
} else {
    $testimonials = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <title>E-Book Lending Platform</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color:  #F8FAFC;
            color: #1F2937;
            line-height: 1.6;
        }


        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 20px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('uploads/bg.png') no-repeat center center/cover;
            filter: brightness(0.6);
            z-index: -1;
        }

        .hero-content {
            max-width: 800px;
            color:  #FFFFFF;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            font-weight: 700;
            letter-spacing: -1px;
            animation: slideUp 1s ease-out;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 30px;
            opacity: 0.9;
            animation: slideUp 1s ease-out 0.2s both;
        }

        .btn {
            display: inline-block;
            background-color:  #FFFFFF;
            color: #0B4208;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: -100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn:hover::after {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .features {
            padding: 100px 20px;
            background-color:  #FFFFFF;
        }

        .features h2 {
          text-align: center;
          margin-bottom: 30px;
          font-size: 2.5rem;
          color: #FD8418;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-item {
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .feature-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #4F46E5, #10B981);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 0;
        }

        .feature-item:hover::before {
            opacity: 0.05;
        }

        .feature-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-item i {
            font-size: 3rem;
            background: #111827;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
        }
        .testimonial-section h2 {
          text-align: center;
          margin-bottom: 50px;
          font-size: 2.5rem;
          background: #111827;
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
        }

        /* Featured E-Books section*/
        .catalog {
            padding: 40px 20px;
            background-color: #f8f8f8;
        }
        .catalog h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5rem;
            color: #111827;
        }
        .book-slider {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            overflow: hidden;
        }
        .book-container {
            display: flex;
            transition: transform 0.5s ease;
        }
        .book-item {
            width: 100%;
            padding: 10px;
            flex: 0 0 100%;
            display: flex;
            flex-direction: column;
        }
        .book-item img {
            width: 100%;
            height: 365px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 6px 6px 0 rgba(119,119,119,.75);
        }
        .book-info {
            margin-top: 10px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .book-info h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        .book-info p {
            font-size: 0.9rem;
            color: #666;
        }
        .slider-button {
            position: absolute;
            top: 40%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 1.5rem;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            z-index: 10;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .slider-button.prev {
            left: 10px;
        }
        .slider-button.next {
            right: 10px;
        }
        .slider-button:hover {
            background-color: rgba(0, 0, 0, 0.8);
            transform: translateY(-50%) scale(1.1);
        }
        .slider-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }


        @media (min-width: 769px) {
            .book-item {
                flex: 0 0 25%;
            }
        }


        .testimonial-section {
            padding: 100px 20px;
            background-color:  #F8FAFC;
        }

        .testimonial-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            overflow: hidden;
        }

        .testimonial-card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            transition: transform 0.3s ease;
        }

        .quote-mark {
            font-size: 4rem;
            color: #4F46E5;
            line-height: 1;
            margin-bottom: -1rem;
        }

        .testimonial-text {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #1F2937;
            margin-bottom: 1rem;
        }

        .highlight {
            color: #4F46E5;
            font-weight: bold;
        }

        .star-rating {
            font-size: 1.5rem;
            color: #FFD700;
            margin: 1rem 0;
        }

        .profile-section {
            display: flex;
            align-items: center;
            margin-top: 1rem;
        }

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 1rem;
            object-fit: cover;
        }

        .profile-info h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #1F2937;
        }

        .profile-info p {
            margin: 0;
            font-size: 0.9rem;
            color: #666;
        }

        .navigation-arrows {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }

        .nav-arrow {
            background: none;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            color: #FD8418;
            transition: transform 0.2s ease;
        }

        .nav-arrow:hover {
            transform: scale(1.1);
        }
        .view-all-btn {
            background-color: #063E29;
            color: white;
            margin-left: 550px;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .view-all-btn:hover {
            background-color: #063E29;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .testimonial-card {
                padding: 1.5rem;
            }

            .quote-mark {
                font-size: 3rem;
            }

            .testimonial-text {
                font-size: 1rem;
            }
        }

    </style>
</head>
<body>


    <section id="home" class="hero">
        <div class="hero-content">
            <h1>Book Borrowing</h1>
            <p>Discover, borrow, and read from the comfort of your device. Share your favorite reads and embark on your next adventure all with just a click!</p>
            <a href="#catalog" class="btn">Explore Our Catalog</a>
        </div>
    </section>

    <section id="features" class="features">
        <div class="feature-grid">
            <div class="feature-item">
                <i class="fas fa-book"></i>
                <h3>Growing Collection</h3>
                <p>Access to a continually expanding library of e-books and physical books across various genres and topics..</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <h3>Secure & Private</h3>
                <p>Your reading habits and personal information are always protected.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-users"></i>
                <h3>Community</h3>
                <p>Lend your books to other users, fostering a vibrant sharing environment!</p>
            </div>
        </div>
    </section>

    <section id="catalog" class="catalog">
         <h2>Featured Books</h2>
         <div class="book-slider">
             <button class="slider-button prev" id="prevBook" aria-label="Previous books">❮</button>
             <div class="book-container">
                 <?php
                 // Database connection
                 include('./config/connect.php');

                 // Fetch book data from manage_books table
                 $sql = "SELECT title, author, image FROM manage_books WHERE status = 'available'";
                 $result = $conn->query($sql);

                 if ($result->num_rows > 0):
                     $books = $result->fetch_all(MYSQLI_ASSOC);
                     foreach ($books as $index => $book):
                 ?>
                     <div class="book-item">
                         <img src="./uploads/<?php echo htmlspecialchars($book['image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                         <div class="book-info">
                             <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                             <p><?php echo htmlspecialchars($book['author']); ?></p>
                         </div>
                     </div>
                 <?php
                     endforeach;
                 else:
                 ?>
                     <p>No books available.</p>
                 <?php endif; ?>
             </div>
             <button class="view-all-btn" onclick="window.location.href='guest/guest_catalog'">View All</button>
             <button class="slider-button next" id="nextBook" aria-label="Next books">❯</button>
     </section>


     <section id="testimonial" class="testimonial-section">
       <h2>Testimonials</h2>
       <div class="testimonial-container">
         <?php if (count($testimonials) > 0): ?>
           <div class="testimonial-card">
             <div class="quote-mark">"</div>
             <p class="testimonial-text"><?= $testimonials[0]['text']; ?></p>
             <div class="star-rating">★★★★★</div>
             <div class="profile-section">
               <img src="<?= $testimonials[0]['image']; ?>" alt="<?= $testimonials[0]['name']; ?>" class="avatar">
               <div class="profile-info">
                 <h3><?= $testimonials[0]['name']; ?></h3>
                 <p><?= $testimonials[0]['job']; ?></p>
               </div>
             </div>
           </div>
         <?php else: ?>
           <p>No testimonials available at the moment.</p>
         <?php endif; ?>
         <div class="navigation-arrows">
           <button class="nav-arrow" id="prevBtn">&#8249;</button>
           <button class="nav-arrow" id="nextBtn">&#8250;</button>
         </div>
       </div>
     </section>


<section>
  <?php
   include 'footer.php';
   ?>
</section>


    <script>

    window.onload = function() {
    if (window.location.hash === '#testimonial') {
        const catalogSection = document.getElementById('testimonial');
        if (catalogSection) {
            catalogSection.scrollIntoView({ behavior: 'smooth' });
        }
    }
};
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        const testimonials = <?php echo json_encode($testimonials); ?>;

        let currentIndex = 0;

        function updateTestimonial() {
            const testimonial = testimonials[currentIndex];
            document.querySelector('.testimonial-text').innerHTML = testimonial.text;
            document.querySelector('.profile-info h3').textContent = testimonial.name;
            document.querySelector('.profile-info p').textContent = testimonial.job;
            document.querySelector('.avatar').src = testimonial.image;
        }

        document.getElementById('prevBtn').addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + testimonials.length) % testimonials.length;
            updateTestimonial();
        });

        document.getElementById('nextBtn').addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % testimonials.length;
            updateTestimonial();
        });

        // Initialize with the first testimonial
        updateTestimonial();

        document.addEventListener('DOMContentLoaded', function() {
             const bookContainer = document.querySelector('.book-container');
             const prevButton = document.getElementById('prevBook');
             const nextButton = document.getElementById('nextBook');
             const bookItems = document.querySelectorAll('.book-item');
             let currentIndex = 0;

             function updateSlider() {
                 const isMobile = window.innerWidth < 769;
                 const booksPerSlide = isMobile ? 1 : 4;
                 const slideWidth = 100 / booksPerSlide;

                 bookContainer.style.transform = `translateX(${-currentIndex * slideWidth}%)`;

                 prevButton.disabled = currentIndex === 0;
                 nextButton.disabled = currentIndex >= Math.ceil(bookItems.length / booksPerSlide) - 1;
             }

             prevButton.addEventListener('click', () => {
                 if (currentIndex > 0) {
                     currentIndex--;
                     updateSlider();
                 }
             });

             nextButton.addEventListener('click', () => {
                 const isMobile = window.innerWidth < 769;
                 const booksPerSlide = isMobile ? 1 : 4;
                 if (currentIndex < Math.ceil(bookItems.length / booksPerSlide) - 1) {
                     currentIndex++;
                     updateSlider();
                 }
             });

             window.addEventListener('resize', () => {
                 currentIndex = 0;
                 updateSlider();
             });

             // Initial setup
             updateSlider();
         });
</script>

</body>
</html>
