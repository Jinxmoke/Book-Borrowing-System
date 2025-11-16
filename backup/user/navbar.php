<?php
session_start();


if (isset($_SESSION['name']) && isset($_SESSION['member_id'])) {
    $name = $_SESSION['name'];
    $member_id = $_SESSION['member_id'];
} else {

    header("Location: /e-book/login_form.php");
    exit;
}
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
            font-family: Titillium Web;
        }

        body {

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
            margin-right: 500px;
            gap: 40px;
            margin-left: auto;
            transition: all 0.3s ease-in-out;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            position: relative; /* Needed for positioning the pseudo-element */
        }

        .nav-links a:hover {
            color: #115C29; /* New color on hover (adjust as needed) */
        }

        .nav-links a:hover:before {
            width: 100%; /* Expands to 50% width on hover */
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
            background: var(--surface-color);
            border-radius: var(--border-radius);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            padding: 0.5rem;
            min-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px) scale(0.98);
            transition: var(--transition);
        }

        .user-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
            background-color: White;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        .user-dropdown a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1rem;
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
            font-weight: 500;
        }

        .user-dropdown a:hover {
            background-color: #f1f5f9;
            color: var(--primary-color);
        }

        .user-dropdown a i {
            font-size: 1.1rem;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .user-dropdown a:hover i {
            color: var(--primary-color);
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

      @media (max-width: 768px) {
      .mobile-menu-btn {
          display: block;
          margin-left: -30px;
          z-index: 1001;
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
          gap: 30px;
          margin-right: 0;
      }

      .logo {
          font-size: 15px;
          font-weight: bold;
          color: #0B5E2B;
          text-decoration: none;
      }

      .logo span {
          display: block;
          font-size: 11px;
          margin-top: -5px;
          font-weight: 500;
          color: #666666;
      }

      .search-box {
          width: 300px;
          right: -100px;
      }

      .user-dropdown {
          right: 0;
          min-width: 200px;
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
      <a class="modal-trigger" id="openLibraryModal">Location</a>
    </div>

    <nav class="navbar" id="navbar">
        <div class="logo-container">
            <img class="logos" src="logo.png" alt="">
            <a href="user_catalogForm.php" class="logo">Book Borrowing
                <span >Caloocan Public Library</span>
            </a>
        </div>

        <div class="nav-links" id="navLinks">
            <a href="user_catalogForm.php" class="navbar-link">Catalog</a>
            <a href="user_genres.php" class="navbar-link">Categories</a>
            <a href="user_myborrowedForm.php" class="navbar-link">My Borrowed Books</a>
            <a href="user_historyForm.php" class="navbar-link">History</a>
            <a href="user_bookmarkForm.php" class="navbar-link">Bookmark</a>
        </div>

        <div class="nav-icons">
            <div class="search-container">
                <button id="searchBtn"><i class="fa-solid fa-magnifying-glass"></i></button>
                <div class="search-box" id="searchBox">
                  <form id="searchForm" action="user_searchBook.php" method="get" class="search-form">
                      <div class="search-container">
                          <input type="text"
                                 id="searchInput"
                                 name="searchInput"
                                 class="search-input"
                                 placeholder="Search books...">

                      </div>
                  </form>
                  <div class="search-results" id="searchResults"></div>
              </div>
            </div>

            <button id="userMenuBtn"><i class="fa-regular fa-user"></i></button>
            <div class="user-dropdown" id="userDropdown">
              <a href="user_editProfile.php"><i class="fas fa-user"></i> Profile</a>
              <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
            fetch(`user_searchBook.php?searchInput=${encodeURIComponent(searchTerm)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const searchResults = document.getElementById('searchResults');
                searchResults.innerHTML = '';

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

                    searchResults.appendChild(resultItem);
                });
            })
            .catch(error => console.error('Error:', error));
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

        // Handle form submission
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchTerm = searchInput.value;
            if (searchTerm.trim()) {
                window.location.href = `user_searchBook.php?searchInput=${encodeURIComponent(searchTerm)}`;
            }
        });
    });

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

        //--------------------------USER PUSHER------------------------------------------//

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

        const pusher = new Pusher('d634f1649c151fec12e6', {
            cluster: 'eu',
            encrypted: true
        });

        // Get the member_id from the data attribute on the body tag
        const memberId = document.body.getAttribute('data-member-id');

        if (memberId) {
            const channel = pusher.subscribe(`user-${memberId}`);

            channel.bind('pusher:subscription_succeeded', function() {
                console.log(`Successfully subscribed to user-${memberId} channel`);
            });

            channel.bind('pusher:subscription_error', function(error) {
                console.error('Subscription error:', error);
            });

            channel.bind('new-notification', function(data) {
                const notificationItem = document.createElement('div');
                notificationItem.className = 'notification-item';
                notificationItem.innerHTML = `
                    <strong>${data.message}</strong>
                    <p>${data.timestamp}</p>
                `;
                notificationList.appendChild(notificationItem);

                // Store the notification in localStorage
                let notifications = JSON.parse(localStorage.getItem('notifications')) || [];
                notifications.push(data);
                localStorage.setItem('notifications', JSON.stringify(notifications));

                // Update notification badge
                updateNotificationBadge();
            });
        } else {
            console.error('Member ID not found');
        }

        // Clear notifications
        const clearNotificationsBtn = document.getElementById('clearNotifications');
        clearNotificationsBtn.addEventListener('click', () => {
            notificationList.innerHTML = '';
            localStorage.removeItem('notifications');
            updateNotificationBadge();
        });

        // Update notification badge
        function updateNotificationBadge() {
            const badge = document.getElementById('notificationBadge');
            const notifications = JSON.parse(localStorage.getItem('notifications')) || [];
            const unreadCount = notifications.length;

            if (unreadCount > 0) {
                badge.style.display = 'inline';
                badge.textContent = unreadCount;
            } else {
                badge.style.display = 'none';
            }
        }

        // Load notifications from localStorage on page load
        const savedNotifications = JSON.parse(localStorage.getItem('notifications')) || [];
        savedNotifications.forEach((notification) => {
            const notificationItem = document.createElement('div');
            notificationItem.className = 'notification-item';
            notificationItem.innerHTML = `
                <strong>${notification.message}</strong>
                <p>${notification.timestamp}</p>
            `;
            notificationList.appendChild(notificationItem);
        });

        // Initial badge update
        updateNotificationBadge();
    });
    </script>

</body>
</html>
