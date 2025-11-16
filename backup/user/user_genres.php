<?php

include('../config/connect.php');
include 'navbar.php';

try {
    // Query to fetch the genre and their count from manage_books
    $sql = "SELECT genre, COUNT(*) as count
            FROM manage_books
            GROUP BY genre
            ORDER BY count DESC, genre ASC";

    // Execute the query
    $result = $conn->query($sql);
    $db_genres = $result->fetch_all(MYSQLI_ASSOC);

    // Query to get the total number of books
    $total_books_sql = "SELECT COUNT(*) as total FROM manage_books";

    // Execute the query to fetch the total count
    $total_books_result = $conn->query($total_books_sql);
    $total_books = $total_books_result->fetch_assoc()['total'];

} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

$genre_categories = [
    'Fiction' => [
        "Romance novel", "Fantasy", "Horror", "Thriller",
        "Historical Fiction", "Magic realism", "Mystery", "Paranormal romance",
        "Crime fiction", "Dystopian Fiction", "Young adult", "Adventure fiction",
        "Contemporary fantasy", "Fantasy Fiction", "Historical fiction", "Literary fiction",
        "Speculative fiction", "Western", "Romantic Suspense", "Comedy",
        "Conspiracy", "Dark fantasy", "Erotic thriller", "Science Fiction"
    ],
    'Non-Fiction' => [
        "Mathematics", "Literature", "Business", "Chemistry", "Economics", "History",
        "Philosophy", "Statistics", "Memoir", "Travel writing", "Narrative nonfiction",
        "Science", "Journalism", "Self-help book", "Biography", "Essay", "Political science",
        "Religious books", "Self-help and instruction", "Textbook", "Academic texts",
        "Business economics", "Health and Wellness", "Humor", "Instruction manuals",
        "True crime", "Crafting"
    ],
    'Other Genres' => []
];

// Initialize an array to store all genres and their count
$all_genres = [];
foreach ($db_genres as $db_genre) {
    $genre = $db_genre['genre'];
    $count = $db_genre['count'];
    $all_genres[$genre] = ['count' => $count];
}

// Initialize an array to categorize genres into Fiction, Non-Fiction, and Other Genres
$categorized_genres = [
    'Fiction' => [],
    'Non-Fiction' => [],
    'Other Genres' => []
];

foreach ($all_genres as $genre => $data) {
    if (in_array($genre, $genre_categories['Fiction'])) {
        $categorized_genres['Fiction'][$genre] = $data;
    } elseif (in_array($genre, $genre_categories['Non-Fiction'])) {
        $categorized_genres['Non-Fiction'][$genre] = $data;
    } else {
        $categorized_genres['Other Genres'][$genre] = $data;
    }
}

// Update the 'Other Genres' category with genre names that don't belong to Fiction or Non-Fiction
$genre_categories['Other Genres'] = array_keys($categorized_genres['Other Genres']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Subjects</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            margin-top: 50px;
            background: white;
            padding: 20px;
            border-radius: 4px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .format-tab {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .total-books {
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }

        .genre-category {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .category-header {
            padding: 15px;
            background-color: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .category-header:hover {
            background-color: #e9ecef;
        }

        .category-title {
            margin: 0;
            font-size: 1.2em;
            color: #444;
        }

        .toggle-icon {
            transition: transform 0.3s ease;
        }

        .genres-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            padding: 15px;
            display: none;
        }

        .genre-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-radius: 4px;
            background-color: #fff;
            transition: background-color 0.2s;
        }

        .genre-item:hover {
            background-color: #e9ecef;
        }

        .genre-name {
            color: #333;
            text-decoration: none;
        }

        .genre-count {
            color: #666;
        }

        .active .toggle-icon {
            transform: rotate(180deg);
        }

        .active + .genres-container {
            display: grid;
        }

        .btn {
            text-decoration: none;
            color: black;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .btn:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Browse Categories</h1>

        <div class="format-tab">
            <strong>BOOKS</strong>
        </div>

        <!-- Display total number of books -->
        <div class="total-books">
            <a class="btn" href="user_catalogForm.php">All book titles: <?php echo $total_books; ?></a>
        </div>

        <!-- Loop through each genre category -->
        <?php foreach ($genre_categories as $category => $genres): ?>
            <div class="genre-category">
                <div class="category-header">
                    <h3 class="category-title"><?php echo htmlspecialchars($category); ?></h3>
                    <span class="toggle-icon"><i class="fa fa-angle-down"></i></span>
                </div>
                <div class="genres-container">
                    <?php foreach ($genres as $genre): ?>
                        <div class="genre-item">
                            <a href="user_booksGenre.php?genre=<?php echo urlencode($genre); ?>" class="genre-name">
                                <?php echo htmlspecialchars($genre); ?>
                            </a>
                            <span class="genre-count">
                                <?php echo isset($all_genres[$genre]) ? $all_genres[$genre]['count'] : 0; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        document.querySelectorAll('.category-header').forEach(header => {
            header.addEventListener('click', () => {
                header.classList.toggle('active');
            });
        });
    </script>
</body>
</html>
