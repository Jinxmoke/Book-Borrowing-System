<?php
include('../config/connect.php');
include 'navbar.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Borrowing System</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        .view-toggle {
            padding: 8px 16px;
            background-color: #02A95C;
            color: white;
            width: 110px;
            height: 40px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .view-toggle:hover {
            transform: scale(1.1);
            background-color: #029f54;
        }

        .book-table {
            width: 100%;
            margin-top: 20px;
            overflow-x: auto;
        }

        .book-table table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .book-table th,
        .book-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .book-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .table-book-cover {
            width: 50px;
            height: 70px;
            object-fit: cover;
        }

        .book-table tr:hover {
            background-color: #f9f9f9;
        }

        .book-table button {
            margin: 2px;
            padding: 5px 10px;
        }

        .buttoncondemn {
            background-color: #FF5733;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-block;
        }

        .buttoncondemn:hover {
            background-color: #E74C3C;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Book Collection</h1>
        <div class="filter-container">
            <select id="bookTypeFilter">
                <option value="all">All Books</option>
                <option value="physical">Physical Books</option>
                <option value="ebook">E-Books</option>
            </select>
            <button id="viewToggleBtn" class="view-toggle">List View</button>
        </div>

        <div class="book-grid">
            <?php
            $sql = "SELECT * FROM manage_books WHERE status = 'borrowed'";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagePath = '../uploads/' . $row["image"];
                    ?>
                    <div class="book-card" data-book-type="<?= htmlspecialchars($row["book_type"], ENT_QUOTES) ?>">
                        <div class="cover-container">
                            <img src="<?= htmlspecialchars($imagePath, ENT_QUOTES) ?>"
                                 alt="<?= htmlspecialchars($row["title"], ENT_QUOTES) ?>"
                                 class="book-cover">
                        </div>
                        <div>
                            <div class="book-title"><?= htmlspecialchars($row["title"], ENT_QUOTES) ?></div>
                            <div class="book__details">
                                <p>Genre:‎ <?= htmlspecialchars($row["genre"], ENT_QUOTES) ?></p>
                                <p>ISBN:‎ <?= htmlspecialchars($row["isbn"], ENT_QUOTES) ?></p>
                                <p>Status:‎ <?= htmlspecialchars($row["status"], ENT_QUOTES) ?></p>
                                <p>Book Type:‎ <?= htmlspecialchars($row["book_type"], ENT_QUOTES) ?></p>
                                <p>Expiry: <?= htmlspecialchars($row["expiry_days"], ENT_QUOTES) ?> days</p>
                            </div>
                            <div class="description">
                                <div class="description-label">Description:</div>
                                <p><?= htmlspecialchars($row["description"], ENT_QUOTES) ?></p>
                            </div>
                            <div class="button-group">
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No available books found. Start by adding your first book!</p>";
            }
            ?>
        </div>

        <!-- Table View -->
        <div class="book-table" style="display: none;">
            <table>
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Genre</th>
                        <th>ISBN</th>
                        <th>Status</th>
                        <th>Book Type</th>
                        <th>Expiry</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result->data_seek(0);
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $imagePath = '../uploads/' . $row["image"];
                            ?>
                            <tr class="book-row" data-book-type="<?= htmlspecialchars($row["book_type"], ENT_QUOTES) ?>">
                                <td>
                                    <img src="<?= htmlspecialchars($imagePath, ENT_QUOTES) ?>"
                                         alt="<?= htmlspecialchars($row["title"], ENT_QUOTES) ?>"
                                         class="table-book-cover">
                                </td>
                                <td><?= htmlspecialchars($row["title"], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row["genre"], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row["isbn"], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row["status"], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row["book_type"], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row["expiry_days"], ENT_QUOTES) ?> days</td>
                                <td>
                                  <div class="button-group">
                                      <button class="buttondelete"
                                              onclick="deleteBook(<?= htmlspecialchars($row['book_id'], ENT_QUOTES) ?>)">
                                          Delete
                                      </button>
                                      <button class="buttonupdate"
                                              onclick="updateBook(<?= htmlspecialchars($row['book_id'], ENT_QUOTES) ?>)">
                                          Update
                                      </button>
                                  </div>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <button id="prevPage" disabled><i class="fas fa-chevron-left"></i></button>
            <button id="nextPage"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>

    <script>
        // View toggle functionality
        const viewToggleBtn = document.getElementById('viewToggleBtn');
        const bookGrid = document.querySelector('.book-grid');
        const bookTable = document.querySelector('.book-table');

        viewToggleBtn.addEventListener('click', function() {
            const isGridView = bookGrid.style.display !== 'none';
            if (isGridView) {
                bookGrid.style.display = 'none';
                bookTable.style.display = 'block';
                viewToggleBtn.textContent = 'Grid View';
            } else {
                bookGrid.style.display = 'flex';
                bookTable.style.display = 'none';
                viewToggleBtn.textContent = 'List View';
            }
        });

        $(document).ready(function () {
            const itemsPerPage = 8;
            let currentPage = 1;
            const $bookCards = $('.book-card');
            const $bookRows = $('.book-row');

            function filterBooks() {
                const selectedType = $('#bookTypeFilter').val();
                if (selectedType === 'all') {
                    $('.book-card, .book-row').show();
                } else {
                    $('.book-card, .book-row').hide()
                        .filter(`[data-book-type="${selectedType}"]`).show();
                }
                updateView();
            }

            function showPage(page) {
                const selectedType = $('#bookTypeFilter').val();
                const filteredCards = selectedType === 'all'
                    ? $('.book-card')
                    : $('.book-card').filter(`[data-book-type="${selectedType}"]`);
                const filteredRows = selectedType === 'all'
                    ? $('.book-row')
                    : $('.book-row').filter(`[data-book-type="${selectedType}"]`);

                const start = (page - 1) * itemsPerPage;
                const end = start + itemsPerPage;

                $('.book-card, .book-row').hide();
                filteredCards.slice(start, end).show();
                filteredRows.slice(start, end).show();

                $('#prevPage').prop('disabled', page === 1);
                $('#nextPage').prop('disabled', page >= Math.ceil(filteredCards.length / itemsPerPage));
            }

            function updateView() {
                currentPage = 1;
                showPage(currentPage);
            }

            $('#prevPage').click(function () {
                if (currentPage > 1) {
                    currentPage--;
                    showPage(currentPage);
                }
            });

            $('#nextPage').click(function () {
                const selectedType = $('#bookTypeFilter').val();
                const filteredItems = selectedType === 'all'
                    ? $('.book-card')
                    : $('.book-card').filter(`[data-book-type="${selectedType}"]`);

                if (currentPage < Math.ceil(filteredItems.length / itemsPerPage)) {
                    currentPage++;
                    showPage(currentPage);
                }
            });

            $('#bookTypeFilter').change(function () {
                filterBooks();
            });

            updateView();
        });

    </script>
  </body>
  </html>
