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
</head>
<style media="screen">
.catalog-main-container {
    display: flex;
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 2rem;
    gap: 2rem;
}

.catalog-container {
    flex: 1;
    background-color: #fff;
    border-radius: 1rem;
    padding: 2rem;
    margin-bottom: 100px;
}

.catalog-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1rem 0;
    border-bottom: 2px solid #F8FAFC;
}

.catalog-filters select {
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    background-color: #fff;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.catalog-filters select:hover {
    border-color: #FF6B00;
}

.catalog-book-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 2rem;
}

.catalog-book-card {
    background: white;
    border-radius: 1rem;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.catalog-book-card:hover {
    transform: translateY(-4px);
}

.catalog-book-card-container {
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.catalog-book-content {
    flex: 1;
    display: flex;
    padding: 15px;
    min-height: 250px;
}

.catalog-book-cover-container {
    width: 120px;
    height: 180px;
    flex-shrink: 0;
    margin-right: 15px;
}

.catalog-book-cover {
    width: 125%;
    height: 125%;
    object-fit: cover;
    border-radius: 0.5rem;
    box-shadow: 0 6px 6px 0 rgba(119,119,119,.75);
    transition: transform 0.3s ease;
}

.catalog-book-cover:hover {
    transform: scale(1.05);
}

.catalog-book-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.catalog-book-info h2 {
    font-size: 1.1rem;
    margin-bottom: 5px;
    margin-left: 15px;
}

.catalog-book-info{
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 10px;
    margin-left: 15px;
}

 .catalog-author {
     color: #666;
     font-size: 0.9rem;
     margin-bottom: 10px;
     margin-left: 15px;
 }

.catalog-book-info {
    font-size: 0.9rem;
    margin-bottom: 10px;
    margin-left: 15px;

}

.catalog-description {
  font-size: 0.9rem;
  margin-bottom: 10px;
  margin-left: 15px;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;

}

.catalog-book-info  {
    font-size: 0.8rem;
    color: #666;
    margin-left: 15px;
}

.catalog-details
{
    font-size: 0.8rem;
    color: #666;
    margin-left: 15px;
}

.catalog-sidebar {
    width: 320px;
    display: flex;
    font-size: 1rem;
    margin-top: 34px;
    flex-direction: column;
    gap: 1rem;
}

.catalog-genre-section {
    background: white;
    margin-top: 40px;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    margin-top: 100px;
}

.catalog-genre-section h2 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #0f172a;
}

.catalog-genre-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 0.75rem;
}

.catalog-genre-tag {
    background: #f8fafc;
    color: #475569;
    padding: 0.75rem 0.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-size: 0.7rem;
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
}

.catalog-genre-tag:hover, .catalog-genre-tag.active {
    background: #FF6B00;
    color: white;
    transform: translateY(-2px);
}

.catalog-subscription-card {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

.catalog-subscription-card h2 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #0f172a;
}

.catalog-price {
    font-size: 3rem;
    font-weight: 700;
    color: #FF6B00;
    margin: 1.5rem 0;
}

.catalog-subscribe-btn {
    background: #FF6B00;
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 0.5rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
}

.catalog-subscribe-btn:hover {
    background: #4CAF50;
    transform: translateY(-2px);
}

.catalog-pagination-container {
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.catalog-pagination-nav {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
}

.catalog-pagination-arrow {
    background: none;
    border: 1px solid #e2e8f0;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.catalog-pagination-arrow:hover {
    border-color: #FF6B00;
    color: #FF6B00;
}

.catalog-pagination-arrow:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.catalog-all-genres {
    font-size: 12px;
    margin-left: 225px;
    color: #FF6B00;
}

@keyframes catalogFadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.catalog-book-card {
    animation: catalogFadeIn 0.5s ease forwards;
}

.catalog-filter-section {
    border-bottom: 1px solid #e5e5e5;
    padding: 12px 0;
}

.catalog-filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    font-size: 20px;
    user-select: none;
}

.catalog-filter-header h3 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 600;
}

.catalog-filter-content {
    display: none;
    margin-top: 8px;
}

.catalog-filter-content.active {
    display: block;
}

.catalog-filter-option {
    display: flex;
    justify-content: space-between;
    padding: 4px 0;
    color: #444;
    text-decoration: none;
    cursor: pointer;
}

.catalog-filter-option:hover {
    color: #000;
}

.catalog-count {
    color: #666;
}

.catalog-arrow {
    transition: transform 0.2s;
}

.catalog-arrow.active {
    transform: rotate(180deg);
}

.catalog-more-button {
    display: block;
    width: 100%;
    padding: 8px;
    margin-top: 12px;
    border: 1px solid #ccc;
    background: white;
    border-radius: 4px;
    cursor: pointer;
}

.catalog-more-button:hover {
    background: #f5f5f5;
}

.catalog-filter-content {
    font-size: 15px;
}

@media (max-width: 1280px) {
    .catalog-main-container {
        flex-direction: column;
        padding: 0 1rem;
    }


    .catalog-sidebar {
        width: 100%;
        margin-top: 2rem;
    }

    .catalog-genre-section {
        margin-top: 2rem;
    }





}

@media (max-width: 768px) {
    .catalog-book-grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }

    .catalog-filters {
        flex-direction: column;
        align-items: flex-start;
    }

    .catalog-filters select {
        width: 100%;
        margin-bottom: 1rem;
    }

    .catalog-book-content {
        flex-direction: column;
        align-items: center;
    }

    .catalog-book-cover-container {
        margin-right: 0;
        margin-bottom: 15px;
    }

    .catalog-book-info {
        text-align: center;
    }

    .catalog-all-genres {
        margin-left: 0;
        text-align: center;
        display: block;
        margin-top: 1rem;
    }
}

@media (max-width: 480px) {
    .catalog-book-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .catalog-container {
        padding: 0.3rem;
    }

    .catalog-book-card {
        border-radius: 12px;
    }

    .catalog-book-content {
        display: flex;
        flex-direction: row;
        padding: 0;
        min-height: 160px;
        align-items: flex-start;
    }

    .catalog-book-cover-container {
        width: 120px;
        height: 180px;
        margin: 0;
        flex-shrink: 0;
    }

    .catalog-book-cover {
        width: 105%;
        height: 105%;
        border-radius: 12px;
        box-shadow: 0 6px 6px 0 rgba(119,119,119,.75);
    }

    .catalog-book-info {
        flex: 1;
        padding: 0.1rem;
        text-align: left;

    }

    .catalog-book-info h2 {
        font-size: 15px;
        font-weight: 600;
        margin: 0 -10px 0 0;
    }

    .catalog-author {
        color: #666;
        font-size: 10px;
        margin: 0 -10px 0 0;
    }

    .catalog-description {
        font-size: 10px;
        margin: 0 0 0.5rem 0;
        -webkit-line-clamp: 5;
        color: black;
    }

    .catalog-details {
        font-size: 10px;
        color: #666;
        margin: 0;
    }

    .catalog-book-info h2,
    .catalog-book-info .catalog-author,
    .catalog-book-info .catalog-description,
    .catalog-book-info .catalog-details {
        margin-left: 0;
    }
}
</style>
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
                                <a href="guest_descriptionForm?book_id=${encodeURIComponent(book.book_id)}">
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
include 'footer.php';

 ?>
</html>
