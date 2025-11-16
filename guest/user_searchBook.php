<?php
include('../config/connect.php');

$search = isset($_GET['searchInput']) ? $_GET['searchInput'] : '';


$sql = "SELECT book_id, title, author, genre, image, description, publication_date, expiry_days, status, book_type
        FROM manage_books
        WHERE title LIKE ? OR author LIKE ? OR genre LIKE ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$searchTerm = "%" . $search . "%";
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
if ($result === false) {
    die("Getting result set failed: " . $stmt->error);
}

// If this is an AJAX request, return results as JSON
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $books = array();
    while ($row = $result->fetch_assoc()) {
        $books[] = array(
            'book_id' => $row['book_id'],
            'title' => $row['title'],
            'author' => $row['author'],
            'cover_image' => '../uploads/' . $row['image'],
            'description' => $row['description'] ? $row['description'] : 'No description available.',
            'publication_date' => $row['publication_date'] ? $row['publication_date'] : 'N/A'
        );
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($books);
    exit;
}

include('navbar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
</head>
<style>
@import url('https://fonts.googleapis.com/css2?family=Faculty+Glyphic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap');

body {
font-family: Titillium Web;

}

/* searchBook Form Style */
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
      <div id="search-book-grid" class="search-book-grid">
          <?php
          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                ?>
                <div class="search-book-card">
                    <div class="search-book-card-container">
                        <div class="search-book-content">
                            <div class="search-book-cover-container">
                                <a href="guest_descriptionForm?book_id=<?php echo urlencode($row['book_id']); ?>">
                                    <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>"
                                         alt="Cover of <?php echo htmlspecialchars($row['title']); ?>"
                                         class="search-book-cover">
                                </a>
                            </div>


                          <div class="search-book-info">
                              <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                              <div class="search-author"><?php echo htmlspecialchars($row['author']); ?></div>
                              <p class="search-description">
                                  <?php
                                  echo isset($row['description']) && !empty($row['description']) ? htmlspecialchars($row['description']) : 'No description available.';
                                  ?>
                              </p>
                              <div class="search-details">
                                  <?php
                                  echo isset($row['book_type']) && !empty($row['book_type'])
                                  ? 'Book Type: ' . htmlspecialchars($row['book_type'])
                                  : 'not available.';
                                  ?>
<br>
                                  <?php

                                  echo isset($row['publication_date']) && !empty($row['publication_date'])
                                      ? 'Published Date: ' . htmlspecialchars($row['publication_date'])
                                      : 'Publication date not available.';
                                  ?>
                                  <br>Book Expiry: <?php echo isset($row['expiry_days']) ? htmlspecialchars($row['expiry_days']) . ' Days' : 'Not available.'; ?>
                                  <br>Genre: <?php echo htmlspecialchars($row['genre']); ?>
                                  <br>Status: <?php echo htmlspecialchars($row['status']); ?>




                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          <?php }
          } else {
              echo '<p>No results found for your search.</p>';
          }
          ?>
      </div>
    </div>
  </div>
  <?php include 'footer.php'; ?>
</body>
</html>
