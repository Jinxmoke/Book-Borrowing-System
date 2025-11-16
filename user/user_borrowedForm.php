<?php
session_start();


if (isset($_SESSION['name']) && isset($_SESSION['member_id'])) {
    $name = $_SESSION['name'];
    $member_id = $_SESSION['member_id'];
} else {

    header("Location: /e-book/login_form.php");
    exit;
}

include('../config/connect.php');
include 'navbar.php';

$lender_id = $_SESSION['member_id'];

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Number of records per page
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = " AND (b.title LIKE '%$search%' OR u.name LIKE '%$search%')";
}

// Query to get borrowed books
$query = "SELECT b.book_id, b.title, b.member_id, u.name, b.borrow_date, b.due_date
          FROM borrowed_books b
          JOIN user_info u ON b.member_id = u.member_id
          WHERE b.lender_id = ? $searchCondition
          ORDER BY b.borrow_date DESC
          LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $lender_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Get total number of records for pagination
$totalQuery = "SELECT COUNT(*) as total FROM borrowed_books b
               JOIN user_info u ON b.member_id = u.member_id
               WHERE b.lender_id = ? $searchCondition";
$stmtTotal = $conn->prepare($totalQuery);
$stmtTotal->bind_param("i", $lender_id);
$stmtTotal->execute();
$totalResult = $stmtTotal->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowed Books</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="user_style.css">
</head>
<body>
    <div class="borrowed-container">
        <h2 class="borrowed-title">BORROWED BOOKS</h2>

        <div class="borrowed-search-container">
            <form method="GET" action="" class="borrowed-search-form">
                <input type="text" id="borrowedSearchInput" name="search" placeholder="Search books..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="borrowed-table-container">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Title</th>
                        <th>Borrower Name</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $counter = ($page - 1) * $limit + 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$counter}</td> <!-- Display No. -->
                                    <td>{$row['title']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['borrow_date']}</td>
                                    <td>{$row['due_date']}</td>
                                </tr>";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='5' class='borrowed-no-results'>No borrowed books found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="borrowed-pagination">
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($search); ?>"
               class="borrowed-pagination-button"
               <?php if ($page <= 1) echo 'disabled'; ?>>
                Previous
            </a>

            <span class="borrowed-pagination-info">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>

            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($search); ?>"
               class="borrowed-pagination-button"
               <?php if ($page >= $totalPages) echo 'disabled'; ?>>
                Next
            </a>
        </div>
    </div>

    <script>
        document.getElementById('borrowedSearchInput').addEventListener('keyup', function() {
            this.form.submit();
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$stmtTotal->close();
$conn->close();
?>
