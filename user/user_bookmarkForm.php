<?php
ob_start();

include('../config/connect.php');
include 'navbar.php';

// Handle removing a bookmark
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_bookmark'])) {
    $book_id_to_remove = $_POST['book_id'];

    $remove_sql = "DELETE FROM bookmarks WHERE member_id = ? AND book_id = ?";
    $remove_stmt = $conn->prepare($remove_sql);
    $remove_stmt->bind_param("ii", $member_id, $book_id_to_remove);
    $remove_stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Retrieve bookmarks from manage_books table
$sql = "
    SELECT b.book_id, b.title, b.author, b.image, b.status, b.description
    FROM bookmarks bm
    JOIN manage_books b ON bm.book_id = b.book_id
    WHERE bm.member_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo "Error: " . $conn->error;
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookmarked Books</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #1A8B3E;
            --primary-hover: #115C29;
            --background: #f0f0f0;
            --card-background: #ffffff;
            --text: #333333;
            --text-muted: #666666;
            --border: #dddddd;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .bookmarks-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        #bookmarks-count {
            color: var(--text-muted);
        }

        #status-filter {
            padding: 0.5rem;
            border: 1px solid var(--border);
            border-radius: 4px;
            background-color: var(--card-background);
        }

        #bookmarks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background-color: var(--card-background);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            cursor: pointer;
        }

        .card-content {
            padding: 1rem;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .card-author {
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
        }

        .status-badge.available {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-badge.borrowed {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .remove-button {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
        }

        .remove-button:hover {
            color: var(--text);
        }

        #empty-state {
            text-align: center;
            padding: 3rem 0;
        }

        #empty-state i {
            font-size: 3rem;
            color: var(--text-muted);
        }

        #empty-state h2 {
            margin-top: 1rem;
            font-size: 1.5rem;
        }

        #empty-state p {
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .button {
            display: inline-flex;
            align-items: center;
            background-color: var(--primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: var(--primary-hover);
        }

        .button i {
            margin-right: 0.5rem;
        }

        .hidden {
            display: none;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            #bookmarks-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Bookmarked Books</h1>
        <div class="bookmarks-header">
            <p id="bookmarks-count"></p>
            <select id="status-filter">
                <option value="all">All Statuses</option>
                <option value="available">Available</option>
                <option value="borrowed">Borrowed</option>
            </select>
        </div>
        <div id="bookmarks-grid"></div>
        <div id="empty-state" class="hidden">
            <i data-lucide="bookmark"></i>
            <h2>No bookmarks found</h2>
            <p>Start exploring and bookmarking books you're interested in.</p>
            <a href="user_catalogForm" class="button">
                 Browse Books
            </a>
        </div>
    </div>
    <script>
        // Bookmarks data
        const bookmarks = <?php echo json_encode($result->fetch_all(MYSQLI_ASSOC)); ?>;

        document.addEventListener('DOMContentLoaded', () => {
            const bookmarksGrid = document.getElementById('bookmarks-grid');
            const bookmarksCount = document.getElementById('bookmarks-count');
            const statusFilter = document.getElementById('status-filter');
            const emptyState = document.getElementById('empty-state');

            function renderBookmarks(filteredBookmarks) {
                bookmarksGrid.innerHTML = '';
                bookmarksCount.textContent = `${filteredBookmarks.length} book(s) found`;

                if (filteredBookmarks.length === 0) {
                    emptyState.classList.remove('hidden');
                    bookmarksGrid.classList.add('hidden');
                } else {
                    emptyState.classList.add('hidden');
                    bookmarksGrid.classList.remove('hidden');

                    filteredBookmarks.forEach(book => {
                        const card = document.createElement('div');
                        card.className = 'card';
                        card.innerHTML = `
                            <div class="card-image" style="background-image: url('../uploads/${book.image}')" onclick="window.location.href='user_descriptionForm?book_id=${book.book_id}'"></div>
                            <div class="card-content">
                                <h2 class="card-title">${book.title}</h2>
                                <p class="card-author">by ${book.author}</p>
                                <div class="card-footer">
                                    <span class="status-badge ${book.status.toLowerCase()}">${book.status}</span>
                                    <form method="POST" action="">
                                        <input type="hidden" name="book_id" value="${book.book_id}">
                                        <button type="submit" name="remove_bookmark" class="remove-button">
                                            <i data-lucide="x"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        `;
                        bookmarksGrid.appendChild(card);
                    });
                }

                lucide.createIcons();
            }

            function filterBookmarks() {
                const selectedStatus = statusFilter.value;
                const filteredBookmarks = selectedStatus === 'all'
                    ? bookmarks
                    : bookmarks.filter(book => book.status.toLowerCase() === selectedStatus);
                renderBookmarks(filteredBookmarks);
            }

            statusFilter.addEventListener('change', filterBookmarks);

            filterBookmarks();
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
ob_end_flush();
?>
