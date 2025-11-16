<?php
include('../config/connect.php');
include 'navbar.php';
// Fetch the total count of members from the user_info table
$queryMembers = "SELECT COUNT(*) AS total_members FROM user_info";
$resultMembers = mysqli_query($conn, $queryMembers);
$total_members = 0;
if ($resultMembers && mysqli_num_rows($resultMembers) > 0) {
    $rowMembers = mysqli_fetch_assoc($resultMembers);
    $total_members = $rowMembers['total_members'];
}

// Query to count borrowed books
$sqlBorrowedBooksCount = "SELECT COUNT(*) AS borrowed_books_count FROM borrowed_books";
$resultBorrowedBooksCount = $conn->query($sqlBorrowedBooksCount);

$borrowedBooksCount = 0;
if ($resultBorrowedBooksCount && $resultBorrowedBooksCount->num_rows > 0) {
    $row = $resultBorrowedBooksCount->fetch_assoc();
    $borrowedBooksCount = $row['borrowed_books_count'];
}

// Query to count returned books
$sqlReturnedBooksCount = "SELECT COUNT(*) AS returned_books_count FROM returned_books";
$resultReturnedBooksCount = $conn->query($sqlReturnedBooksCount);

$returnedBooksCount = 0;
if ($resultReturnedBooksCount && $resultReturnedBooksCount->num_rows > 0) {
    $row = $resultReturnedBooksCount->fetch_assoc();
    $returnedBooksCount = $row['returned_books_count'];
}

// Query to fetch borrowed books for the table display (no lender_id used)
$sql = "SELECT name, member_id, book_type, title, borrow_date, due_date FROM borrowed_books LIMIT 5";
$result = $conn->query($sql);

$borrowedBooks = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $borrowedBooks[] = $row;
    }
}

// Fetch overdue books
$query = "SELECT book_id, title, name, due_date
          FROM borrowed_books
          WHERE due_date < CURDATE() AND status != 'returned' AND book_type = 'physical'";
$result = mysqli_query($conn, $query);
$overdue_books = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Dashboard</title>
    <link rel="stylesheet" href="ldr_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<style>
/* Admin Dashboard */
.dashboard-container {
    max-width: 1200px;
    margin: auto;
    margin-bottom: 200px;
    padding: 2rem;
}

.dashboard-overview-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #2c3e50;
}

.dashboard-metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.dashboard-metric-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    transition: transform 0.2s ease;
}

.dashboard-metric-card:hover {
    transform: translateY(-4px);
}

.dashboard-metric-card.total-books { background-color: #A7C7E7; }
.dashboard-metric-card.borrowed { background-color: #FFB366; }
.dashboard-metric-card.returned { background-color: #98D8BF; }
.dashboard-metric-card.members { background-color: #98D8BF; }
.dashboard-metric-card.not-returned { background-color: #FF9999; }

.dashboard-metric-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.dashboard-metric-title {
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    color: rgba(0, 0, 0, 0.7);
}

.dashboard-metric-icon {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 8px;
}

.dashboard-metric-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0.5rem 0;
}

.dashboard-metric-link {
    margin-top: auto;
    text-decoration: none;
    color: rgba(0, 0, 0, 0.7);
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    transition: color 0.2s ease;
}

.dashboard-metric-link:hover {
    color: rgba(0, 0, 0, 0.9);
}

.dashboard-metric-link i {
    margin-left: 0.5rem;
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }

    .dashboard-metrics-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .dashboard-metric-value {
        font-size: 2rem;
    }
}

.dashboard-table-container {
    margin-top: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.dashboard-table-title {
    font-size: 1.25rem;
    font-weight: 600;
    padding: 1.5rem;
    background-color: #A7C7E7;
    color: #2c3e50;
}

.dashboard-responsive-table {
    width: 100%;
    border-collapse: collapse;
}

.dashboard-responsive-table th,
.dashboard-responsive-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.dashboard-responsive-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #2c3e50;
}

.dashboard-responsive-table tr:hover {
    background-color: #f8f9fa;
}

@media (max-width: 768px) {
    .dashboard-responsive-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
}

.dashboard-status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.875rem;
    font-weight: 500;
}

.dashboard-status-borrowed {
    background-color: #FFB366;
    color: #000;
}
.overdue-notification {
    background-color: white;
    color: white;
    padding: 5px 10px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    position: relative;
    width: 214px;
}
.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.overdue-books-banner {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 10px 20px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    max-width: 350px;
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 15px;
    transition: all 0.3s ease;
}

.overdue-books-banner:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

.overdue-books-banner h3 {
    margin: 0;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.overdue-books-banner h3::before {
    font-size: 1.5rem;
}

.overdue-books-banner .action-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.overdue-books-banner button {
    background-color: white;
    color: #e74c3c;
    border: none;
    padding: 5px 15px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s, transform 0.1s;
}

.overdue-books-banner button:hover {
    background-color: #f0f0f0;
    transform: scale(1.05);
}

.close-button {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    font-size: 24px;
    cursor: pointer;
    transition: color 0.2s;
}

.close-button:hover {
    color: white;
}


.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    background-color: #f8f9fa;
    margin: 5% auto;
    padding: 30px;
    border-radius: 15px;
    width: 90%;
    max-width: 700px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    position: relative;
}

.overdue-books-list {
    display: grid;
    gap: 15px;
}

.overdue-book {
    background-color: white;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}

.overdue-book:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

.overdue-book h4 {
    margin-top: 0;
    color: #2c3e50;
    font-size: 1.1rem;
}

.overdue-book p {
    margin: 5px 0;
    color: #6c757d;
}

#closeOverdueModal {
    display: block;
    margin: 20px auto 0;
    padding: 12px 25px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.2s, transform 0.1s;
}

#closeOverdueModal:hover {
    background-color: #2980b9;
    transform: scale(1.05);
}

