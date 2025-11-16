<?php
include('../config/connect.php');
include 'navbar.php';

function getPopularBooks($conn) {
    $popular_books = [];

    $sql = "
        SELECT mb.*,
               ROUND(AVG(bc.rating), 1) as average_rating,
               COUNT(bc.rating) as review_count
        FROM manage_books mb
        LEFT JOIN book_comments bc ON mb.book_id = bc.book_id
        GROUP BY mb.book_id
        HAVING average_rating >= 4.0 AND average_rating IS NOT NULL
        ORDER BY average_rating DESC, review_count DESC
        LIMIT 10
    ";

    $result = $conn->query($sql);

    if ($result === false) {
        error_log("Popular books query failed: " . $conn->error);
        return $popular_books;
    }

    while ($row = $result->fetch_assoc()) {
        $popular_books[] = $row;
    }
    return $popular_books;
}

$popular_books = getPopularBooks($conn);
$popularBooksJson = json_encode($popular_books);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popular Books</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="user_style.css">
    <style>
        .popular-book-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }

        .popular-book-header {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .popular-rating {
            display: flex;
            align-items: center;
            color:#FD8418;
            gap: 0.5rem;
        }

        .popular-rating-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #FD8418;
        }

        .popular-star {
            color: #FD8418;
        }

        .popular-book-title {
            font-size: 2rem;
            margin: 0;
            color: #333333;
        }

        .popular-book-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .popular-tag {
            background-color: #ffffff;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.9rem;
            color: #666666;
            border: 1px solid #666666;
        }

        .popular-book-content {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 2rem;
            margin-bottom: 2rem;
            transition: opacity 0.3s ease;
        }

        .popular-book-info {
            background-color: #ffffff;
            padding: 1.5rem;
            border-radius: 8px;
            width: 840px;
            height: 400px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        .popular-summary h2 {
            margin-top: 0;
            color: #666666;
            font-size: 1.2rem;
        }

        .popular-status {
            margin-top: 1rem;
            color: #666666;
        }

        .popular-book-cover img {
            width: 300px;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 6px 6px 0 rgba(119,119,119,.75);
        }

        .popular-navigation-dots {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .popular-dot {
            width: 8px;
            height: 8px;
            background-color: #ffffff;
            border: 1px solid #FD8418;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .popular-dot.active {
            background-color: #FD8418;
        }

        .popular-books-slider {
            width: 100%;
            padding: 20px;
            background: #ffffff;
            margin-bottom: 30px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        .popular-slider-container {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .popular-slider-wrapper {
            width: 100%;
            overflow: hidden;
            position: relative;
        }

        .popular-slider-track {
            display: flex;
            transition: transform 0.5s ease;
            gap: 30px;
        }

        .popular-slider-item {
            flex: 0 0 240px;
            padding: 10px;
            background: #f7f7f7;
            border-radius: 8px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        .popular-slider-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .popular-slider-item h3 {
            font-size: 1rem;
            margin: 5px 0;
            color: #333333;
        }

        .popular-slider-arrow {
            background: rgba(0,0,0,0.1);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: absolute;
            z-index: 2;
            color: #333333;
        }

        .popular-slider-arrow.prev {
            left: -20px;
        }

        .popular-slider-arrow.next {
            right: -20px;
        }

        .popular-slider-arrow:hover {
            background: rgba(0,0,0,0.2);
        }

        .popular-review-count {
            font-size: 0.8rem;
            color: #666666;
        }

        @media (max-width: 768px) {
            .popular-book-content {
                grid-template-columns: 1fr;
            }

            .popular-book-cover {
                order: -1;
                text-align: center;
            }

            .popular-book-cover img {
                width: 100%;
                max-width: 300px;
            }

            .popular-book-info {
                width: auto;
            }

            .popular-slider-item {
                flex: 0 0 150px;
            }

            .popular-slider-arrow {
                width: 30px;
                height: 30px;
            }

            .popular-slider-arrow.prev {
                left: -15px;
            }

            .popular-slider-arrow.next {
                right: -15px;
            }
        }
    </style>
</head>
<body>
    <div class="popular-book-container">
        <?php if (!empty($popular_books)) { ?>
        <div class="popular-book-header">
            <div class="popular-rating">
                <i class="fas fa-star popular-star"></i>
                <span class="popular-rating-number"></span>
            </div>
            <h1 class="popular-book-title"></h1>
            <div class="popular-book-tags"></div>
        </div>

        <div class="popular-book-content" id="popularBookContent"></div>

        <div class="popular-navigation-dots">
            <?php
            for ($i = 0; $i < min(count($popular_books), 8); $i++) {
                echo "<span class='popular-dot" . ($i === 0 ? " active" : "") . "'></span>";
            }
            ?>
        </div>

        <div class="popular-books-slider">
            <h2>Popular Books</h2>
            <div class="popular-slider-container">
                <button class="popular-slider-arrow prev" id="prevSlide">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="popular-slider-wrapper">
                    <div class="popular-slider-track" id="popularSliderTrack">
                        <?php foreach ($popular_books as $book) { ?>
                            <div class="popular-slider-item">
                                <a href="user_descriptionForm.php?book_id=<?php echo urlencode($book['book_id']); ?>">
                                    <img src="../uploads/<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
                                </a>
                                <h3><?php echo $book['title']; ?></h3>
                                <div class="popular-rating">
                                    <?php
                                    $stars = str_repeat('★', floor($book['average_rating'])) .
                                             str_repeat('☆', 5 - floor($book['average_rating']));
                                    echo $stars . ' ' . $book['average_rating'];
                                    ?>
                                </div>
                                <div class="popular-review-count"><?php echo $book['review_count']; ?> reviews</div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <button class="popular-slider-arrow next" id="nextSlide">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        <?php } else { ?>
        <p>No popular books found.</p>
        <?php } ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const popularBooks = <?php echo $popularBooksJson; ?>;
        const sliderTrack = document.getElementById('popularSliderTrack');
        const prevButton = document.getElementById('prevSlide');
        const nextButton = document.getElementById('nextSlide');
        const bookContent = document.getElementById('popularBookContent');
        let currentPosition = 0;
        let currentBookIndex = 0;
        const itemWidth = 230;
        const autoSlideInterval = 3000;

        function updateSliderPosition() {
            sliderTrack.style.transform = `translateX(${-currentPosition * itemWidth}px)`;

            prevButton.style.opacity = currentPosition === 0 ? '0.5' : '1';
            nextButton.style.opacity =
                currentPosition >= popularBooks.length - Math.floor(sliderTrack.parentElement.offsetWidth / itemWidth)
                ? '0.5' : '1';
        }

        prevButton.addEventListener('click', () => {
            if (currentPosition > 0) {
                currentPosition--;
                updateSliderPosition();
            }
        });

        nextButton.addEventListener('click', () => {
            if (currentPosition < popularBooks.length - Math.floor(sliderTrack.parentElement.offsetWidth / itemWidth)) {
                currentPosition++;
                updateSliderPosition();
            }
        });

        updateSliderPosition();

        window.addEventListener('resize', updateSliderPosition);

        const dots = document.querySelectorAll('.popular-dot');
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                updateFeaturedBook(index);
            });
        });

        function updateFeaturedBook(index) {
            currentBookIndex = index;
            const book = popularBooks[index];

            bookContent.style.opacity = '0';

            setTimeout(() => {
                bookContent.innerHTML = `
                    <div class="popular-book-info">
                        <div class="popular-summary">
                            <h2>SUMMARY</h2>
                            <p>${book.description}</p>
                        </div>
                        <div class="popular-status">
                            <span>Status: ${book.status}</span>
                        </div>
                    </div>
                    <div class="popular-book-cover">
                        <img src="../uploads/${book.image}" alt="${book.title}" id="popularBookCover">
                    </div>
                `;

                document.querySelector('.popular-rating-number').textContent = book.average_rating;
                document.querySelector('.popular-book-title').textContent = book.title;

                const tagsContainer = document.querySelector('.popular-book-tags');
                tagsContainer.innerHTML = '';
                book.genre.split(',').forEach(tag => {
                    const span = document.createElement('span');
                    span.className = 'popular-tag';
                    span.textContent = tag.trim();
                    tagsContainer.appendChild(span);
                });
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === index);
                });

                bookContent.style.opacity = '1';
            }, 300);
        }

        function autoSlide() {
            currentBookIndex = (currentBookIndex + 1) % popularBooks.length;
            updateFeaturedBook(currentBookIndex);
        }

        let autoSlideTimer = setInterval(autoSlide, autoSlideInterval);

        bookContent.addEventListener('mouseenter', () => {
            clearInterval(autoSlideTimer);
        });

        bookContent.addEventListener('mouseleave', () => {
            autoSlideTimer = setInterval(autoSlide, autoSlideInterval);
        });

        updateFeaturedBook(0);
    });
    </script>
</body>
</html>
