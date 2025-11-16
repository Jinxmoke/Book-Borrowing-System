<?php
include('../config/connect.php');
include 'navbar.php';

function getBooks($conn) {
    $books = [];

    if ($conn === false) {
        error_log("Database connection failed.");
        return $books;
    }

    $sql = "
        SELECT book_id, title, author, genre, publication_date, book_type, status, expiry_days, description, image,
               DATE(date_added) AS date_added, book_type AS type
        FROM manage_books
    ";

    $result = $conn->query($sql);

    if ($result === false) {
        error_log("Query failed: " . $conn->error);
        return $books;
    }

    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    return $books;
}

// Fetch books data
$books = getBooks($conn);

if (empty($books)) {
    echo "No books found.";
    exit;
}

// Convert PHP arrays to JSON for JavaScript
$booksJson = json_encode($books);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Library Catalog</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="user_style.css">
</head>
<body>
    <div class="catalog-main-container">
        <div class="catalog-container">
            <div id="book-grid" class="catalog-book-grid">
                <!-- Book cards will be dynamically inserted here -->
            </div>

            <div class="catalog-pagination-container">
                <div class="catalog-pagination-nav">
                    <button id="prev-page" class="catalog-pagination-arrow" aria-label="Previous page">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span id="page-info">1 of ?</span>
                    <button id="next-page" class="catalog-pagination-arrow" aria-label="Next page">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="catalog-sidebar">

              <!-- Book Type Filter Section -->
            <div class="catalog-filter-section">
                <div class="catalog-filter-header" data-section="book_type">
                    <h3>Book Type</h3>
                    <span class="catalog-arrow"><i class="fa fa-angle-down"></i></span>
                </div>
                <div class="catalog-filter-content" id="book_type">
                    <div class="catalog-filter-option" data-book-type="all">All Books <span class="catalog-count">0</span></div>
                    <div class="catalog-filter-option" data-book-type="physical">Physical Books <span class="catalog-count">0</span></div>
                    <div class="catalog-filter-option" data-book-type="ebook">E-Books <span class="catalog-count">0</span></div>
                </div>
            </div>
              <!-- Availability Filter Section -->
            <div class="catalog-filter-section">
                <div class="catalog-filter-header" data-section="availability">
                    <h3>Availability</h3>
                    <span class="catalog-arrow"><i class="fa fa-angle-down"></i></span>
                </div>
                <div class="catalog-filter-content" id="availability">
                    <div class="catalog-filter-option" data-availability="all">All titles <span class="catalog-count">0</span></div>
                    <div class="catalog-filter-option" data-availability="available">Available now <span class="catalog-count">0</span></div>
                </div>
            </div>
                <!-- Genre Filter Section -->
            <div class="catalog-filter-section">
                <div class="catalog-filter-header" data-section="genre">
                    <h3>Genre</h3>
                    <span class="catalog-arrow"><i class="fa fa-angle-down"></i></span>
                </div>
                <div class="catalog-filter-content" id="genre">
                </div>
            </div>
        </div>
    </div>
    <?php
