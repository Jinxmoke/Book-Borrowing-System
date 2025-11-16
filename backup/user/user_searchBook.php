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
    <link rel="stylesheet" href="user_style.css">
    <title>Search Results</title>
</head>
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
                              <a href="user_descriptionForm.php?book_id=<?php echo urlencode($row['book_id']); ?>">
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
</body>
</html>
