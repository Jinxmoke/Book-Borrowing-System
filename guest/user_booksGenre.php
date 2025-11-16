
<?php
session_start();
ob_start(); // Start output buffering to prevent premature output

include('../config/connect.php');
include 'navbar.php';

// Get the genre from the URL
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';

// Redirect if no genre is specified
if (empty($genre)) {
    header("Location: user_catalogForm.php");
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
<style media="screen">
.search-main-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

.searchBook-container {
    flex: 1;
    background-color: #fff;
    border-radius: 1rem;
    padding: 2rem;
    margin-bottom: 500px;
    width: 80%;
    max-width: 1200px;
}

.search-book-card {
    background: white;
    border-radius: 1rem;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.search-book-card:hover {
    transform: translateY(-4px);
}

.search-book-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 2rem;
}

.search-book-card-container {
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.search-book-content {
    flex: 1;
    display: flex;
    padding: 10px;
    min-height: 250px;
}

.search-book-cover-container {
    width: 120px;
    height: 180px;
    flex-shrink: 0;
    margin-right: 15px;
}

.search-book-cover {
    width: 140%;
    height: 140%;
    object-fit: cover;
    border-radius: 0.5rem;
    box-shadow: 0 6px 6px 0 rgba(119,119,119,.75);
    transition: transform 0.3s ease;
}

.search-book-cover:hover {
    transform: scale(1.05);
}

.search-book-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: space-between;
    flex: 1; /* Allow the content to stretch and fill remaining space */
    padding-left: 50px;
}

.search-book-info h2 {
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
    flex-grow: 1;
}

.search-book-info .search-author {
    color: #666;
    font-size: 0.9rem;
    text-transform: capitalize;
    margin-bottom: 10px;
}

.search-book-info .search-description {
    font-size: 0.9rem;
    margin-bottom: 10px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    color: #444;
}

.search-book-info .search-details {
    font-size: 0.8rem;
    color: #666;
    text-transform: capitalize;
    align-self: flex-start;
    margin-top: 10px;
}
@media (max-width: 768px) {
  .search-book-grid {
      display: grid;
      gap: 0rem;
      grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  }
  .search-book-info {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      justify-content: space-between;
      flex: 1;
      padding-left: 20px;
  }
  .search-book-cover {
      width: 130%;
      height: 135%;
      margin-left: -10px;
      object-fit: cover;
      margin-top: 5px;
      border-radius: 0.5rem;
      box-shadow: 0 6px 6px 0 rgba(119, 119, 119, .75);
      transition: transform 0.3s ease;
  }
  .searchBook-container {
    flex: 1;
    background-color: #fff;
    border-radius: 1rem;
    width: 100%;
    margin-left: -20px;
    margin-top: -20px;
    max-width: 1200px;
}
.search-book-info .search-description {
    font-size: 0.9rem;
    margin-bottom: 10px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    width: 210px;
    color: #444;
}
.search-main-container {
  width: 100%;
  overflow-x: hidden;
  padding: 0;
}
}
</style>
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
                                        <a href="guest_descriptionForm?book_id=<?php echo urlencode($book['book_id']); ?>">
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
<?php include 'footer.php'; ?>
