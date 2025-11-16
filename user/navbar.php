<?php
session_start();
include('../config/connect.php');
require '../pusher/vendor/autoload.php';

if (isset($_SESSION['name']) && isset($_SESSION['member_id'])) {
    $name = $_SESSION['name'];
    $member_id = $_SESSION['member_id'];
} else {
    header("Location: /e-book/login_form");
    exit;
}

// Initialize Pusher
$options = array(
    'cluster' => 'eu',
    'useTLS' => true
);
$pusher = new Pusher\Pusher(
    'd634f1649c151fec12e6',
    'ebe82ec59b8b84901f8c',
    '1897855',
    $options
);

// Get current date
$current_date = date('Y-m-d');

// 1. Check for overdue books
$check_overdue_sql = "
    SELECT bb.*,
           b.book_type,
           DATEDIFF(CURRENT_DATE, bb.due_date) as days_overdue
    FROM borrowed_books bb
    JOIN manage_books b ON bb.book_id = b.book_id
    WHERE bb.member_id = ?
    AND bb.due_date < CURRENT_DATE
    AND bb.status = 'borrowed'
    AND b.book_type = 'physical'  -- Filter only physical books
    AND NOT EXISTS (
        SELECT 1
        FROM user_notification un
        WHERE un.book_id = bb.book_id
        AND un.member_id = bb.member_id
        AND un.type = 'overdue'
        AND DATE(un.created_at) = CURRENT_DATE
    )";

$stmt = $conn->prepare($check_overdue_sql);
if ($stmt === false) {
    die('Error preparing SQL statement: ' . $conn->error);
}
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

