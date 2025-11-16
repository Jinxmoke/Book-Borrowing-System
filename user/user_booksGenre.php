<?php
ob_start(); // Start output buffering to prevent premature output

include('../config/connect.php');
include 'navbar.php';

// Get the genre from the URL
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';

// Redirect if no genre is specified
if (empty($genre)) {
    header("Location: user_catalogForm");
    exit;
}

// Fetch books by genre only from manage_books
$sql = "SELECT book_id, title, author, genre, image, description, publication_date, book_type, expiry_days, status
        FROM manage_books
        WHERE genre = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $genre);
$stmt->execute();
$result = $stmt->get_result();
$books = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($genre); ?> eBooks</title>
    <link rel="stylesheet" href="user_style.css">
</head>
<body>
    <div class="search-main-container">
        <div class="searchBook-container">
            <h1 class="text-3xl font-bold mb-6 text-center"><?php echo htmlspecialchars($genre); ?></h1>

            <?php if (empty($books)): ?>
                <p class="text-center text-gray-600">No books available in this genre.</p>
            <?php else: ?>
                <div id="search-book-grid" class="search-book-grid">
                    <?php foreach ($books as $book): ?>
                        <div class="search-book-card">
                            <div class="search-book-card-container">
                                <div class="search-book-content">
                                    <div class="search-book-cover-container">
                                        <a href="user_descriptionForm?book_id=<?php echo urlencode($book['book_id']); ?>">
                                            <img src="../uploads/<?php echo htmlspecialchars($book['image']); ?>"
                                                 alt="Cover of <?php echo htmlspecialchars($book['title']); ?>"
                                                 class="search-book-cover">
                                        </a>
                                    </div>
                                    <div class="search-book-info">
                                        <h2><?php echo htmlspecialchars($book['title']); ?></h2>
                                        <div class="search-author"><?php echo htmlspecialchars($book['author']); ?></div>
                                        <p class="search-description">
                                            <?php
                                            echo isset($book['description']) && !empty($book['description'])
                                                ? htmlspecialchars($book['description'])
                                                : 'No description available.';
                                            ?>
                                        </p>

                                        <div class="search-details">
                                            <?php
                                            echo isset($book['publication_date']) && !empty($book['publication_date'])
                                                ? 'Published: ' . htmlspecialchars($book['publication_date'])
                                                : 'Publication date not available.';
                                            ?><br>Genre: <?php echo htmlspecialchars($book['genre']); ?><br> Book Expiry: <?php echo htmlspecialchars($book['expiry_days']); ?> Days
                                            <br> Book Type: <?php echo htmlspecialchars($book['book_type']); ?><br> Status: <?php echo htmlspecialchars($book['status']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
ob_end_flush(); // Flush the output buffer at the end of the script
?>
