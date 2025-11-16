<?php
include('../config/connect.php');
include 'navbar.php';

$stmt = $conn->prepare("SELECT has_accepted_policy FROM user_info WHERE member_id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$hasAcceptedPolicy = $user['has_accepted_policy'];
$stmt->close();

$book_id = $_GET['book_id'];

// Query to fetch book details from manage_books
$query = "
    SELECT
        m.title, m.author, m.genre, m.publication_date, m.book_type, m.isbn, m.expiry_days, m.publisher,
        m.status, m.image, m.description
    FROM
        manage_books AS m
    WHERE
        m.book_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Book not found.");
}

$book = $result->fetch_assoc();
$stmt->close();

// Query for similar books from manage_books
$similar_books_query = "
    SELECT book_id, title, author, genre, description, image, status
    FROM manage_books
    WHERE genre LIKE CONCAT('%', ?, '%')
    AND book_id != ?
    LIMIT 4
";

$similar_stmt = $conn->prepare($similar_books_query);
$genre = explode(',', $book['genre'])[0];
$similar_stmt->bind_param("si", $genre, $book_id);
$similar_stmt->execute();
$similar_books = $similar_stmt->get_result();
$similar_stmt->close();

$similar_books_array = [];
while ($row = $similar_books->fetch_assoc()) {
    $similar_books_array[] = $row;
}

// Check if user can review (has borrowed and returned the book, and hasn't reviewed yet)
$can_review_query = "
    SELECT 1
    FROM returned_books
    WHERE book_id = ? AND member_id = ?
    AND NOT EXISTS (
        SELECT 1 FROM book_comments
        WHERE book_id = returned_books.book_id AND member_id = returned_books.member_id
    )
    LIMIT 1
";
$can_review_stmt = $conn->prepare($can_review_query);
$can_review_stmt->bind_param("ii", $book_id, $member_id);
$can_review_stmt->execute();
$can_review_result = $can_review_stmt->get_result();
$can_review = $can_review_result->num_rows > 0;
$can_review_stmt->close();

// Query to fetch the average rating and total number of ratings
$rating_query = "
    SELECT AVG(rating) as average_rating, COUNT(*) as total_ratings
    FROM book_comments
    WHERE book_id = ?
";

$rating_stmt = $conn->prepare($rating_query);
$rating_stmt->bind_param("i", $book_id);
$rating_stmt->execute();
$rating_result = $rating_stmt->get_result();
$rating_data = $rating_result->fetch_assoc();
$rating_stmt->close();

$average_rating = $rating_data['average_rating'] ? round($rating_data['average_rating'], 1) : 0;
$total_ratings = $rating_data['total_ratings'];

// Query to fetch comments
$comments_query = "
    SELECT bc.*, ui.name
    FROM book_comments bc
    JOIN user_info ui ON bc.member_id = ui.member_id
    WHERE bc.book_id = ?
    ORDER BY bc.created_at DESC
