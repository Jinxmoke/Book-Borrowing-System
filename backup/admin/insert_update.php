<!-- Modal for Adding a Book -->
<div id="addBookModal" class="lender-modal">
    <div class="lender-modal-content">
        <span class="lender-close">&times;</span>
        <h2>Add a New Book</h2>
        <form id="addBookForm" action="admin_insertBook.php" method="post" enctype="multipart/form-data">
            <div class="lender-form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="lender-form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" required>
            </div>
            <div class="lender-form-group">
                <label for="genre">Subject</label>
                <input type="text" id="genre" name="genre" required>
            </div>
            <div class="lender-form-group">
                <label for="isbn">ISBN</label>
                <input type="text" id="isbn" name="isbn" required>
            </div>
            <div class="lender-form-group">
                <label for="book_type">Book Type</label>
                <select id="book_type" name="book_type" required onchange="togglePdfUpload()">
                    <option value="physical">Physical Book</option>
                    <option value="ebook">E-Book</option>
                </select>
            </div>
            <div class="lender-form-group">
                <label for="publicationDate">Publication Date</label>
                <input type="date" id="publicationDate" name="publication_date" required>
            </div>
            <div class="lender-form-group">
                <label for="publisher">Publisher</label>
                <input type="text" id="publisher" name="publisher" required>
            </div>
            <div class="lender-form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>
            <div class="lender-form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="lender-form-group">
                <label for="image">Book Cover</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>
            <div class="lender-form-group" id="pdf_upload" style="display: none;">
                <label for="pdf">E-Book (PDF)</label>
                <input type="file" id="pdf" name="pdf" accept=".pdf">
            </div>
            <div class="lender-form-group">
                <label for="expiry_days">Expiry</label>
                <select id="expiry_days" name="expiry_days" required>
                    <?php foreach (getExpiryOptions() as $days => $label): ?>
                        <option value="<?= $days ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="lender-submit-button" name="add_books">Add Book</button>
        </form>
    </div>
</div>

<!-- Update Book Modal -->
<div id="updateBookModal" class="lender-modal">
    <div class="lender-modal-content">
        <span class="lender-close">&times;</span>
        <h2>Update Book</h2>
        <form id="addBookForm" action="admin_processUpdate.php" method="post" enctype="multipart/form-data">
            <input type="hidden" id="updateBookId" name="book_id">
            <div class="lender-form-group">
                <label for="updateTitle">Title</label>
                <input type="text" id="updateTitle" name="title" required>
            </div>
            <div class="lender-form-group">
                <label for="updateAuthor">Author</label>
                <input type="text" id="updateAuthor" name="author" required>
            </div>
            <div class="lender-form-group">
                <label for="updateGenre">Subject</label>
                <input type="text" id="updateGenre" name="genre" required>
            </div>
            <div class="lender-form-group">
                <label for="updateIsbn">ISBN</label>
                <input type="text" id="updateIsbn" name="isbn" required>
            </div>
            <div class="lender-form-group">
                <label for="updateBookType">Book Type</label>
                <select id="updateBookType" name="book_type" required onchange="toggleUpdatePdfUpload()">
                    <option value="physical">Physical Book</option>
                    <option value="ebook">E-Book</option>
                </select>
            </div>
            <div class="lender-form-group">
                <label for="updatePublicationDate">Publication Date</label>
                <input type="date" id="updatePublicationDate" name="publication_date" required>
            </div>
            <div class="lender-form-group">
                <label for="updatePublisher">Publisher</label>
                <input type="text" id="updatePublisher" name="publisher" required>
            </div>
            <div class="lender-form-group">
                <label for="updateStatus">Status</label>
                <select id="updateStatus" name="status" required>
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>
            <div class="lender-form-group">
                <label for="updateDescription">Description</label>
                <textarea id="updateDescription" name="description" required></textarea>
            </div>
            <div class="lender-form-group" id="updatePdfUpload" style="display: none;">
                <label for="updatePdf">E-Book (PDF)</label>
                <input type="file" id="updatePdf" name="pdf" accept=".pdf">
            </div>
            <div class="lender-form-group">
                <label for="updateExpiryDays">Expiry</label>
                <select id="updateExpiryDays" name="expiry_days" required>
                    <?php foreach (getExpiryOptions() as $days => $label): ?>
                        <option value="<?= $days ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="lender-submit-button">Update Book</button>
        </form>
    </div>
</div>
