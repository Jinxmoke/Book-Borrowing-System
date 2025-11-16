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
    <link rel="stylesheet" href="user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    body {
        line-height: 1.6;
        color:  #333333;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 1200px;
        margin: auto;
        margin-top: 70px;
        padding: 20px;
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
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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
        color: #FD8418;
    }

    .book-author {
        font-size: 1.2em;
        color:  #333333;
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
        background-color: #A85810;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background-color: #f0f0f0;
        color:  #333333;
    }

    .btn-secondary:hover {
        background-color: #e0e0e0;
        transform: translateY(-2px);
    }

.description-recommended-section {
    max-width: 1500px;
    margin: 80px auto;
    padding: 0 24px;
}

.description-recommended-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    width: 1155px;
    margin-left: -30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e0e0e0;
}

.description-recommended-title {
    font-size: 20px;
    color: #333;
    letter-spacing: -0.5px;
}

.description-see-all {
    color: #FD8418;
    text-decoration: none;
    font-weight: 600;
    font-size: 18px;
    transition: all 0.3s ease;
}

.description-see-all:hover {
    color: #A85810;
    text-decoration: underline;
}

.description-book-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 77px;
    margin-bottom: 48px;
}

.description-book-card {
    background: white;
    width: 265px;
    margin-left: -30px;
    max-width: 1210px;
    border-radius: 5px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.description-book-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
}

.description-book-card-image-container {
    position: relative;
    padding-top: 100%;
    overflow: hidden;
}

.description-book-card-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.description-book-card:hover .description-book-card-image {
    transform: scale(1.1);
}

.description-book-card-content {
    padding: 20px;
    height: 170px;
}

.description-book-card-title {
    font-size: 15px;
    margin-top: -10px;
    font-weight: 700;
    margin-bottom: 5px;
    color: #333;
    height: 52px;
}

.description-book-card-author {
    font-size: 13px;
    color: #666;
    margin-top: -20px;
    margin-bottom: 16px;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.description-book-card-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.description-status-badge {
    background: #1A8B3E;
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: background 0.3s ease;
}

.description-status-badge.borrowed {
    background: #e74c3c;
}

.description-borrow-btn {
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    padding: 10px;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.description-borrow-btn:hover {
    background: #f0f0f0;
    color: #ff6b6b;
}

.modal1 {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(5px);
}

.modal-content {
    background-color: #ffffff;
    margin: 10% auto;
    padding: 30px;
    border: none;
    width: 94%;
    height: 555px;
    max-width: 655px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}
.modal-buttons {
  margin-top: 30px;
  text-align: right;
}

.modal-btn {
  padding: 12px 24px;
  margin: 0 8px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.confirm-btn {
  background-color: #4CAF50;
  color: white;
}

.confirm-btn:hover {
  background-color: #45a049;
}

.modal-btn:not(.confirm-btn) {
  background-color: #f0f0f0;
  color: #333;
}

.modal-btn:not(.confirm-btn):hover {
  background-color: #e0e0e0;
}

#policyText {
    max-height: 365px;
    margin-bottom: 25px;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 8px;
    line-height: 1.6;
}

#policyText ul {
  padding-left: 25px;
  margin: 10px 0;
}

#policyText li {
  margin-bottom: 8px;
}

.book-review-section {
    width: 100%;
    max-width: 1200px;
    margin: auto;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
}
.book-review-header {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 20px;
    margin-left: -20px;
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
@media screen and (max-width: 768px) {
    .container {
        padding: 10px;
        margin-top: 20px;
    }

    .book-header {
        flex-direction: column;
        gap: 20px;
        align-items: center;
    }

    .book-cover {
        width: 250px;
        height: 375px;
    }

    .book-info {
        width: 100%;
        text-align: center;
    }

    .book-title {
        font-size: 2em;
    }

    .book-author {
        font-size: 1em;
    }

    .book-meta {
        grid-template-columns: 1fr;
        gap: 10px;
        justify-items: center;
    }

    .book-meta-item {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .book-actions {
        flex-direction: column;
        gap: 10px;
        align-items: center;
    }

    .btn {
        width: 100%;
        max-width: 300px;
    }

    .description-recommended-section {
        margin: 40px auto;
        padding: 0 15px;
        width: 100%;
    }

    .description-recommended-header {
        width: 100%;
        margin-left: 0;
        flex-direction: column;
        align-items: flex-start;
    }

    .description-book-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 20px;
        margin-left: 0;
    }

    .description-book-card {
        width: 100%;
        margin-left: 0;
    }

    .modal1 {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(5px);
    }

    .modal-content {
        width: 90%;
        max-width: 90%;
        margin: 10% auto;
        padding: 20px;
        border-radius: 10px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .modal-content h2 {
        font-size: 1.4em;
        margin-bottom: 15px;
    }

    #policyText {
        max-height: 50vh;
        overflow-y: auto;
        font-size: 0.9em;
        line-height: 1.5;
        padding: 10px;
    }

    #policyText ul {
        padding-left: 20px;
    }

    .modal-buttons {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 15px;
    }

    .modal-btn {
        width: 100%;
        padding: 12px;
        font-size: 0.9em;
    }
}