";
$comments_stmt = $conn->prepare($comments_query);
$comments_stmt->bind_param("i", $book_id);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();
$comments = $comments_result->fetch_all(MYSQLI_ASSOC);
$comments_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']) ?> - Book Description</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        line-height: 1.6;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        margin-top: 50px;
    }

    .book-header {
        display: flex;
        gap: 30px;
        margin-bottom: 40px;
    }

    .book-cover {
        flex-shrink: 0;
        width: 300px;
        height: 450px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .book-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .book-info {
        flex: 1;
    }

    .book-title {
        font-size: 2.5em;
        margin-bottom: 10px;
        color: #333;
    }

    .book-author {
        font-size: 1.2em;
        color: #666;
        margin-bottom: 20px;
    }

    .book-meta {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .book-meta-item {
        display: flex;
        align-items: center;
    }

    .book-meta-item i {
        margin-right: 10px;
        color: #0B4208;
    }

    .book-description {
        margin-bottom: 30px;
        line-height: 1.8;
    }

    .book-actions {
        display: flex;
        gap: 15px;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s, transform 0.3s;
    }

    .btn-primary {
        background-color: #FD8418;
        color: white;
    }

    .btn-primary:hover {
        background-color: #E67300;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background-color: #02a95c;
        color: #fff;
    }

    .btn-secondary:hover {
        background-color: #017741;
        transform: translateY(-2px);
    }

    .book-review-item {
        background-color: #ffffff;
        padding: 20px;
        margin-left: -30px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
    .book-review-header {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        margin-bottom: 20px;
        text-align: center;
    }

    .book-review-title {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin: 0;
    }

    .book-review-summary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        margin-top: 10px;
    }

    .book-average-rating {
        font-size: 30px;
        font-weight: bold;
        color: #fd8418;
    }

    .book-star-rating {
        display: flex;
        gap: 5px;
    }

    .book-star {
        font-size: 20px;
        color: #e0e0e0;
    }

    .book-star-filled {
        color: #fd8418;
    }

    .book-total-ratings {
        font-size: 14px;
        color: #555;
    }

    .book-review-toggle {
        background-color: transparent;
        border: none;
        color: #fd8418;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        margin-top: 10px;
    }

    .book-review-toggle:hover i {
        transform: rotate(180deg);
    }

    .book-review-content {
        display: none;
        padding: 10px;
    }

    .book-review-form {
        background-color: #f9f9f9;
        padding: 20px;
        margin-left: -30px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .book-review-form h3 {
        margin-top: 0;
        color: #333;
        margin-bottom: 20px;
    }

    .book-form-group {
        margin-bottom: 20px;
    }

    .book-form-group label {
        display: block;
        margin-bottom: 8px;
        color: #666;
        font-weight: 600;
    }

    .book-star-rating-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }

    .book-star-rating-input input {
        display: none;
    }

    .book-star-rating-input label {
        cursor: pointer;
        font-size: 30px;
        color: #ddd;
        transition: color 0.3s ease;
    }

    .book-star-rating-input label:hover,
    .book-star-rating-input label:hover ~ label,
    .book-star-rating-input input:checked ~ label {
        color: #FD8418;
    }

    .book-form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        resize: vertical;
        min-height: 100px;
    }

    .book-submit-review {
        background-color: #FD8418;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
        font-weight: 600;
    }

    .book-submit-review:hover {
        background-color: #E67300;
    }

    .book-review-list {
        display: grid;
        gap: 20px;
        margin-left: 20px;
    }

    .book-review-item {
      background-color: #ffffff;
      padding: 20px;
      margin-left: -30px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .book-review-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .book-reviewer-name {
        font-weight: 600;
        color: #333;
    }

    .book-review-text {
        color: #555;
        line-height: 1.6;
        margin-bottom: 10px;
    }

    .book-review-date {
        font-size: 0.9em;
        color: #888;
    }
    .similar-book-section {
        padding: 40px 0;
        border-radius: 15px;
        margin-left: -20px;
    }

    .similar-book-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 0 20px;
    }

    .similar-book-title {
        font-size: 1.8em;
        color: #2c3e50;
        font-weight: 600;
        position: relative;
    }

    .similar-book-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 50px;
        height: 4px;
        background-color: #FD8418;
        border-radius: 2px;
    }

    .similar-book-link {
        color: #FD8418;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
        display: flex;
        align-items: center;
    }

    .similar-book-link:hover {
        color: #E67300;
    }

    .similar-book-link i {
        margin-left: 5px;
        transition: transform 0.3s ease;
    }

    .similar-book-link:hover i {
        transform: translateX(3px);
    }

    .similar-book-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 25px;
        padding: 0 20px;
    }

    .similar-book-card {
        background-color: #fff;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .similar-book-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }

    .similar-book-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(to right, #FD8418, #FF6B35);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .similar-book-card:hover::before {
        opacity: 1;
    }

    .similar-book-cover {
        width: 100%;
        height: 320px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .similar-book-card:hover .similar-book-cover {
        transform: scale(1.05);
    }

    .similar-book-info {
        padding: 15px;
        background-color: #fff;
        position: relative;
        z-index: 1;
    }

    .similar-book-name {
        font-size: 1.2em;
        color: #2c3e50;
        margin-bottom: 8px;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .similar-book-card:hover .similar-book-name {
        color: #FD8418;
    }

    .similar-book-author {
        color: #7f8c8d;
        font-size: 0.9em;
        margin-bottom: 10px;
    }

    .similar-book-status {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.7em;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .status-available {
        background-color: rgba(76, 175, 80, 0.1);
        color: #4CAF50;
        border: 1px solid rgba(76, 175, 80, 0.3);
    }

    .status-borrowed {
        background-color: rgba(244, 67, 54, 0.1);
        color: #F44336;
        border: 1px solid rgba(244, 67, 54, 0.3);
    }

    /* Updated styles for dynamic star rating */
    .book-star-rating-input {
        display: inline-flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }

    .book-star-rating-input .star {
        font-size: 24px;
        color: #ccc;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .book-star-rating-input .star:hover,
    .book-star-rating-input .star:hover ~ .star,
    .book-star-rating-input .star.active,
    .book-star-rating-input .star.active ~ .star {
        color: #FD8418;
    }

    @media (max-width: 768px) {
        .book-header {
            flex-direction: column;
        }

        .book-cover {
            width: 100%;
            height: auto;
            aspect-ratio: 3/4;
        }

        .book-meta {
            grid-template-columns: 1fr;
        }

        .book-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }

        .similar-book-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
        }

        .similar-book-cover {
            height: 250px;
        }

        .similar-book-title {
            font-size: 1.5em;
        }
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="book-header">
            <div class="book-cover">
                <img src="../uploads/<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?> Cover">
            </div>
            <div class="book-info">
                <h1 class="book-title"><?= htmlspecialchars($book['title']) ?></h1>
                <p class="book-author">by <?= htmlspecialchars($book['author']) ?></p>
                <div class="book-meta">
                    <div class="book-meta-item">
                        <i class="fas fa-book"></i>
                        <span>Book Type: <?= htmlspecialchars($book['book_type']) ?></span>
                    </div>
                    <div class="book-meta-item">
                        <i class="fas fa-barcode"></i>
                        <span>ISBN: <?= htmlspecialchars($book['isbn']) ?></span>
                    </div>
                    <div class="book-meta-item">
                        <i class="fas fa-clock"></i>
                        <span>Expiry: <?= htmlspecialchars($book['expiry_days']) ?> Days</span>
                    </div>
                    <div class="book-meta-item">
                        <i class="fas fa-building"></i>
                        <span>Publisher: <?= htmlspecialchars($book['publisher']) ?></span>
                    </div>
                    <div class="book-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Published: <?= htmlspecialchars($book['publication_date']) ?></span>
                    </div>
                    <div class="book-meta-item">
                        <i class="fas fa-tag"></i>
                        <span>Genre: <?= htmlspecialchars($book['genre']) ?></span>
                    </div>
                </div>
                <p class="book-description"><?= nl2br(htmlspecialchars($book['description'])) ?></p>
                <div class="book-actions">
                    <?php if ($book['status'] === 'available'): ?>
                        <form action="user_borrowHandler.php" method="post" class="borrow-form" data-title="<?= htmlspecialchars($book['title']) ?>">
                            <input type="hidden" name="book_id" value="<?= htmlspecialchars($book_id) ?>">
                            <input type="hidden" name="title" value="<?= htmlspecialchars($book['title']) ?>">
                            <button type="button" class="btn btn-primary borrow-button">
                                <i class="fas fa-book-open"></i> Borrow
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>
                            <i class="fas fa-book-open"></i> Not Available
                        </button>
                    <?php endif; ?>
                    <form action="user_bookmarkHandler.php" method="post">
                        <input type="hidden" name="book_id" value="<?= htmlspecialchars($book_id) ?>">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-bookmark"></i> Bookmark
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Review Section -->
        <div class="book-review-section">
            <div class="book-review-header">
                <h2 class="book-review-title">Reviews</h2>
                <div class="book-review-summary">
                    <span class="book-average-rating"><?= number_format($average_rating, 1) ?></span>
                    <div class="book-star-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="book-star <?= $i <= round($average_rating) ? 'book-star-filled' : '' ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <span class="book-total-ratings">(<?= $total_ratings ?> reviews)</span>
                </div>
            </div>
            <div id="bookReviewContent" class="book-review-content" style="display: block;">
                <?php if ($can_review): ?>
                    <div class="book-review-form">
                        <h3>Submit Your Review</h3>
                        <form action="submit_review.php" method="post">
                            <input type="hidden" name="book_id" value="<?= htmlspecialchars($book_id) ?>">
                            <div class="book-form-group">
                                <label for="rating">Rating:</label>
                                <div class="book-star-rating-input">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="bookStar<?= $i ?>" name="rating" value="<?= $i ?>" required>
                                        <label for="bookStar<?= $i ?>">★</label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="book-form-group">
                                <label for="comment">Comment:</label>
                                <textarea name="comment" id="bookComment" required></textarea>
                            </div>
                            <button type="submit" class="book-submit-review">Submit Review</button>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="book-review-list">
                    <?php if (empty($comments)): ?>
                        <p>No reviews yet.</p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="book-review-item">
                                <div class="book-review-item-header">
                                    <span class="book-reviewer-name"><?= htmlspecialchars($comment['name']) ?></span>
                                    <span class="book-review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="book-star <?= $i <= $comment['rating'] ? 'book-star-filled' : '' ?>">★</span>
                                        <?php endfor; ?>
                                    </span>
                                </div>
                                <div class="book-review-text">
                                    <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                                </div>
                                <div class="book-review-date">
                                    Posted on: <?= date('F j, Y', strtotime($comment['created_at'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="similar-book-section">
            <div class="similar-book-header">
                <h2 class="similar-book-title">You may also like</h2>
                <a href="user_booksGenre?genre=<?= urlencode($genre) ?>" class="similar-book-link">
                    See all <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            <div class="similar-book-grid">
                <?php foreach ($similar_books_array as $similar_book): ?>
                    <div class="similar-book-card">
                        <a href="user_descriptionForm?book_id=<?= htmlspecialchars($similar_book['book_id']) ?>">
                            <img src="../uploads/<?= htmlspecialchars($similar_book['image']) ?>" alt="<?= htmlspecialchars($similar_book['title']) ?>" class="similar-book-cover">
                        </a>
                        <div class="similar-book-info">
                            <h3 class="similar-book-name"><?= htmlspecialchars($similar_book['title']) ?></h3>
                            <p class="similar-book-author">Author: <?= htmlspecialchars($similar_book['author']) ?></p>
                            <p class="similar-book-author">Status: <?= htmlspecialchars($similar_book['status']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Borrow button functionality
        const borrowButtons = document.querySelectorAll('.borrow-button');
        let currentForm;

        borrowButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                currentForm = this.closest('.borrow-form');
                const bookTitle = currentForm.dataset.title;

                Swal.fire({
                    title: 'Request Book',
                    text: `Do you want to request "${bookTitle}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#FD8418',
                    cancelButtonColor: '#00762d',
                    confirmButtonText: 'Yes, request it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        currentForm.submit();
                    }
                });
            });
        });

        // Bookmark functionality
        const bookmarkForms = document.querySelectorAll('form[action="user_bookmarkHandler.php"]');

        bookmarkForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('user_bookmarkHandler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Book has been added to your bookmarks',
                            icon: 'success',
                            confirmButtonColor: '#FD8418'
                        });
                    } else {
                        Swal.fire({
                            title: 'Notice',
                            text: data.message || 'Book is already in your bookmarks',
                            icon: 'info',
                            confirmButtonColor: '#FD8418'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#FD8418'
                    });
                });
            });
        });

        // Toggle reviews functionality
        const toggleButton = document.getElementById('bookToggleReviews');
        const reviewContent = document.getElementById('bookReviewContent');
        const toggleText = document.querySelector('.book-toggle-text');

        if (toggleButton && reviewContent) {
            toggleButton.addEventListener('click', function() {
                const isVisible = reviewContent.style.display === 'block';
                reviewContent.style.display = isVisible ? 'none' : 'block';
                toggleText.textContent = isVisible ? 'Show Reviews' : 'Hide Reviews';
                this.querySelector('i').style.transform = isVisible ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        }

        // Star rating functionality
        function initializeStarRating() {
            const ratingInputs = document.querySelectorAll('.book-star-rating-input input[type="radio"]');
            const ratingLabels = document.querySelectorAll('.book-star-rating-input label');
            let selectedRating = 0;

            // Function to update stars visual state
            function updateStars(rating, isHover = false) {
                ratingLabels.forEach((label) => {
                    const starValue = parseInt(label.getAttribute('for').replace('bookStar', ''));
                    if (starValue <= rating) {
                        label.style.color = '#FD8418';
                    } else {
                        label.style.color = isHover ? '#ccc' : (starValue <= selectedRating ? '#FD8418' : '#ccc');
                    }
                });
            }

            // Add click handlers to radio inputs
            ratingInputs.forEach((input) => {
                input.addEventListener('click', (e) => {
                    selectedRating = parseInt(e.target.value);
                    updateStars(selectedRating);
                });
            });

            // Add hover handlers to labels
            ratingLabels.forEach((label) => {
                const starValue = parseInt(label.getAttribute('for').replace('bookStar', ''));

                label.addEventListener('mouseenter', () => {
                    updateStars(starValue, true);
                });

                label.addEventListener('click', () => {
                    selectedRating = starValue;
                    const input = document.querySelector(`#bookStar${starValue}`);
                    if (input) {
                        input.checked = true;
                    }
                    updateStars(starValue);
                });
            });

            // Add mouseleave handler to the container
            const container = document.querySelector('.book-star-rating-input');
            if (container) {
                container.addEventListener('mouseleave', () => {
                    updateStars(selectedRating);
                });
            }
        }

        // Initialize star rating
        initializeStarRating();

        // Form submission handler for reviews
        const reviewForm = document.querySelector('.book-review-form form');
        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const rating = document.querySelector('.book-star-rating-input input:checked');
                const comment = document.getElementById('bookComment');

                if (!rating) {
                    Swal.fire({
                        title: 'Rating Required',
                        text: 'Please select a rating',
                        icon: 'warning',
                        confirmButtonColor: '#FD8418'
                    });
                    return;
                }

                if (!comment.value.trim()) {
                    Swal.fire({
                        title: 'Comment Required',
                        text: 'Please enter a comment',
                        icon: 'warning',
                        confirmButtonColor: '#FD8418'
                    });
                    return;
                }

                // Submit the form
                this.submit();
            });
        }
    });
    </script>
</body>
</html>