// Check if there's a message in the query string
if (isset($_GET['message'])) {
    $message = $_GET['message'];
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: '$message',
            showConfirmButton: false,
            timer: 3000
        });
    </script>";
}
?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const books = <?php echo $booksJson; ?>;
        const bookGrid = document.getElementById('book-grid');
        const itemsPerPage = 10;
        let currentPage = 1;
        let filteredBooks = books;
        let currentGenre = null;
        let currentAvailability = 'all';
        let currentBookType = 'all';

        function updateBookTypeCounts() {
            const allBooks = books.length;
            const physicalBooks = books.filter(book => book.type === 'physical').length;
            const eBooks = books.filter(book => book.type === 'ebook').length;

            document.querySelectorAll('#book_type .catalog-filter-option').forEach(option => {
                const countSpan = option.querySelector('.catalog-count');
                const bookType = option.dataset.bookType;
                if (bookType === 'all') {
                    countSpan.textContent = allBooks;
                } else if (bookType === 'physical') {
                    countSpan.textContent = physicalBooks;
                } else if (bookType === 'ebook') {
                    countSpan.textContent = eBooks;
                }

                option.addEventListener('click', function() {
                    currentBookType = bookType;
                    currentGenre = null; // Reset genre when changing book type
                    applyFilters();
                });
            });
        }

        function updateAvailabilityCounts() {
            const allBooks = books.length;
            const availableBooks = books.filter(book =>
                book.status.toLowerCase() === 'available' ||
                book.status.toLowerCase() === 'in stock'
            ).length;

            document.querySelectorAll('#availability .catalog-filter-option').forEach(option => {
                const countSpan = option.querySelector('.catalog-count');
                const availability = option.dataset.availability;
                if (availability === 'all') {
                    countSpan.textContent = allBooks;
                } else if (availability === 'available') {
                    countSpan.textContent = availableBooks;
                }

                option.addEventListener('click', function() {
                    currentAvailability = availability;
                    currentGenre = null; // Reset genre when changing availability
                    applyFilters();
                });
            });
        }

        function updateGenreCounts() {
            const genres = [...new Set(books.map(book => book.genre))];
            const genreContainer = document.getElementById('genre');
            genreContainer.innerHTML = '';

            genres.slice(0, 8).forEach(genre => {
                const count = books.filter(book => book.genre === genre).length;
                const genreElement = document.createElement('div');
                genreElement.className = 'catalog-filter-option';
                genreElement.innerHTML = `
                    <span>${genre}</span>
                    <span class="catalog-count">${count}</span>
                `;
                genreElement.addEventListener('click', function() {
                    currentGenre = genre;
                    applyFilters();
                });
                genreContainer.appendChild(genreElement);
            });

            if (genres.length > 1) {
                const moreButton = document.createElement('a');
                moreButton.href = 'user_genres';
                moreButton.innerHTML = '<button class="catalog-more-button">More</button>';
                genreContainer.appendChild(moreButton);
            }
        }

        function applyFilters() {
            filteredBooks = books.filter(book => {
                const genreMatch = !currentGenre || book.genre === currentGenre;
                const availabilityMatch = currentAvailability === 'all' ||
                    (currentAvailability === 'available' &&
                    (book.status.toLowerCase() === 'available' || book.status.toLowerCase() === 'in stock'));
                const bookTypeMatch = currentBookType === 'all' || book.type === currentBookType;

                return genreMatch && availabilityMatch && bookTypeMatch;
            });

            currentPage = 1;
            displayBooks(currentPage);
            updateFilterUI();
        }

        function updateFilterUI() {
            document.querySelectorAll('.catalog-filter-option').forEach(option => option.classList.remove('active'));

            if (currentGenre) {
                const genreOption = document.querySelector(`#genre .catalog-filter-option:contains('${currentGenre}')`);
                if (genreOption) genreOption.classList.add('active');
            }

            document.querySelector(`#availability [data-availability="${currentAvailability}"]`).classList.add('active');
            document.querySelector(`#book_type [data-book-type="${currentBookType}"]`).classList.add('active');
        }

        function displayBooks(page) {
            const totalPages = Math.ceil(filteredBooks.length / itemsPerPage);
            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredBooks.length);

            bookGrid.innerHTML = '';

            if (filteredBooks.length === 0) {
                bookGrid.innerHTML = '<p>No books available.</p>';
                return;
            }

            const booksToShow = filteredBooks.slice(startIndex, endIndex);
            booksToShow.forEach(book => {
                const bookCard = document.createElement('div');
                bookCard.className = 'catalog-book-card';
                bookCard.innerHTML = `
                    <div class="catalog-book-card-container">
                        <div class="catalog-book-content">
                            <div class="catalog-book-cover-container">
                                <a href="user_descriptionForm?book_id=${encodeURIComponent(book.book_id)}">
                                    <img src="../uploads/${book.image}" alt="Cover of ${book.title}" class="catalog-book-cover">
                                </a>
                            </div>
                            <div class="catalog-book-info">
                                <h2>${book.title}</h2>
                                <div class="catalog-author">${book.author}</div>
                                <p class="catalog-description">${book.description}</p>
                                <div class="catalog-details">
                                    Book Type: ${book.book_type}<br>
                                    Genre: ${book.genre}<br>
                                    Book Expiry: ${book.expiry_days} days<br>
                                    Status: ${book.status}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                bookGrid.appendChild(bookCard);
            });

            updatePagination(totalPages);
        }

        function updatePagination(totalPages) {
            document.getElementById('page-info').textContent = `${currentPage} of ${totalPages}`;
            document.getElementById('prev-page').disabled = currentPage === 1;
            document.getElementById('next-page').disabled = currentPage === totalPages;
        }

        document.getElementById('prev-page').addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                displayBooks(currentPage);
            }
        });

        document.getElementById('next-page').addEventListener('click', function() {
            const totalPages = Math.ceil(filteredBooks.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                displayBooks(currentPage);
            }
        });

        document.querySelectorAll('.catalog-filter-header').forEach(header => {
            header.addEventListener('click', function() {
                const sectionId = this.dataset.section;
                const content = document.getElementById(sectionId);
                const arrow = this.querySelector('.catalog-arrow');

                content.classList.toggle('active');
                arrow.classList.toggle('active');
            });
        });

        updateBookTypeCounts();
        updateGenreCounts();
        updateAvailabilityCounts();
        displayBooks(currentPage);
    });
    </script>
</body>
<?php
include 'footer';

 ?>
</html>