while ($overdue_book = $result->fetch_assoc()) {
    $days_overdue = $overdue_book['days_overdue'];
    $book_title = $overdue_book['title'];

    // Check if notification already exists (to prevent duplication)
    $check_existing_notification_sql = "
        SELECT COUNT(*) FROM user_notification
        WHERE book_id = ? AND member_id = ? AND type = 'overdue' AND DATE(created_at) = ?";
    $check_stmt = $conn->prepare($check_existing_notification_sql);
    $check_stmt->bind_param("iis", $overdue_book['book_id'], $member_id, $current_date);
    $check_stmt->execute();
    $check_stmt->bind_result($notification_exists);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($notification_exists == 0) {
        $message = [
            'member_id' => $member_id,
            'book_id' => $overdue_book['book_id'],
            'message' => "Your borrowed physical book '$book_title' is overdue by $days_overdue days! Please return it as soon as possible.",
            'type' => 'overdue',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Insert notification
        $insert_sql = "INSERT INTO user_notification (member_id, book_id, message, type, created_at)
                    VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        if ($insert_stmt === false) {
            die('Error preparing insert SQL statement: ' . $conn->error);
        }
        $insert_stmt->bind_param("iisss",
            $message['member_id'],
            $message['book_id'],
            $message['message'],
            $message['type'],
            $message['created_at']
        );
        $insert_stmt->execute();
        $insert_stmt->close();

        // Trigger real-time notification
        $pusher->trigger('user-channel', 'notify-user', $message);
    }
}
$stmt->close();

// 2. Check for available bookmarked books
$check_bookmarks_sql = "
    SELECT b.book_id, b.title, bm.member_id
    FROM bookmarks bm
    JOIN manage_books b ON bm.book_id = b.book_id
    WHERE bm.member_id = ?
    AND b.status = 'available'
    AND NOT EXISTS (
        SELECT 1
        FROM user_notification un
        WHERE un.book_id = b.book_id
        AND un.member_id = bm.member_id
        AND un.type = 'bookmark_available'
        AND DATE(un.created_at) = CURRENT_DATE
    )";

$bookmark_stmt = $conn->prepare($check_bookmarks_sql);
if ($bookmark_stmt === false) {
    die('Error preparing bookmark SQL statement: ' . $conn->error);
}
$bookmark_stmt->bind_param("i", $member_id);
$bookmark_stmt->execute();
$bookmark_result = $bookmark_stmt->get_result();

while ($available_book = $bookmark_result->fetch_assoc()) {
    $book_title = $available_book['title'];

    // Check if notification already exists for the bookmarked book (to prevent duplication)
    $check_existing_notification_sql = "
        SELECT COUNT(*) FROM user_notification
        WHERE book_id = ? AND member_id = ? AND type = 'bookmark_available' AND DATE(created_at) = ?";
    $check_stmt = $conn->prepare($check_existing_notification_sql);
    $check_stmt->bind_param("iis", $available_book['book_id'], $member_id, $current_date);
    $check_stmt->execute();
    $check_stmt->bind_result($notification_exists);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($notification_exists == 0) {
        $message = [
            'member_id' => $member_id,
            'book_id' => $available_book['book_id'],
            'message' => "Your bookmarked book '$book_title' is now available for borrowing.",
            'type' => 'bookmark_available',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Insert notification
        $insert_sql = "INSERT INTO user_notification (member_id, book_id, message, type, created_at)
                    VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        if ($insert_stmt === false) {
            die('Error preparing insert bookmark SQL statement: ' . $conn->error);
        }
        $insert_stmt->bind_param("iisss",
            $message['member_id'],
            $message['book_id'],
            $message['message'],
            $message['type'],
            $message['created_at']
        );
        $insert_stmt->execute();
        $insert_stmt->close();

        // Trigger real-time notification
        $pusher->trigger('user-channel', 'notify-user', $message);
    }
}
$bookmark_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caloocan Public Library</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" referrerpolicy="no-referrer" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Faculty+Glyphic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Titillium Web', sans-serif;
        }
        
        .top-bar {
            background: #0B4208;
            color: white;
            padding: 8px 24px;
            text-align: right;
            transition: transform 0.3s ease;
            position: relative;
            z-index: 1000;
        }
        
        .top-bar.hidden {
            transform: translateY(-100%);
        }
        
        .top-bar a {
            color: white;
            text-decoration: none;
            margin-left: 24px;
            font-size: 14px;
            cursor: pointer;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 999;
            transition: top 0.3s ease;
        }
        
        .navbar.top-hidden {
            top: 0;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo {
            font-size: 19px;
            font-weight: bold;
            color: #0B5E2B;
            text-decoration: none;
        }
        
        .logo span {
            display: block;
            font-size: 12px;
            margin-top: -5px;
            font-weight: 500;
            color: #666666;
        }
        
        .nav-links {
            display: flex;
            gap: 40px;
            transition: all 0.3s ease-in-out;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            position: relative;
        }
        
        .nav-links a:hover {
            color: #115C29;
        }
        
        .nav-links a:hover:before {
            width: 100%;
        }
        
        .nav-icons {
            display: flex;
            align-items: center;
            gap: 50px;
            margin-right: 50px;
        }
        
        .nav-icons button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #0B5E2B;
        }
        
        .logos {
            width: 45px;
            height: auto;
        }
        
        .user-dropdown {
            position: absolute;
            top: calc(100% + 0.75rem);
            right: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            min-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px) scale(0.98);
            transition: all 0.3s ease;
        }
        
        .user-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }
        
        .user-dropdown a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1rem;
            color: #333;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .user-dropdown a:hover {
            background-color: #f1f5f9;
            color: #0B5E2B;
        }
        
        .user-dropdown a i {
            font-size: 1.1rem;
            color: #666;
            transition: all 0.3s ease;
        }
        
        .user-dropdown a:hover i {
            color: #0B5E2B;
        }
        
        .search-container {
            position: relative;
            display: inline-block;
        }
        
        .search-box {
            position: absolute;
            top: 100%;
            right: -50px;
            width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            display: none;
            z-index: 1000;
        }
        
        .search-box.active {
            display: block;
        }
        
        .search-input {
            width: 270px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .search-results {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .search-result-item {
            padding: 8px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .search-result-item:hover {
            background: #f5f5f5;
        }
        
        .search-result-item img {
            width: 40px;
            height: 60px;
            object-fit: cover;
        }
        
        .search-result-info {
            flex: 1;
        }
        
        .search-result-info h4 {
            margin: 0;
            font-size: 14px;
            color: #333;
        }
        
        .search-result-info p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }
        
        #submitSearchBtn {
            display: inline-block;
            margin-top: 10px;
            width: 100%;
            padding: 8px 0;
            border: none;
            background-color: #0B5E2B;
            color: white;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        #submitSearchBtn:hover {
            background-color: #115C29;
        }
        
        .nav-links .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 8px 0;
            z-index: 1000;
            transform: translateY(10px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: all 0.2s ease;
        }
        
        .dropdown-content a:hover {
            background-color: #f8f9fa;
            color: #115C29;
        }
        
        .nav-links .dropdown > a:after {
            content: '\f107';
            font-family: 'FontAwesome';
            margin-left: 5px;
            display: inline-block;
            transition: transform 0.3s ease;
        }
        
        .nav-links .dropdown:hover > a:after {
            transform: rotate(180deg);
        }
        
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
                margin-left: -30px;
                z-index: 1001;
            }
        
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                margin-left: 30px;
                background: white;
                flex-direction: column;
                padding: 20px;
                gap: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                margin: 0;
                z-index: 1000;
            }
        
            .navbar {
                position: relative;
            }
        
            .nav-links.active {
                display: flex;
            }
        
            .nav-icons {
                gap: 20px;
                margin-right: 20px;
            }
        
            .logo {
                font-size: 15px;
            }
        
            .logo span {
                font-size: 11px;
            }
        
            .search-box {
                width: 300px;
                right: -100px;
            }
        
            .user-dropdown {
                right: 0;
                min-width: 200px;
            }
        
            .dropdown-content {
                position: static;
                box-shadow: none;
                padding-left: 20px;
                min-width: 100%;
                display: none;
                opacity: 1;
                visibility: visible;
                transform: none;
            }
        
            .dropdown.active .dropdown-content {
                display: block;
            }
        
            .nav-links .dropdown > a:after {
                float: right;
                margin-top: 4px;
            }
        }
        
                    
            .notification-container {
              position: relative;
              display: inline-block;
            }
            
            .notification-badge {
              position: absolute;
              top: -8px;
              right: -11px;
              background-color: #e53e3e;
              color: white;
              border-radius: 50%;
              padding: 2px 6px;
              font-size: 12px;
              min-width: 23px;
              text-align: center;
            }
            
            .notification-dropdown {
              position: absolute;
              top: calc(100% + 10px);
              right: 0;
              width: 320px;
              background-color: white;
              border-radius: 8px;
              box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
              opacity: 0;
              visibility: hidden;
              transform: translateY(-10px);
              transition: all 0.3s ease;
              z-index: 1000;
            }
            
            .notification-dropdown.active {
              opacity: 1;
              visibility: visible;
              transform: translateY(0);
            }
            
            .notification-header {
              display: flex;
              justify-content: space-between;
              align-items: center;
              padding: 16px;
              border-bottom: 1px solid #e2e8f0;
              background-color: #0A5C3D;
              border-top-left-radius: 10px;
              border-top-right-radius: 10px;
            }
            
            .notification-header h4 {
              margin: 0;
              color: #fff;
              font-size: 16px;
              font-weight: 600;
            }
            
            #clearNotifications {
              background: none;
              border: none;
              color: #fff;
              font-size: 14px;
              cursor: pointer;
              padding: 4px 8px;
              border-radius: 4px;
              transition: background-color 0.2s;
            }
            
            #clearNotifications:hover {
              color: black;
              background-color: #fff;
            }
            
            .notification-list {
              max-height: 300px;
              overflow-y: auto;
              padding: 8px 0;
            }
            
            .notification-item {
              padding: 12px 16px;
              border-bottom: 1px solid #f0f0f0;
              transition: background-color 0.2s;
            }
            
            .notification-item:hover {
              background-color: #f8f9fa;
            }
            
            .notification-item strong {
              display: block;
              color: red;
              margin-bottom: 4px;
              font-size: 15px;
            }
            
            .notification-item p {
              color: #718096;
              margin: 0;
              font-size: 14px;
            }
            
            .notification-footer {
              padding: 12px 16px;
              border-top: 1px solid #e2e8f0;
              text-align: center;
            }
            
            .notification-footer a {
              color: #0B5E2B;
              text-decoration: none;
              font-size: 14px;
              font-weight: 500;
            }
            
            .notification-footer a:hover {
              text-decoration: underline;
            }
            
            @media (max-width: 768px) {
              .notification-dropdown {
                  width: 280px;
                  right: -100px;
              }
            }

        </style>
