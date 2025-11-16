<?php
include('../config/connect.php');
include 'navbar.php';

$stmt = $conn->prepare("SELECT has_accepted_policy FROM user_info WHERE member_id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
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
</head>
<style>
.description-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 15px;
    width: 100%;
}

.description-book-header {
    display: flex;
    gap: 24px;
    margin-bottom: 32px;
}

.description-book-cover {
    width: 200px;
    height: 300px;
    margin-top: 100px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.description-book-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.description-book-cover:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
}

.description-book-info {
    flex: 1;
}

.description-book-title {
    font-size: 48px;
    font-weight: 700;
    margin-top: 80px;
    margin-bottom: 20px;
    color: #1a1a1a;
}

.description-book-subtitle {
    font-size: 20px;
    color: #666666;
    margin-bottom: 24px;
}

.description-author, .description-status {
    margin-bottom: 10px;
    color: #666666;
    text-transform: capitalize;
}

.description-publisher {
    margin-bottom: 26px;
    color: #666666;
    text-transform: capitalize;
}

.description-action-buttons {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
}

.description-btn {
    padding: 12px 24px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s ease, transform 0.3s ease;
}

.description-btn-primary {
    background: #FD8418;
    color: white;
}

.description-btn-primary:hover {
    background: #A85810;
    transform: translateY(-2px);
}

.description-btn-secondary {
    background: #e0e0e0;
    color: #333333;
}

.description-btn-secondary:hover {
    background: #d3d3d3;
    transform: translateY(-2px);
}

.description-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 24px;
}

.description-tag {
    padding: 6px 12px;
    background: #f0f0f0;
    border-radius: 4px;
    font-size: 14px;
    color: #333333;
    text-transform: uppercase;
    transition: background 0.3s ease;
}

.description-tag:hover {
    background: #dedede;
}

.description-stats {
    display: flex;
    gap: 24px;
    margin-bottom: 24px;
    color: #666666;
}

.description-stat {
    display: flex;
    align-items: center;
    gap: 8px;
    transition: color 0.3s ease;
}

.description-stat i {
    color: #ff6b6b;
}

.description-stat:hover {
    color: #ff6b6b;
}

.description-description {
    color: #333333;
    line-height: 1.8;
    margin-bottom: 24px;
}

.description-meta-info {
    color: #666666;
    font-size: 14px;
}

.description-book-title {
    transition: color 0.3s ease;
}

.description-book-title:hover {
    color: #FD8418;
}

@media (max-width: 768px) {
    .description-container {
        padding: 10px;
    }

    .description-book-header {
        flex-direction: column;
        gap: 16px;
    }

    .description-book-cover {
        width: 100%;
        max-width: 200px;
        height: 280px;
        margin: 20px auto;
    }
    .description-author, .description-status {
        margin-bottom: 10px;
        text-align: center;
        color: #666666;
        text-transform: capitalize;
    }

    .description-publisher {
        margin-bottom: 26px;
        text-align: center;
        color: #666666;
        text-transform: capitalize;
    }

    .description-book-info {
        padding: 0 10px;
    }

    .description-book-title {
        font-size: 28px !important;
        margin-top: 0 !important;
        text-align: center;
    }

    .description-book-subtitle {
        font-size: 18px;
        text-align: center;
    }

    .description-action-buttons {
        justify-content: center;
    }

    .description-tags {
        justify-content: center;
    }

    .description-description {
        font-size: 14px;
        text-align: justify;
    }
}

@media (max-width: 480px) {
    .description-book-title {
        font-size: 24px !important;
    }

    .description-action-buttons {
        flex-wrap: wrap;
    }

    .description-btn {
        width: 100%;
        margin-bottom: 8px;
    }

    .description-tags {
        flex-wrap: wrap;
        justify-content: center;
    }

    .description-tag {
        font-size: 12px;
    }
}

@media (max-width: 768px) {
    .description-recommended-section {
        padding: 0 15px;
        margin: 40px auto;
    }

    .description-book-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 15px;
    }

    .description-book-card-content {
        padding: 15px;
        height: auto;
    }

    .description-book-card-title {
        font-size: 14px;
        height: auto;
        margin-bottom: 8px;
        -webkit-line-clamp: 2;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .description-book-card-author {
        font-size: 12px;
        margin-bottom: 8px;
    }

    .description-status-badge {
        padding: 4px 8px;
        font-size: 11px;
    }
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
    gap: 32px;
    margin-bottom: 48px;
}

.description-book-card {
    background: white;
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
    font-size: 17px;
    margin-top: -10px;
    font-weight: 700;
    margin-bottom: 15px;
    color: #333;
    height: 52px;
}

.description-book-card-author {
    font-size: 15px;
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

@media (max-width: 768px) {
    .description-book-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 24px;
    }

    .description-recommended-title {
        font-size: 28px;
    }

    .description-see-all {
        font-size: 16px;
    }

    .description-book-card-title {
        font-size: 16px;
        height: 48px;
    }

    .description-book-card-author {
        font-size: 14px;
    }

    .description-status-badge {
        font-size: 12px;
        padding: 6px 12px;
    }
}
.modal {
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
    </style>
</head>
<body>
    <div class="description-container">
        <!-- Book Details Section -->
        <div class="description-book-header">
            <div class="description-book-cover">
                <img src="../uploads/<?= htmlspecialchars($book['image']) ?>" alt="Book Cover">
            </div>
            <div class="description-book-info">
                <h1 class="description-book-title"><?= htmlspecialchars($book['title']) ?></h1>
                <p class="description-author">Author: <?= htmlspecialchars($book['author']) ?></p>
                <p class="description-status">Status: <?= htmlspecialchars($book['status']) ?></p>
                <p class="description-author">Book Type: <?= htmlspecialchars($book['book_type']) ?></p>
                <p class="description-author">Isbn: <?= htmlspecialchars($book['isbn']) ?></p>
                <p class="description-author">Book Expiry: <?= htmlspecialchars($book['expiry_days']) ?> Days</p>
                <p class="description-publisher">Publisher: <?= htmlspecialchars($book['publisher']) ?></p>
                <div class="description-action-buttons">
                    <?php if ($book['status'] === 'available'): ?>
                        <form action="user_borrowHandler.php" method="post" class="borrow-form" data-title="<?= htmlspecialchars($book['title']) ?>">
                            <input type="hidden" name="book_id" value="<?= htmlspecialchars($book_id) ?>">
                            <input type="hidden" name="title" value="<?= htmlspecialchars($book['title']) ?>">
                        </form>
                    <?php else: ?>
                    <?php endif; ?>

                </div>
                <div class="description-tags">
                    <?php
                    $genres = explode(',', $book['genre']);
                    foreach ($genres as $tag): ?>
                        <span class="description-tag"><?= htmlspecialchars(trim($tag)) ?></span>
                    <?php endforeach; ?>
                </div>
                <p class="description-description"><?= nl2br(htmlspecialchars($book['description'])) ?></p>
                <div class="description-meta-info">
                    <p>Publication Date: <?= htmlspecialchars($book['publication_date']) ?></p>
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
                            <a href="guest_descriptionForm.php?book_id=<?= htmlspecialchars($similar_book['book_id']) ?>">
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
                                        </form>
                                    <?php else: ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </body>
    </html>

<?php include 'footer.php'; ?>