@media screen and (max-width: 600px) {
    .overdue-books-banner {
        right: 10px;
        left: 10px;
        bottom: 10px;
    }

    .modal-content {
        width: 95%;
        margin: 10% auto;
    }
}

</style>
<body>
<div class="dashboard-container">
    <div class="header-container">
        <h1 class="dashboard-overview-title">DASHBOARD OVERVIEW</h1>
    </div>

    <div class="dashboard-metrics-grid">
        <div class="dashboard-metric-card total-books">
            <div class="dashboard-metric-header">
                <div class="dashboard-metric-title">BORROWED BOOKS</div>
                <div class="dashboard-metric-icon">
                    <i class="fas fa-book-reader"></i>
                </div>
            </div>
            <div class="dashboard-metric-value"><?php echo number_format($borrowedBooksCount); ?></div>
            <a href="admin_borrowedForm.php" class="dashboard-metric-link">
                VIEW MORE <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="dashboard-metric-card borrowed">
            <div class="dashboard-metric-header">
                <div class="dashboard-metric-title">RETURNED BOOKS</div>
                <div class="dashboard-metric-icon">
                    <i class="fas fa-undo"></i>
                </div>
            </div>
            <div class="dashboard-metric-value"><?php echo number_format($returnedBooksCount); ?></div>
            <a href="admin_returnedBooksForm.php" class="dashboard-metric-link">
                VIEW MORE <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="dashboard-metric-card returned">
            <div class="dashboard-metric-header">
                <div class="dashboard-metric-title">TOTAL MEMBERS</div>
                <div class="dashboard-metric-icon">
                    <i class="fa-regular fa-user"></i>
                </div>
            </div>
            <div class="dashboard-metric-value"><?php echo number_format($total_members); ?></div>
            <a href="admin_manageMembersForm.php" class="dashboard-metric-link">
                VIEW MORE <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="dashboard-table-container">
        <div class="dashboard-table-title">Recently Borrowed Books</div>
        <table class="dashboard-responsive-table">
            <thead>
                <tr>
                    <th>Library ID</th>
                    <th>Name</th>
                    <th>Title</th>
                    <th>Book Type</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($borrowedBooks as $book): ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['member_id']); ?></td>
                    <td><?php echo htmlspecialchars($book['name']); ?></td>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['book_type']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($book['borrow_date'])); ?></td>
                    <td><?php echo date('M d, Y', strtotime($book['due_date'])); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($borrowedBooks)): ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No borrowed books found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($overdue_books)): ?>
<div id="overdueBooksBanner" class="overdue-books-banner">
    <h3>Overdue Books (<?php echo count($overdue_books); ?>)</h3>
    <p>There are overdue books that need attention.</p>
    <button class="view_overdue" id="viewOverdueBooks">View Overdue Books</button>
    <button id="closeOverdueBanner" class="close-button">&times;</button>
</div>

<div id="overdueModal" class="modal">
    <div class="modal-content">
        <h2>Overdue Books</h2>
        <div class="overdue-books-list">
            <?php foreach ($overdue_books as $book): ?>
            <div class="overdue-book">
                <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                <p>Borrowed by: <?php echo htmlspecialchars($book['name']); ?></p>
                <p>Due date: <?php echo date('M d, Y', strtotime($book['due_date'])); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <button id="closeOverdueModal">Close</button>
    </div>
</div>
<?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const overdueBooksBanner = document.getElementById('overdueBooksBanner');
        const viewOverdueBooks = document.getElementById('viewOverdueBooks');
        const closeOverdueBanner = document.getElementById('closeOverdueBanner');
        const overdueModal = document.getElementById('overdueModal');
        const closeOverdueModal = document.getElementById('closeOverdueModal');

        if (overdueBooksBanner) {
            viewOverdueBooks.addEventListener('click', function() {
                overdueModal.style.display = 'block';
            });

            closeOverdueBanner.addEventListener('click', function() {
                overdueBooksBanner.style.display = 'none';
            });

            closeOverdueModal.addEventListener('click', function() {
                overdueModal.style.display = 'none';
            });

            window.addEventListener('click', function(event) {
                if (event.target == overdueModal) {
                    overdueModal.style.display = 'none';
                }
            });
        }
    });
</script>

</body>
</html>
