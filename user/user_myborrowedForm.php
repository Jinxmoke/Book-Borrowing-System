<?php
include('../config/connect.php');
include 'navbar.php';

// Function to hash file names
function hashFileName($fileName) {
    return hash('sha256', $fileName . $_SESSION['member_id']);
}

// Default book type is 'all'
$book_type = isset($_GET['book_type']) ? $_GET['book_type'] : 'all';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';</script>
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #ffffff;
            --accent: #f3f4f6;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --danger: #ef4444;
            --success: #22c55e;
            --radius: 12px;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .library-title {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
            color: var(--text-primary);
        }

        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 8rem;
            padding: 1rem 0;
        }

        .book-card {
            background: var(--secondary);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
            height: 100px;
            width: 475px;
            min-height: 290px;
        }

        .book-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .book-card {
            background: var(--secondary);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            display: flex;
            flex-direction: row;
            margin-bottom: 1.5rem;
            height: auto;
            min-height: 280px;
        }

        .book-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .book-cover {
            position: relative;
            width: 200px;
            min-width: 200px;
            background: var(--accent);
            overflow: hidden; /* Ensure hover overlay doesn't overflow */
        }

        .book-cover img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .book-info {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100px;
        }

        .book-info h3 {
            margin: 0;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .book-metadata {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .metadata-item {
            display: flex;
            flex-direction: column;
        }

        .metadata-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .metadata-value {
            font-size: 0.9375rem;
            color: var(--text-primary);
        }

        .description {
            color: var(--text-secondary);
            font-size: 0.9375rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-height: 4.5em;
            line-height: 1.5;
        }

        .book-actions {
            display: flex;
            gap: 1rem;
            margin-top: auto;
        }

        .action-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .delete-btn {
            background: var(--danger);
            color: white;
        }

        .delete-btn:hover {
            background: #dc2626;
        }

        .update-btn {
            background: #0891b2;
            color: white;
        }

        .update-btn:hover {
            background: #0e7490;
        }

        @media (max-width: 768px) {
            .book-card {
                flex-direction: column;
            }

            .book-cover {
                width: 100%;
                height: 200px;
                min-width: unset;
            }

            .book-metadata {
                grid-template-columns: 1fr;
            }
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            background: var(--secondary);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--text-secondary);
        }

        .empty-state h2 {
            margin: 0;
            color: var(--text-primary);
        }

        .empty-state p {
            color: var(--text-secondary);
            margin: 0.5rem 0 0;
        }

        .modal4 {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal-content {
            position: relative;
            width: 90%;
            max-width: 1000px;
            height: 90vh;
            margin: 2.5vh auto;
            background: var(--secondary);
            border-radius: var(--radius);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        #pdf-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        #close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        #close-modal:hover {
            color: var(--text-primary);
        }

        #pdf-container {
            flex: 1;
            margin: auto;
            overflow: auto;
            background: var(--accent);
            border-radius: var(--radius);
            margin-bottom: 1rem;
        }

        .pdf-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: var(--accent);
            border-radius: var(--radius);
        }

        .control-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--radius);
            background: #02A95C;
            color: white;
            cursor: pointer;
            transition: var(--transition);
        }

        .control-btn:hover {
            background: #017741;
        }

        .page-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        #current-page {
            width: 4rem;
            padding: 0.5rem;
            border: 1px solid var(--text-secondary);
            border-radius: var(--radius);
            text-align: center;
        }

        #zoom {
            padding: 0.5rem;
            border: 1px solid var(--text-secondary);
            border-radius: var(--radius);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .library-title {
                font-size: 2rem;
            }

            .book-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .modal-content {
                width: 95%;
                height: 95vh;
                padding: 1rem;
                margin: 2.5vh auto;
            }

            .pdf-controls {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .control-btn {
                padding: 0.5rem;
            }

            #current-page {
                width: 3rem;
            }
        }

        @media (max-width: 480px) {
            .library-title {
                font-size: 1.75rem;
            }

            .book-info h3 {
                font-size: 1.125rem;
            }

            .pdf-controls {
                padding: 0.75rem;
            }
        }
        .book-cover:hover .hover-info {
            opacity: 1;
        }
        .hover-info {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
            cursor: pointer;
        }

        .hover-text {
            color: white;
            font-size: 1.25rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hover-icon {
            font-size: 2.5rem;
            color: white;
            opacity: 0.9;
        }
        
                @media (max-width: 768px) {
            body {
                touch-action: manipulation; /* Prevent double-tap zoom */
                -webkit-text-size-adjust: 100%; /* Prevent font scaling */
            }

            .container {
                padding: 0 0.5rem;
                margin: 1rem auto;
            }

            .book-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .book-card {
                flex-direction: column;
                min-height: 400px; /* Adjusted for mobile */
                width: 100%;
            }

            .book-cover {
                width: 100%;
                height: 250px;
                min-width: 100%;
            }

            .book-info {
                padding: 1rem;
            }

            .book-metadata {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }

            .modal-content {
                width: 95%;
                height: 95vh;
                margin: 2.5vh auto;
                padding: 0.5rem;
            }

            .pdf-controls {
                flex-wrap: wrap;
                gap: 0.5rem;
                padding: 0.5rem;
            }

            .control-btn {
                flex: 1;
                padding: 0.75rem;
                font-size: 0.9rem;
            }

            #current-page, #zoom {
                width: 100%;
                padding: 0.75rem;
                font-size: 1rem;
            }
        }


        @media (max-width: 480px) {
            .book-card {
                min-height: 450px;
            }

            .book-cover {
                height: 300px;
            }

            .book-metadata {
                grid-template-columns: 1fr;
            }

            .metadata-item {
                margin-bottom: 0.5rem;
            }
        }

        select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            color: #333;
            background: white;
            cursor: pointer;
            min-width: 140px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23333%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.4-12.8z%22%2F%3E%3C%2Fsvg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 8px auto;
        }

        select:hover {
            border-color: #999;
        }

        select:focus {
            outline: none;
            border-color: #666;
            box-shadow: 0 0 3px rgba(0,0,0,0.1);
        }

        /* Style for the options */
        select option {
            padding: 8px;
            background: white;
            color: #333;
        }
    </style>