</head>
<body>
<?php include 'user_howitworks.php' ?>
<?php include 'user_location.php' ?>
<div class="top-bar" id="topBar">
    <a class="open-modal-btn">How It Works</a>
    <a href="#">|</a>
    <a class="modal-trigger" id="openLibraryModal">View Library Location</a>
</div>

<nav class="navbar" id="navbar">
    <div class="logo-container">
        <img class="logos" src="logo.png" alt="">
        <a href="user_catalogForm" class="logo">Book Borrowing
            <span>Caloocan Public Library</span>
        </a>
    </div>

    <div class="nav-links" id="navLinks">
        <div class="dropdown">
            <a href="#" class="navbar-link">Catalog</a>
            <div class="dropdown-content">
                <a href="user_catalogForm">All Books</a>
                <a href="user_popularBooks">Popular Books</a>
            </div>
        </div>
        <a href="user_genres" class="navbar-link">Categories</a>
        <a href="user_myborrowedForm" class="navbar-link">My Borrowed Books</a>
        <a href="user_historyForm" class="navbar-link">History</a>
        <a href="user_bookmarkForm" class="navbar-link">Bookmark</a>
    </div>

    <div class="nav-icons">
        <!-- Search icon -->
        <div class="search-container">
            <button id="searchBtn"><i class="fa-solid fa-magnifying-glass"></i></button>
            <div class="search-box" id="searchBox">
                <form id="searchForm" action="user_searchBook" method="get" class="search-form">
                    <div class="search-container">
                        <input type="text" id="searchInput" name="searchInput" class="search-input" placeholder="Search books...">
                    </div>
                </form>
                <div class="search-results" id="searchResults"></div>
            </div>
        </div>

        <!-- Notification icon -->
        <div class="notification-container">
            <button id="notificationBtn">
                <i class="fa-regular fa-bell"></i>
                <span id="notificationBadge" class="notification-badge" style="display:none;">0</span>
            </button>
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header">
                    <h4>Notifications</h4>
                    <button id="clearNotifications">Mark As Read</button>
                </div>
                <div class="notification-list" id="notificationList">
                    <!-- Notifications will be dynamically inserted here -->
                </div>
                <div class="notification-footer">
                    <a href="">Close</a>
                </div>
            </div>
        </div>

        <!-- User profile icon -->
        <button id="userMenuBtn"><i class="fa-regular fa-user"></i></button>
        <div class="user-dropdown" id="userDropdown">
            <a href="user_editProfile"><i class="fas fa-user"></i> Profile</a>
            <a href="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars"></i>
    </button>