@media screen and (max-width: 480px) {
    .book-cover {
        width: 200px;
        height: 300px;
    }

    .book-title {
        font-size: 1.5em;
    }

    .description-book-grid {
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .description-book-card-content {
        height: 140px;
    }

    .modal-content {
        margin: 15% auto;
        width: 95%;
        padding: 15px;
    }

    #policyText {
        max-height: 45vh;
        font-size: 0.8em;
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
                          <i class="fas fa-book-open"></i> N/A
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
              <button id="bookToggleReviews" class="book-review-toggle">
                  <span class="book-toggle-text">Show Reviews</span>
                  <i class="fas fa-chevron-down"></i>
              </button>
          </div>
          <div id="bookReviewContent" class="book-review-content">
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

        <!-- Recommended Books Section -->
        <div class="description-recommended-section">
            <div class="description-recommended-header">
                <h2 class="description-recommended-title">You may also like</h2>
                <a href="user_booksGenre.php?genre=<?= urlencode($genre) ?>" class="description-see-all">See all</a>
            </div>
            <div class="description-book-grid">
                <?php if (empty($similar_books_array)): ?>
                    <p>No similar books found.</p>
                <?php else: ?>
                    <?php foreach ($similar_books_array as $similar_book): ?>
                        <div class="description-book-card">
                            <a href="user_descriptionForm.php?book_id=<?= htmlspecialchars($similar_book['book_id']) ?>">
                                <div class="description-book-card-image-container">
                                    <img src="../uploads/<?= htmlspecialchars($similar_book['image']) ?>"
                                         alt="<?= htmlspecialchars($similar_book['title']) ?>"
                                         class="description-book-card-image">
                                </div>
                            </a>
                            <div class="description-book-card-content">
                                <h3 class="description-book-card-title"><?= htmlspecialchars($similar_book['title']) ?></h3>
                                <p class="description-book-card-author">Author: <?= htmlspecialchars($similar_book['author']) ?></p>
                                <p class="description-book-card-author">Genre: <?= htmlspecialchars($similar_book['genre']) ?></p>
                                <div class="description-book-card-actions">
                                    <span class="description-status-badge <?= $similar_book['status'] === 'borrowed' ? 'borrowed' : '' ?>">
                                        <?= htmlspecialchars($similar_book['status']) ?>
                                    </span>
                                    <?php if ($similar_book['status'] === 'available'): ?>
                                        <form action="user_borrowHandler.php" method="post" class="borrow-form" data-title="<?= htmlspecialchars($similar_book['title']) ?>">
                                            <input type="hidden" name="book_id" value="<?= htmlspecialchars($similar_book['book_id']) ?>">
                                            <input type="hidden" name="title" value="<?= htmlspecialchars($similar_book['title']) ?>">
                                            <button type="button" class="description-borrow-btn borrow-button" title="Borrow this book">
                                                <i class="fas fa-book-open"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="description-borrow-btn" disabled title="Book is currently borrowed">
                                            <i class="fas fa-book-open"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Library Policy Modal -->
    <div id="policyModal" class="modal1">
        <div class="modal-content">
            <h2>Library Policy</h2>
            <div id="policyText">
                <p>Welcome to our library! Before you request for a book for the first time, please read our policy:</p>
                <ul>
                    <li>Books can be borrowed for up to 14 days.</li>
                    <li>You can borrow a maximum of 3 books at a time.</li>
                    <li>Late returns of physical books will result in a temporary suspension of borrowing privileges.</li>
                    <li>If a physical book is damaged or lost, you will be temporarily banned from borrowing books until the issue is resolved.</li>
                    <li>Please handle all books with care and return them in the condition you received them.</li>
                </ul>
            </div>
            <div class="modal-buttons">
                <button id="acceptPolicy" class="modal-btn confirm-btn">I Accept</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const borrowButtons = document.querySelectorAll('.borrow-button');
            const policyModal = document.getElementById('policyModal');
            const acceptPolicyBtn = document.getElementById('acceptPolicy');
            let currentForm;
            let hasAcceptedPolicy = <?php echo json_encode($hasAcceptedPolicy); ?>;

            borrowButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentForm = this.closest('.borrow-form');

                    if (!hasAcceptedPolicy) {
                        policyModal.style.display = 'block';
                    } else {
                        submitBorrowForm();
                    }
                });
            });

            acceptPolicyBtn.addEventListener('click', function() {
                fetch('accept_policy.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ accept: true }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        hasAcceptedPolicy = true;
                        policyModal.style.display = 'none';
                        submitBorrowForm();
                    } else {
                        alert('There was an error accepting the policy. Please try again.');
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('There was an error accepting the policy. Please try again.');
                });
            });

            window.addEventListener('click', function(event) {
                if (event.target == policyModal) {
                    policyModal.style.display = 'none';
                }
            });

            function submitBorrowForm() {
                if (currentForm) {
                    const bookTitle = currentForm.dataset.title;
                    if (confirm(`Do you want to Request "${bookTitle}"?`)) {
                        currentForm.submit();
                    }
                }
            }
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('bookToggleReviews');
    const reviewContent = document.getElementById('bookReviewContent');
    const toggleText = toggleButton.querySelector('.book-toggle-text');
    const toggleIcon = toggleButton.querySelector('i');

    let isReviewVisible = false;

    toggleButton.addEventListener('click', function() {
      isReviewVisible = !isReviewVisible;
      reviewContent.style.display = isReviewVisible ? 'block' : 'none';
      toggleText.textContent = isReviewVisible ? 'Hide Reviews' : 'Show Reviews';
      toggleIcon.style.transform = isReviewVisible ? 'rotate(180deg)' : 'rotate(0deg)';
  });
});
    </script>
</body>
</html>