</head>
<body>
  <div class="container">
         <h1 class="library-title">My Library</h1>
         <form method="GET" action="">
            <select name="book_type" id="book-type-filter" onchange="this.form.submit()">
                <option value="all" <?php echo ($book_type == 'all') ? 'selected' : ''; ?>>All Books</option>
                <option value="ebook" <?php echo ($book_type == 'ebook') ? 'selected' : ''; ?>>eBooks</option>
                <option value="physical" <?php echo ($book_type == 'physical') ? 'selected' : ''; ?>>Physical Books</option>
            </select>
        </form>
         <div id="book-grid" class="book-grid">
           <?php
           $member_id = $_SESSION['member_id'];

           // Adjust the query to filter based on selected book type
           if ($book_type == 'all') {
               $sql = "SELECT b.book_id, m.title, m.author, b.status, m.image, m.pdf, m.book_type, b.borrow_date, b.due_date, m.genre, m.publisher, m.description
                       FROM borrowed_books AS b
                       LEFT JOIN manage_books AS m ON b.book_id = m.book_id
                       WHERE b.member_id = ? AND b.status = 'borrowed'";
           } else {
               $sql = "SELECT b.book_id, m.title, m.author, b.status, m.image, m.pdf, m.book_type, b.borrow_date, b.due_date, m.genre, m.publisher, m.description
                       FROM borrowed_books AS b
                       LEFT JOIN manage_books AS m ON b.book_id = m.book_id
                       WHERE b.member_id = ? AND m.book_type = ? AND b.status = 'borrowed'";
           }

           $stmt = $conn->prepare($sql);

           if ($book_type == 'all') {
               $stmt->bind_param("i", $member_id);
           } else {
               $stmt->bind_param("is", $member_id, $book_type);
           }

           $stmt->execute();
           $result = $stmt->get_result();

           if ($result && $result->num_rows > 0) {
               while ($row = $result->fetch_assoc()) {
                   $imagePath = $row["image"] ? '../uploads/' . $row["image"] : 'default.jpg';
                   $pdfPath = $row["pdf"] ? hashFileName($row["pdf"]) : '#';
                   $bookType = htmlspecialchars($row["book_type"]);
                   $onclick = ($bookType === "physical") ? '' : "onclick='openEncryptedPDF(\"$pdfPath\")'";

                   echo "<div class='book-card' data-aos='fade-up'>
                           <div class='book-cover' " . ($bookType === "ebook" ? "onclick='openEncryptedPDF(\"$pdfPath\")'" : "") . ">
                               <img src='$imagePath' alt='Book cover of " . htmlspecialchars($row["title"]) . "'>
                               " . ($bookType === "ebook" ? "
                               <div class='hover-info'>
                                   <span class='hover-text'>Read Now</span>
                               </div>" : "") . "
                           </div>
                           <div class='book-info'>
                               <h3>" . htmlspecialchars($row["title"]) . "</h3>
                               <div class='book-metadata'>
                                   <div class='metadata-item'>
                                       <span class='metadata-label'>Genre</span>
                                       <span class='metadata-value'>" . htmlspecialchars($row["genre"]) . "</span>
                                   </div>
                                   <div class='metadata-item'>
                                       <span class='metadata-label'>Status</span>
                                       <span class='metadata-value'>" . htmlspecialchars($row["status"]) . "</span>
                                   </div>
                                   <div class='metadata-item'>
                                       <span class='metadata-label'>Book Type</span>
                                       <span class='metadata-value'>" . htmlspecialchars($row["book_type"]) . "</span>
                                   </div>
                                   <div class='metadata-item'>
                                       <span class='metadata-label'>Expiry</span>
                                       <span class='metadata-value'>" . $row["due_date"] . "</span>
                                   </div>
                               </div>
                               <div class='description'>" . htmlspecialchars($row["description"]) . "</div>
                           </div>
                         </div>";
               }
           } else {
                 echo "<div class='empty-state'>
                         <i class='fas fa-book empty-icon'></i>
                         <h2>Your library is empty</h2>
                         <p>Start borrowing books to see them here</p>
                       </div>";
           }
           $stmt->close();
           $conn->close();
           ?>
         </div>
     </div>

     <!-- PDF MODAL -->
     <div id="pdf-modal" class="modal4">
         <div class="modal-content">
             <div class="modal-header">
                 <h2 id="pdf-title">Reading Book</h2>
                 <button id="close-modal" aria-label="Close modal"><i class="fas fa-times"></i></button>
             </div>
             <div id="pdf-container"></div>
             <div class="pdf-controls">
                 <button id="prev-page" class="control-btn" aria-label="Previous page"><i class="fas fa-chevron-left"></i> Previous</button>
                 <div class="page-info">
                     <input type="number" id="current-page" min="1" value="1" aria-label="Current page">
                     <span>of</span>
                     <span id="total-pages">0</span>
                 </div>
                 <button id="next-page" class="control-btn" aria-label="Next page">Next <i class="fas fa-chevron-right"></i></button>
                 <select id="zoom" aria-label="Zoom level">
                     <option value="0.5">50%</option>
                     <option value="0.75">75%</option>
                     <option value="1" selected>100%</option>
                     <option value="1.25">125%</option>
                     <option value="1.5">150%</option>
                     <option value="2">200%</option>
                 </select>
             </div>
         </div>
     </div>

     <script>
         // PDF viewer functionality
         let pdfDoc = null;
         let pageNum = 1;
         let pageRendering = false;
         let pageNumPending = null;
         let scale = 1;

         async function openEncryptedPDF(hashedPdfPath) {
             const modal = document.getElementById('pdf-modal');
             const container = document.getElementById('pdf-container');
             modal.style.display = 'block';

             try {
                 // Fetch the real file path from the server
                 const response = await fetch('get_pdf_path.php', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/x-www-form-urlencoded',
                     },
                     body: 'hashedPath=' + encodeURIComponent(hashedPdfPath)
                 });
                 const data = await response.json();

                 if (data.error) {
                     throw new Error(data.error);
                 }

                 const loadingTask = pdfjsLib.getDocument(data.path);
                 pdfDoc = await loadingTask.promise;
                 document.getElementById('total-pages').textContent = pdfDoc.numPages;
                 renderPage(pageNum);
             } catch (error) {
                 console.error('Error loading PDF:', error);
                 container.innerHTML = '<p class="error-message">Error loading PDF. Please try again later.</p>';
             }
         }

         function renderPage(num) {
             if (pageRendering) {
                 pageNumPending = num;
                 return;
             }

             pageRendering = true;
             const container = document.getElementById('pdf-container');
             const canvas = document.createElement('canvas');
             container.innerHTML = '';
             container.appendChild(canvas);

             pdfDoc.getPage(num).then(function(page) {
                 const viewport = page.getViewport({ scale: scale });
                 canvas.height = viewport.height;
                 canvas.width = viewport.width;

                 const renderContext = {
                     canvasContext: canvas.getContext('2d'),
                     viewport: viewport
                 };

                 page.render(renderContext).promise.then(function() {
                     pageRendering = false;
                     if (pageNumPending !== null) {
                         renderPage(pageNumPending);
                         pageNumPending = null;
                     }
                 });

                 document.getElementById('current-page').value = num;
             });
         }

         // Event Listeners
         document.getElementById('prev-page').addEventListener('click', () => {
             if (pageNum <= 1) return;
             pageNum--;
             renderPage(pageNum);
         });

         document.getElementById('next-page').addEventListener('click', () => {
             if (pageNum >= pdfDoc.numPages) return;
             pageNum++;
             renderPage(pageNum);
         });

         document.getElementById('current-page').addEventListener('change', function() {
             const newPage = parseInt(this.value);
             if (newPage > 0 && newPage <= pdfDoc.numPages) {
                 pageNum = newPage;
                 renderPage(pageNum);
             }
         });

         document.getElementById('zoom').addEventListener('change', function() {
             scale = parseFloat(this.value);
             renderPage(pageNum);
         });

         document.getElementById('close-modal').addEventListener('click', function() {
             document.getElementById('pdf-modal').style.display = 'none';
             pdfDoc = null;
             pageNum = 1;
             scale = 1;
             document.getElementById('zoom').value = "1";
         });

         // Add smooth scrolling and animations
         document.addEventListener('DOMContentLoaded', function() {
             const cards = document.querySelectorAll('.book-card');

             const observer = new IntersectionObserver((entries) => {
                 entries.forEach(entry => {
                     if (entry.isIntersecting) {
                         entry.target.style.opacity = '1';
                         entry.target.style.transform = 'translateY(0)';
                     }
                 });
             }, {
                 threshold: 0.1
             });

             cards.forEach(card => {
                 card.style.opacity = '0';
                 card.style.transform = 'translateY(20px)';
                 card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                 observer.observe(card);
             });
         });

         // Handle touch events for mobile
         let touchStartX = 0;
         let touchEndX = 0;

         document.getElementById('pdf-container').addEventListener('touchstart', e => {
             touchStartX = e.changedTouches[0].screenX;
         });

         document.getElementById('pdf-container').addEventListener('touchend', e => {
             touchEndX = e.changedTouches[0].screenX;
             handleSwipe();
         });

         function handleSwipe() {
             const swipeThreshold = 50;
             const diff = touchEndX - touchStartX;

             if (Math.abs(diff) > swipeThreshold) {
                 if (diff > 0 && pageNum > 1) {
                     // Swipe right - previous page
                     pageNum--;
                     renderPage(pageNum);
                 } else if (diff < 0 && pageNum < pdfDoc.numPages) {
                     // Swipe left - next page
                     pageNum++;
                     renderPage(pageNum);
                 }
             }
         }
         
              document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    alert('Right-clicking is disabled on this page.');
    return false;
});
     </script>

 </body>
 </html>
