<?php
include('../config/connect.php');
include 'navbar.php';

function getExpiryOptions() {
    return [
        5 => '5 days',
        7 => '7 days',
        14 => '14 days'
    ];
}
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
    margin-right: 10px;
    padding: 10px;
    background-color: transparent;
    border: 2px solid #02A95C;
    border-radius: 5px;
    width: 100px;
    color: #02A95C;

        }

.view-toggle:hover {
    background-color: #02A95C;
    color: white;
    cursor: pointer;
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

        /* Condemn Modal */
        #condemnModal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 20px;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .modal-content {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .close {
            align-self: flex-end;
            cursor: pointer;
        }
        .condemnsubmit {
          padding: 8px 16px;
            background-color: #02A95C;
            color: white;
            width: 100%;
            height: 40px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-button {
            background-color: #02A95C;
            color: white;
            width: 100px;
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .dropdown-button:hover {
            background-color: #015d33;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            min-width: 150px;
        }

        .dropdown-content button {
            background-color: white;
            color: black;
            padding: 10px;
            border: none;
            text-align: left;
            width: 100%;
            cursor: pointer;
        }

        .dropdown-content button:hover {
            background-color: #ddd;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }
            @media (max-width: 768px) {
            .book-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
            .cover-container img {
                width: 140px;
                height: 210px;
                border-radius: 10px;
                box-shadow: 0 6px 6px rgba(119, 119, 119, 0.75);
            }
            .book-title {
                font-size: 15px;
                font-weight: bold;
                color: #333333;
                margin-bottom: 10px;
            }
            .book__details {
                font-size: 12px;
                margin-top: -10px;
                text-transform: capitalize;
                color: #888888;
            }

            .book-details p {
                margin: 5px 0;
                color: #666666;
                font-size: 10px;
            }
            .description-label {
                font-weight: 600;
                color: black;
                font-size: 12px;
                margin-top: -5px;
            }

            .book-card {
                background-color: #fff;
                border-radius: 8px;
                padding: 0px;
                display: flex;
                gap: 10px;
            }
            .dropdown-button {
                background-color: #007bff;
                color: white;
                width: 100px;
                padding: 8px 15px;
                border: none;
                cursor: pointer;
                border-radius: 5px;
                margin-left: -150px;
                margin-top: 5px;
                width: 140px;
            }
            .dropdown {
                position: relative;
                display: inline-block;
            }

            .dropdown-button:hover {
                background-color: #0056b3;
            }

            .dropdown-content {
                display: none;
                position: absolute;
                top: 100%;
                left: -150px;
                background-color: #f9f9f9;
                box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
                z-index: 1;
                min-width: 140px;
            }

            .dropdown-content button {
                background-color: white;
                color: black;
                padding: 10px;
                border: none;
                text-align: left;
                width: 100%;
                cursor: pointer;
            }

            .dropdown-content button:hover {
                background-color: #ddd;
            }

            .dropdown:hover .dropdown-content {
                display: block;
            }
            .description {
                margin-top: 10px;
                color: #888888;
                font-size: 14px;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }   
    </style>
</head>
<body>
    <div class="container">
        <h1>Book Catalog</h1>
        <div class="filter-container">
            <select id="bookTypeFilter">
                <option value="all">All Books</option>
                <option value="physical">Physical Books</option>
                <option value="ebook">E-Books</option>
            </select>
            <button id="viewToggleBtn" class="view-toggle">List View</button>
            <button id="openModalBtn" class="insert_book">Add Book</button>
        </div>

        <div class="book-grid">
            <?php
            $sql = "SELECT * FROM manage_books WHERE status = 'available'";
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
                                <div class="dropdown">
                                    <button class="dropdown-button">Actions</button>
                                    <div class="dropdown-content">
                                        <button onclick="updateBook(<?= htmlspecialchars($row['book_id'], ENT_QUOTES) ?>)">Update</button>
                                        <button onclick="deleteBook(<?= htmlspecialchars($row['book_id'], ENT_QUOTES) ?>)">Delete</button>
                                        <button onclick="condemnBook(<?= htmlspecialchars($row['book_id'], ENT_QUOTES) ?>)">Condemn</button>
                                    </div>
                                </div>
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
                                      <button class="buttoncondemn" onclick="condemnBook(<?= htmlspecialchars($row['book_id'], ENT_QUOTES) ?>)">
                                          Condemn
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

    <!-- Insert And Update Modal -->
    <?php include 'insert_update.php'; ?>

    <!-- Condemn Reason Modal -->
<div id="condemnModal" style="display: none;">
    <div class="modal-content">
        <span class="close" id="condemnClose">&times;</span>
        <h2>Condemn Book</h2>
        <p>Provide a reason for condemning this book:</p>
        <textarea id="condemnReason" rows="4" placeholder="Enter reason here..." required></textarea>
        <button class="condemnsubmit" id="submitCondemnReason">Submit</button>
    </div>
</div>


    <script>
    let selectedBookId = null;

    function condemnBook(bookId) {
        selectedBookId = bookId; // Store the selected book ID
        document.getElementById("condemnModal").style.display = "block";
    }

    // Close the modal
    document.getElementById("condemnClose").onclick = function () {
        document.getElementById("condemnModal").style.display = "none";
    };

    // Submit the reason
    document.getElementById("submitCondemnReason").onclick = function () {
        const reason = document.getElementById("condemnReason").value.trim();
        if (!reason) {
            alert("Please provide a reason for condemning.");
            return;
        }

        $.ajax({
            url: 'condemn_book.php',
            method: 'POST',
            data: { book_id: selectedBookId, reason: reason },
            success: function (response) {
                alert(response);
                location.reload(); // Reload the page to reflect changes
            },
            error: function () {
                alert('Failed to condemn the book. Please try again.');
            }
        });
    };
        // Add Book Modal functionality
        const addBookModal = document.getElementById("addBookModal");
        const addBtn = document.getElementById("openModalBtn");
        const addSpan = addBookModal.getElementsByClassName("lender-close")[0];

        addBtn.onclick = function() {
            addBookModal.style.display = "block";
        }

        addSpan.onclick = function() {
            addBookModal.style.display = "none";
        }

        // Update Book Modal functionality
        const updateBookModal = document.getElementById("updateBookModal");
        const updateSpan = updateBookModal.getElementsByClassName("lender-close")[0];

        updateSpan.onclick = function() {
            updateBookModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == addBookModal) {
                addBookModal.style.display = "none";
            }
            if (event.target == updateBookModal) {
                updateBookModal.style.display = "none";
            }
        }

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
                bookGrid.style.display = 'grid';
                bookTable.style.display = 'none';
                viewToggleBtn.textContent = 'List View';
            }
        });


        function deleteBook(bookId) {
            if (confirm("Are you sure you want to delete this book?")) {
                window.location.href = `admin_processDelete.php?book_id=${bookId}`;
            }
        }

        function togglePdfUpload() {
            var bookType = document.getElementById('book_type').value;
            var pdfUpload = document.getElementById('pdf_upload');
            var pdfInput = document.getElementById('pdf');

            if (bookType === 'ebook') {
                pdfUpload.style.display = 'block';
                pdfInput.required = true;
            } else {
                pdfUpload.style.display = 'none';
                pdfInput.required = false;
            }
        }

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

        function updateBook(bookId) {
            $.ajax({
                url: 'admin_getbookDetails.php',
                method: 'GET',
                data: { book_id: bookId },
                dataType: 'json',
                success: function (data) {
                    $('#updateBookId').val(data.book_id);
                    $('#updateTitle').val(data.title);
                    $('#updateAuthor').val(data.author);
                    $('#updateGenre').val(data.genre);
                    $('#updateIsbn').val(data.isbn);
                    $('#updateBookType').val(data.book_type);
                    $('#updatePublicationDate').val(data.publication_date);
                    $('#updatePublisher').val(data.publisher);
                    $('#updateStatus').val(data.status);
                    $('#updateDescription').val(data.description);
                    $('#updateExpiryDays').val(data.expiry_days);

                    $('#updateBookModal').css('display', 'block');
                },
                error: function () {
                    alert('Failed to fetch book details. Please try again.');
                }
            });
        }
    </script>
</body>
</html>