</nav>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchBtn = document.getElementById('searchBtn');
    const searchBox = document.getElementById('searchBox');
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const searchForm = document.getElementById('searchForm');

    // Toggle the search box visibility
    searchBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        searchBox.classList.toggle('active');
        if (searchBox.classList.contains('active')) {
            searchInput.focus();
        }
    });

    // Close search when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchBox.contains(e.target) && !searchBtn.contains(e.target)) {
            searchBox.classList.remove('active');
        }
    });

    // Live Search Functionality
    function performSearch(searchTerm) {
        console.log('Searching for:', searchTerm); // Debugging
        fetch(`user_searchBook?searchInput=${encodeURIComponent(searchTerm)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data); // Debugging
            searchResults.innerHTML = '';

            if (data.length === 0) {
                searchResults.innerHTML = '<p>No results found</p>';
                return;
            }

            data.forEach(book => {
                const resultItem = document.createElement('div');
                resultItem.className = 'search-result-item';
                resultItem.innerHTML = `
                    <img src="../uploads/${book.cover_image}" alt="Book cover">
                    <div class="search-result-info">
                        <h4>${book.title}</h4>
                        <p>${book.author}</p>
                    </div>
                `;
                resultItem.addEventListener('click', () => {
                    // Ensure the correct book_id is used for the redirect
                    const bookId = book.book_id;
                    if (bookId) {
                        window.location.href = `user_descriptionForm?book_id=${bookId}`;
                    } else {
                        console.error('Book ID not found');
                    }
                });

                searchResults.appendChild(resultItem);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            searchResults.innerHTML = '<p></p>';
        });
    }

    // Handle search input dynamically
    let searchTimeout;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const searchTerm = e.target.value;

        searchTimeout = setTimeout(() => {
            if (searchTerm.length >= 2) {
                performSearch(searchTerm);
            } else {
                searchResults.innerHTML = '';
            }
        }, 300);
    });

    // Handle form submission (including Enter key)
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission to handle the search manually
        const searchTerm = searchInput.value;
        if (searchTerm.trim()) {
            // Redirect the user to the search results page
            window.location.href = `user_searchBook?searchInput=${encodeURIComponent(searchTerm)}`;
        }
    });

    // User menu functionality
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    userMenuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.remove('active');
        }
    });

    // Scroll functionality
    let lastScroll = 0;
    const topBar = document.getElementById('topBar');
    const navbar = document.getElementById('navbar');

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > lastScroll && currentScroll > 50) {
            // Scrolling down
            topBar.classList.add('hidden');
            navbar.classList.add('top-hidden');
        } else {
            // Scrolling up
            topBar.classList.remove('hidden');
            navbar.classList.remove('top-hidden');
        }

        lastScroll = currentScroll;
    });
});
</script>
    
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navLinks = document.getElementById('navLinks');

        // Toggle mobile menu
        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent event from bubbling up
            navLinks.classList.toggle('active');
            mobileMenuBtn.innerHTML = navLinks.classList.contains('active')
                ? '<i class="fas fa-times"></i>'
                : '<i class="fas fa-bars"></i>';
        });

        // Close mobile menu when clicking a link
        document.querySelectorAll('.navbar-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    navLinks.classList.remove('active');
                    mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
                }
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!navLinks.contains(e.target) &&
                !mobileMenuBtn.contains(e.target) &&
                navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });

        // Close mobile menu when window is resized above mobile breakpoint
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                navLinks.classList.remove('active');
                mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });



        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationList = document.getElementById('notificationList');

        // Toggle the dropdown
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('active');
        });

        document.addEventListener('click', () => {
            notificationDropdown.classList.remove('active');
        });
        });

  //--------------------------USER PUSHER------------------------------------------//
  // Initialize Pusher
  const pusher = new Pusher('d634f1649c151fec12e6', {
      cluster: 'eu',
      encrypted: true
  });

  // Subscribe to the channel
  const channel = pusher.subscribe('user-channel');

  // DOM elements
  const notificationBadge = document.getElementById('notificationBadge');
  const notificationList = document.getElementById('notificationList');
  const clearNotificationsBtn = document.getElementById('clearNotifications');

  // Keep track of notifications
  let notifications = [];

  // Load existing notifications when page loads
  document.addEventListener('DOMContentLoaded', () => {
      loadExistingNotifications();
  });

  // Handle incoming notifications
  channel.bind('notify-user', function(data) {
      // Add notification to array
      notifications.unshift(data);
      // Update UI
      updateNotificationUI();
      // Show badge
      updateNotificationBadge();
  });

  // Function to load existing notifications from database
  function loadExistingNotifications() {
      fetch('get_notifications.php')
          .then(response => response.json())
          .then(data => {
              notifications = data;
              updateNotificationUI();
              updateNotificationBadge();
          })
          .catch(error => console.error('Error loading notifications:', error));
  }

  // Function to update notification UI
  function updateNotificationUI() {
      notificationList.innerHTML = '';

      if (notifications.length === 0) {
          notificationList.innerHTML = '<div class="notification-item"><p>No notifications</p></div>';
          return;
      }

      notifications.forEach((notification, index) => {
          const notificationEl = document.createElement('div');
          notificationEl.className = 'notification-item';

          const date = new Date(notification.created_at);
          const formattedDate = date.toLocaleString();

          notificationEl.innerHTML = `
              <strong>${notification.type}</strong>
              <p>${notification.message}</p>
              <small>${formattedDate}</small>
          `;
          notificationList.appendChild(notificationEl);
      });
  }

  // Function to update notification badge
  function updateNotificationBadge() {
      if (notifications.length > 0) {
          notificationBadge.style.display = 'block';
          notificationBadge.textContent = notifications.length;
      } else {
          notificationBadge.style.display = 'none';
      }
  }

  // Clear notifications
  clearNotificationsBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      fetch('clear_notifications.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
          }
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              notifications = [];
              updateNotificationUI();
              updateNotificationBadge();
          }
      })
      .catch(error => console.error('Error clearing notifications:', error));
  });
    </script>


</body>
</html>
