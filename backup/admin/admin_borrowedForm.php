<?php
include('../config/connect.php');
include('navbar.php');

// Initialize pagination variables
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;


$search = isset($_GET['search']) ? $_GET['search'] : '';

// Initialize filter variable
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Fetch borrowed books with search, filter, and pagination
$sql = "SELECT b.*, u.email, u.member_id
        FROM borrowed_books b
        JOIN user_info u ON b.member_id = u.member_id
        WHERE b.title LIKE ? AND b.book_type = 'physical'
        " . ($filter === 'overdue' ? "AND b.due_date < CURDATE()" :
            ($filter === 'due' ? "AND b.due_date >= CURDATE()" : "")) . "
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%$search%";
$stmt->bind_param("sii", $searchTerm, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Get total number of records for pagination
$sqlTotal = "SELECT COUNT(*) AS total
             FROM borrowed_books
             WHERE title LIKE ? AND book_type = 'physical'
             " . ($filter === 'overdue' ? "AND due_date < CURDATE()" :
                 ($filter === 'due' ? "AND due_date >= CURDATE()" : ""));
$stmtTotal = $conn->prepare($sqlTotal);
$stmtTotal->bind_param("s", $searchTerm);
$stmtTotal->execute();
$totalResult = $stmtTotal->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalRows = $totalRow['total'];
$totalPages = ceil($totalRows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library System - Borrowed Books</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
         * {
             margin: 0;
             padding: 0;
             box-sizing: border-box;
         }

         body {
             font-family: 'Inter', sans-serif;
             background-color: #f1f5f9;
             color: #1e293b;
             line-height: 1.6;
         }

         .container {
             max-width: 1350px;
             margin: 0 auto;
             padding: 2rem;
         }

         .header {
             background-color: #ffffff;
             padding: 1.5rem;
             border-radius: 8px;
             box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
             margin-bottom: 2rem;
         }

         .header h1 {
             font-size: 1.5rem;
             color: #FD8418;
             margin-bottom: 0.5rem;
         }

         .search-and-filter {
             display: flex;
             flex-wrap: wrap;
             gap: 1rem;
             margin-bottom: 2rem;
         }

         .search-bar {
             flex: 1 ;
             display: flex;
         }

         .search-input {
             flex: 1;
             padding: 0.75rem 1rem;
             border: 1px solid  #e2e8f0;
             font-size: 1rem;
             transition: border-color 0.3s, box-shadow 0.3s;
         }

         .search-input:focus {
             outline: none;
             border-color: #FD8418;
             box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
         }

         .search-button {
             padding: 0.75rem 1.5rem;
             background-color: #FD8418;
             color: white;
             border: none;
             cursor: pointer;
             transition: background-color 0.3s;
             font-weight: 500;
         }

         .search-button:hover {
             background-color: #2563eb;
         }

         .filter-bar {
             display: flex;
             align-items: center;
             gap: 1rem;
         }

         .filter-bar label {
             font-weight: 500;
             color: #64748b;
         }

         .filter-bar select {
             padding: 0.75rem;
             border: 1px solid  #e2e8f0;
             border-radius: 4px;
             background-color: white;
             font-size: 1rem;
             color: #1e293b;
             transition: border-color 0.3s, box-shadow 0.3s;
         }

         .filter-bar select:focus {
             outline: none;
             border-color: #FD8418;
             box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
         }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        /* Maintain minimum width for table columns */
        .book-table {
            width: 100%;
            min-width: 900px; /* Ensures table has a horizontal scrollbar when content exceeds screen width */
            border-collapse: separate;
            border-spacing: 0;
            background-color: #ffffff;
        }
        
        /* Adjust padding and font size for smaller screens */
        .book-table th,
        .book-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        /* Style for table header */
        .book-table th {
            background-color: #fdaa5f;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            white-space: nowrap;
        }

         .book-table tr:last-child td {
             border-bottom: none;
         }

         .book-table tr:nth-child(even) {
             background-color: #f1f5f9;
         }

         .book-table tr:hover {
             background-color: rgba(59, 130, 246, 0.05);
         }

         .status {
             display: inline-block;
             padding: 0.25rem 0.5rem;
             border-radius: 4px;
             font-size: 0.75rem;
             font-weight: 500;
             text-transform: uppercase;
         }

         .status.overdue {
             background-color: #fecaca;
             color: #ef4444;
         }

         .status.due {
             background-color: #d1fae5;
             color: #22c55e;
         }

         .action-button {
             display: inline-block;
             padding: 0.5rem 1rem;
             background-color: #DC3545;
             color: white;
             border: none;
             border-radius: 4px;
             font-size: 0.875rem;
             font-weight: 500;
             cursor: pointer;
             transition: background-color 0.3s;
         }

         .action-button:hover {
             background-color: #92232E;
         }

         .action-button.disabled {
             background-color: #64748b;
             cursor: not-allowed;
             margin-top: 5px;
             height: 35px;
             opacity: 0.7;
         }

         .pagination {
             display: flex;
             justify-content: center;
             gap: 1rem;
             margin-top: 2rem;
         }

         .pagination a {
             padding: 0.5rem 1rem;
             background-color: #ffffff;
             color: #1e293b;
             text-decoration: none;
             border-radius: 4px;
             transition: background-color 0.3s;
             font-weight: 500;
         }

         .pagination a:hover {
             background-color:  #e2e8f0;
         }

         .book-grid {
             display: grid;
             gap: 1.5rem;
             grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
         }

         .book-card {
             background-color: #ffffff;
             border-radius: 8px;
             box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
             padding: 1.5rem;
             transition: transform 0.3s, box-shadow 0.3s;
         }

         .book-card:hover {
             transform: translateY(-5px);
             box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
         }

         .book-title {
             font-size: 1.25rem;
             font-weight: 600;
             margin-bottom: 0.5rem;
             color: #FD8418;
         }

         .book-info {
             font-size: 0.875rem;
             color: #64748b;
             margin-bottom: 1rem;
         }

         .book-info p {
             margin-bottom: 0.25rem;
         }

         .view-toggle {
             display: flex;
             justify-content: flex-end;
             margin-bottom: 1rem;
         }

         .view-toggle button {
             background-color: #ffffff;
             border: 1px solid  #e2e8f0;
             padding: 0.5rem 1rem;
             cursor: pointer;
             transition: background-color 0.3s, color 0.3s;
             font-weight: 500;
         }

         .view-toggle button:first-child {
             border-radius: 4px 0 0 4px;
         }

         .view-toggle button:last-child {
             border-radius: 0 4px 4px 0;
         }

         .view-toggle button.active {
             background-color: #FD8418;
             color: white;
             border-color: #FD8418;
         }
         .return-book-button {
           background-color: #00762d;
           width: 100px;
           border: none;
           margin-top: 5px;
           height: 35px;
           border-radius: 5px;
           color: #ffffff;
           transition: transform 0.3s ease, background-color 0.3s ease;
         }

         .return-book-button:hover {
             background-color: #005d21;
         }

         @media (max-width: 768px) {
             .search-and-filter {
                  flex-direction: column;
             }

             .search-bar,
             .filter-bar {
                  width: 100%;
             }

             .search-input,
             .filter-bar select {
                  width: 100%;
             }


                @media (max-width: 768px) {
        .search-and-filter {
            flex-direction: column;
        }

        .search-bar,
        .filter-bar {
            width: 100%;
        }

        .search-input,
        .filter-bar select {
            width: 100%;
        }
    }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Borrowed Books</h1>
            <p>Easily track and manage overdue books to ensure timely returns and maintain an organized Borrowing process.</p>
        </header>

        <form method="GET" class="search-and-filter">
            <div class="search-bar">
                <input type="text" class="search-input" name="search" placeholder="Search by book title..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="search-button" type="submit">Search</button>
            </div>
            <div class="filter-bar">
                <select id="filter" name="filter" onchange="this.form.submit()">
                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Books</option>
                    <option value="overdue" <?php echo $filter === 'overdue' ? 'selected' : ''; ?>>Overdue Books</option>
                    <option value="due" <?php echo $filter === 'due' ? 'selected' : ''; ?>>Due Books</option>
                </select>
            </div>
        </form>

        <div id="bookContainer" class="book-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="book-card">
                    <h2 class="book-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                    <div class="book-info">
                        <p><strong>Borrower:</strong> <?php echo htmlspecialchars($row['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                        <p><strong>Library ID:</strong> <?php echo htmlspecialchars($row['member_id']); ?></p>
                        <p><strong>Borrow Date:</strong> <?php echo htmlspecialchars($row['borrow_date']); ?></p>
                        <p><strong>Due Date:</strong> <?php echo htmlspecialchars($row['due_date']); ?></p>
                    </div>
                    <div>
                        <span class="status <?php echo $row['due_date'] < date('Y-m-d') ? 'overdue' : 'due'; ?>">
                            <?php echo $row['due_date'] < date('Y-m-d') ? 'OVERDUE' : 'DUE'; ?>
                        </span>
                    </div>
                    <div class="book-actions">
                        <?php if ($row['due_date'] < date('Y-m-d')): ?>
                            <button class="action-button" data-book-id="<?php echo $row['book_id']; ?>"
                                data-email="<?php echo $row['email']; ?>"
                                data-name="<?php echo $row['name']; ?>"
                                data-title="<?php echo $row['title']; ?>"
                                data-due-date="<?php echo $row['due_date']; ?>">
                                Send Reminder
                            </button>
                        <?php else: ?>
                            <button class="action-button disabled" disabled>No Action Needed</button>
                        <?php endif; ?>
                        <button class="return-book-button" data-book-id="<?php echo $row['book_id']; ?>">
                            Return
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo $filter; ?>">Previous</a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo $filter; ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Theme toggling functionality
        function toggleTheme() {
            document.body.setAttribute('data-theme', 
                document.body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Handle action buttons for sending reminders
            document.querySelectorAll('.action-button').forEach(button => {
                button.addEventListener('click', function() {
                    const bookId = this.getAttribute('data-book-id');
                    const borrowerEmail = this.getAttribute('data-email');
                    const borrowerName = this.getAttribute('data-name');
                    const bookTitle = this.getAttribute('data-title');
                    const dueDate = this.getAttribute('data-due-date');

                    fetch('send_reminder.php', {
                        method: 'POST',
                        body: JSON.stringify({
                            bookId: bookId,
                            borrowerEmail: borrowerEmail,
                            borrowerName: borrowerName,
                            bookTitle: bookTitle,
                            dueDate: dueDate
                        }),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Reminder sent successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to send reminder. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'Try Again'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                });
            });

            // Handle return book buttons
            document.querySelectorAll('.return-book-button').forEach(button => {
                button.addEventListener('click', function() {
                    const bookId = this.getAttribute('data-book-id');
                    const bookTitle = this.closest('.book-card').querySelector('.book-title').textContent;

                    Swal.fire({
                        title: 'Return Book',
                        text: `Are you sure you want to return "${bookTitle}"?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, return it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('return_book.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: 'book_id=' + bookId
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        title: 'Returned!',
                                        text: data.message,
                                        icon: 'success'
                                    }).then(() => {
                                        const bookCard = button.closest('.book-card');
                                        if (bookCard) {
                                            bookCard.remove();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: data.message,
                                        icon: 'error'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'Unexpected Error',
                                    text: 'An unexpected error occurred.',
                                    icon: 'error'
                                });
                            });
                        }
                    });
                });
            });

            // Add animations
            anime({
                targets: '.book-card',
                scale: [0.9, 1],
                opacity: [0, 1],
                duration: 1000,
                delay: anime.stagger(100),
                easing: 'easeOutElastic(1, .8)'
            });
        });
    </script>
</body>
</html>