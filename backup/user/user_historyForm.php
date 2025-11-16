<?php
include('../config/connect.php');
include 'navbar.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$recordsPerPage = 10;

// SQL query to fetch all data without LIMIT
$sql = "SELECT book_id, title, name, borrow_date, return_date FROM returned_books
        WHERE member_id = ? AND (title LIKE ? OR name LIKE ?)";

$stmt = $conn->prepare($sql);
$searchTerm = '%' . $search . '%';
$stmt->bind_param("iss", $member_id, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

$totalRecords = count($data);
$totalPages = ceil($totalRecords / $recordsPerPage);

// Encode the data as JSON for use in JavaScript
$jsonData = json_encode($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>History</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .container-returned {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            margin-bottom: 200px;
            background: white;
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #0B5E2B;
            margin-bottom: 2rem;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .search-container-returned {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            gap: 1rem;
        }

        .search-form-returned {
            display: flex;
            flex: 1;
            max-width: 600px;
        }

        #searchInput-returned {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px 0 0 6px;
            font-size: 0.875rem;
        }

        .search-form-returned button {
            padding: 0.5rem 1rem;
            background: #F2AC60;
            color: white;
            border: none;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .table-container-returned {
            margin-top: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8f9fa;
            color: #64748b;
            font-weight: 500;
            text-align: left;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
            color: #333;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .pagination-returned {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding: 1rem 0;
        }

        .pagination-button-returned {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #000;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .pagination-button-returned:hover:not(:disabled) {
            background: #f8f9fa;
        }

        .pagination-button-returned:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-info-returned {
            font-size: 0.875rem;
            color: #64748b;
        }

        .no-results-returned {
            text-align: center;
            padding: 2rem;
            color: #64748b;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .container-returned {
                padding: 1rem;
                margin: 1rem;
            }

            .search-container-returned {
                flex-direction: column;
            }

            .search-form-returned {
                width: 100%;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="container-returned">
        <h2>History</h2>
        <div class="search-container-returned">
            <form class="search-form-returned" action="" method="GET">
                <input type="text" id="searchInput-returned" name="search" placeholder="Search by title or borrower name" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class='bx bx-search'></i></button>
            </form>
        </div>
        <div class="table-container-returned">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Title</th>
                        <th>Borrower Name</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    <!-- Table content will be populated by jQuery -->
                </tbody>
            </table>
        </div>

        <div class="pagination-returned">
            <button id="prevPage" class="pagination-button-returned">Previous</button>
            <span id="paginationInfo" class="pagination-info-returned"></span>
            <button id="nextPage" class="pagination-button-returned">Next</button>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        const data = <?php echo $jsonData; ?>;
        const recordsPerPage = <?php echo $recordsPerPage; ?>;
        let currentPage = 1;

        function displayRecords(page) {
            const start = (page - 1) * recordsPerPage;
            const end = start + recordsPerPage;
            const records = data.slice(start, end);

            $('#historyTableBody').empty();
            if (records.length === 0) {
                $('#historyTableBody').append('<tr><td colspan="5" class="no-results-returned">No records found</td></tr>');
            } else {
                $.each(records, function(index, record) {
                    const rowNumber = start + index + 1;
                    $('#historyTableBody').append(`
                        <tr>
                            <td>${rowNumber}</td>
                            <td>${record.title}</td>
                            <td>${record.name}</td>
                            <td>${record.borrow_date}</td>
                            <td>${record.return_date}</td>
                        </tr>
                    `);
                });
            }

            updatePaginationInfo();
        }

        function updatePaginationInfo() {
            const totalPages = Math.ceil(data.length / recordsPerPage);
            $('#paginationInfo').text(`Page ${currentPage} of ${totalPages}`);
            $('#prevPage').prop('disabled', currentPage === 1);
            $('#nextPage').prop('disabled', currentPage === totalPages);
        }

        $('#prevPage').click(function() {
            if (currentPage > 1) {
                currentPage--;
                displayRecords(currentPage);
            }
        });

        $('#nextPage').click(function() {
            const totalPages = Math.ceil(data.length / recordsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                displayRecords(currentPage);
            }
        });

        // Initial display
        displayRecords(currentPage);

        // Search input delay logic
        let timeout;
        $('#searchInput-returned').on('keyup', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                $(this).closest('form').submit();
            }, 1000);  // Delay in milliseconds
        });
    });
    </script>
</body>
</html>
