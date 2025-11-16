<?php
include('../config/connect.php');
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Condemned Books Archive</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --background: #f4f6f9;
            --text-primary: #2c3e50;
            --text-secondary: #7f8c8d;
            --border-light: #e7eaf3;
            --accent-color: #3498db;
            --hover-color: #2980b9;
            --transition: all 0.3s ease;
        }

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
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-light);
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
        }

        .page-header i {
            margin-right: 1rem;
            color: var(--accent-color);
        }

        .book-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        .book-table thead {
            background-color: var(--background);
            border-bottom: 2px solid var(--border-light);
        }

        .book-table th {
            text-align: left;
            padding: 1rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .book-table tbody tr {
            transition: var(--transition);
            border-bottom: 1px solid var(--border-light);
        }

        .book-table tbody tr:last-child {
            border-bottom: none;
        }

        .book-table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.03);
        }

        .book-table td {
            padding: 1rem;
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .action-button {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .action-button:hover {
            background-color: var(--hover-color);
            transform: translateY(-2px);
        }

        .action-button i {
            margin-right: 0.3rem;
        }

        .no-data {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
            font-style: italic;
        }

        @media screen and (max-width: 768px) {
            .book-table {
                font-size: 0.9rem;
            }

            .book-table th, .book-table td {
                padding: 0.75rem;
            }
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
        .search-container {
            margin-bottom: 2rem;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 1rem;
        }
        
        .search-container input {
            padding: 0.75rem;
            font-size: 1rem;
            border-radius: 4px;
            border: 1px solid #ddd;
            width: 300px;
            transition: border-color 0.3s ease;
            outline: none;
        }
        
        .search-button {
          background-color: #FD8418;
          color: white;
          border: 2px solid #FD8418;
          margin-left: -6px;
          padding: 0.79rem;
          cursor: pointer;
          font-size: 0.85rem;
          transition: #FD8418;
        }
        
        }
        .search-container input:focus {
            border-color: var(--accent-color);
        }
        
        
        .search-container button:hover {
          border: 2px solid  #A85810;
            background-color: #A85810;
        }
        
        .search-container input::placeholder {
            color: var(--text-secondary);
        }
        @media screen and (max-width: 768px) {
        .book-table {
            font-size: 0.9rem;
        }

        .book-table th, .book-table td {
            padding: 0.75rem;
        }

        .search-container {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }

        .search-container input {
            width: 100%;
        }

        .search-button {
            width: 100%;
            margin-left: 0;
        }
    }

        @media screen and (max-width: 480px) {
        .book-table {
            display: block;
            overflow-x: auto;
        }

        .book-table th, .book-table td {
            white-space: nowrap;
        }

        .action-button {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
        }

        .header h1 {
            font-size: 1.25rem;
        }
    }
        </style>
    </head>
    <body>
      <div class="container">
        <header class="header">
            <h1>Condemned Books Archive</h1>
            <p>Track and manage condemned books by keeping a detailed record of their status, including reasons for condemnation, dates of removal from circulation.</p>
        </header>
        <div class="search-container">
   <form method="GET" action="">
       <input type="text" name="search" placeholder="Search by Book ID, Title, Genre, ISBN" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
       <button type="submit" class="search-button"><i class="fas fa-search"></i> Search</button>
   </form>
</div>

<table class="book-table">
   <thead>
       <tr>
           <th>Book ID</th>
           <th>Title</th>
           <th>Genre</th>
           <th>ISBN</th>
           <th>Condemned Date</th>
           <th>Reason</th>
           <th>Actions</th>
       </tr>
   </thead>
   <tbody>
       <?php
       // Get the search term from the GET request, if any
       $search = isset($_GET['search']) ? $_GET['search'] : '';

       // Modify the SQL query based on the search term
       $sql = "SELECT id, book_id, title, genre, isbn, description, condemned_date, condemn_reason FROM condemned_books";
       if ($search) {
           $sql .= " WHERE book_id LIKE '%$search%' OR title LIKE '%$search%' OR genre LIKE '%$search%' OR isbn LIKE '%$search%'";
       }
       $result = $conn->query($sql);

       if ($result->num_rows > 0) {
           while ($row = $result->fetch_assoc()) {
               echo "<tr>";
               echo "<td>" . htmlspecialchars($row['book_id']) . "</td>";
               echo "<td>" . htmlspecialchars($row['title']) . "</td>";
               echo "<td>" . htmlspecialchars($row['genre'] ?? 'N/A') . "</td>";
               echo "<td>" . htmlspecialchars($row['isbn'] ?? 'N/A') . "</td>";
               echo "<td>" . htmlspecialchars($row['condemned_date']) . "</td>";
               echo "<td>" . htmlspecialchars($row['condemn_reason'] ?? 'N/A') . "</td>";
               echo "<td>
                   <form action='update_book_status.php' method='POST'>
                       <input type='hidden' name='book_id' value='" . $row['book_id'] . "'>
                       <button class='action-button' type='submit'><i class='fas fa-cog'></i> Manage</button>
                   </form>
               </td>";
               echo "</tr>";
           }
       } else {
           echo "<tr><td colspan='7' class='no-data'><i class='fas fa-info-circle'></i> No condemned books found in the archive.</td></tr>";
       }
       ?>
   </tbody>
</table>
</div>

<script>
<?php if (isset($_GET['message'])): ?>
   <?php if ($_GET['message'] === 'success'): ?>
       Swal.fire({
           icon: 'success',
           title: 'Success!',
           text: 'The book has been successfully uncondemned!',
       });
   <?php else: ?>
       Swal.fire({
           icon: 'error',
           title: 'Error!',
           text: 'An error occurred while managing the book.',
       });
   <?php endif; ?>
<?php endif; ?>
</script>
</body>
</html>
