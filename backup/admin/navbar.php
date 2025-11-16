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
    <title>Book Borrowing System</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" referrerpolicy="no-referrer" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Faculty+Glyphic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
</head>
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
      padding: 15px 24px;
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
      font-size: 20px;
      font-weight: bold;
      color: #0B5E2B;
      text-decoration: none;
  }

  .logo span {
      display: block;
      font-size: 12px;
      margin-top: -10px;
      font-weight: 500;
      color: #666666;
  }

  .nav-links {
      display: flex;
      margin-right: 50px;
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
      color: #115C29;
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

  @media (max-width: 1024px) {
      .nav-links {
          margin-right: 20px;
          gap: 20px;
      }

      .nav-icons {
          gap: 20px;
          margin-right: 20px;
      }
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
    right: 0;
    width: 300px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

.mark-as-read {
    background-color: red;
    color: black;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    margin-left: -10px;
    font-size: 100px;
}
#notificationBtn {
    position: relative;
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
}

.notification-badge {
    position: absolute;
    top: 0;
    right: 0;
    background-color: #ff4444;
    color: white;
    border-radius: 50%;
    min-width: 20px;
    height: 20px;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    transform: translate(25%, -25%);
    padding: 2px;
}
.notification-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    margin-top: 10px;
    left: 1200px;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 8px;
    width: 300px;
    max-height: 400px;
    z-index: 1000;
}

.notification-dropdown.active {
    display: block;
}

.notification-header {
    padding: 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    border-top-left-radius: 10px;  /* Top-left corner */
    border-top-right-radius: 10px;
    justify-content: space-between;
    align-items: center;
    background-color: #0A5C3D;
}

.notification-header h4 {
    margin: 0;
    color: #fff;
}

#clearNotifications {
    background: none;
    border: none;
    color: #fff;
    cursor: pointer;
    font-size: 0.9em;
}

.notification-list {
    max-height: 300px;
    overflow-y: auto;
}

.notification-item {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.3s;
}

.notification-item:hover {
    background-color: #f5f5f5;
}

.notification-item.unread {
    background-color: #f0f7ff;
}

.notification-item .notification-title {
    font-weight: bold;
    margin-bottom: 5px;
}

.notification-item .notification-time {
    font-size: 0.8em;
    color: #666;
}

.notification-footer {
    padding: 10px 15px;
    text-align: center;
    border-top: 1px solid #eee;
}

.notification-footer a {
    color: #007bff;
    text-decoration: none;
}

@media (max-width: 768px) {
  .mobile-menu-btn {
    display: block;
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #0B5E2B;
    order: 3; /* Ensure it comes right before nav-icons */
  }

  .nav-icons {
    display: flex;
    order: 4; /* Keep nav-icons at the rightmost position */
  }

  .nav-links {
    display: none; /* Hidden by default */
    position: absolute;
    top: 100%; /* Position below the navbar */
    left: 0;
    right: -50px;
    background: white;
    flex-direction: column;
    padding: 20px;
    gap: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 1000;
  }


  .nav-links.active {
    display: flex; /* Show on hamburger click */
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
  .nav-icons {
      gap: 25px;
      margin-right: 5px;
  }
  .notification-dropdown {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 90%; /* Adjust width for mobile */
      max-width: 350px;
      max-height: 80vh;
      z-index: 2000; /* High z-index to ensure it's above other elements */
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  }

  .notification-dropdown.active {
      display: block;
      position: fixed;
      top: 34%;
      left: 58%;
      transform: translate(-50%, -50%);
  }

  .notification-list {
      overflow-y: auto;
  }
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-toggle {
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}


.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background-color: #ffffff;
    min-width: 200px;
    margin-top: 10px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    animation: fadeIn 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.1);
    z-index: 1;
}

.dropdown.active .dropdown-menu {
    display: block;
}

.dropdown-menu a {
    color: #333;
    padding: 12px 20px;
    text-decoration: none;
    display: block;
    transition:
        background-color 0.2s ease,
        color 0.2s ease;
}

.dropdown-menu a:hover {
    background-color: #3a5a78;
    color: white;
}

@media (max-width: 768px) {
    .dropdown-menu {
        position: static;
        width: 100%;
        box-shadow: none;
        border: none;
        margin-top: 0;
        background-color: rgba(58, 90, 120, 0.05);
        min-width: auto;
    }

    .dropdown-menu a {
        padding: 15px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .dropdown-menu a:last-child {
        border-bottom: none;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
<body>
    <div class="top-bar" id="topBar">
    </div>

    <nav class="navbar" id="navbar">
        <div class="logo-container">
            <img class="logos" src="logo.png" alt="">
            <a href="homepage.php" class="logo">Book Borrowing
                <span >Caloocan Public Library</span>
            </a>
        </div>



    <div class="nav-links" id="navLinks">
      <!-- Catalog dropdown for desktop -->
      <div class="dropdown desktop-dropdown">
          <a href="#" class="navbar-link dropdown-toggle">Catalog</a>
          <div class="dropdown-menu">
              <a href="admin_catalogForm.php" class="dropdown-item">View Catalog</a>
              <a href="admin_condemnedBooks.php" class="dropdown-item">Condemn Books</a>
          </div>
      </div>

      <!-- Mobile specific links (initially hidden) -->
      <div class="mobile-links" style="display: none;">
          <a href="admin_catalogForm.php" class="navbar-link mobile-catalog">View Catalog</a><br>
          <a href="admin_condemnedBooks.php" class="navbar-link mobile-condemned">Condemn Books</a>
      </div>
      <!-- Additional navigation links -->
      <a href="admin_borrowedForm.php" class="navbar-link">Overdue Books</a>
      <a href="admin_returnedBooksForm.php" class="navbar-link">Returned Books</a>
      <a href="admin_manageMembersForm.php" class="navbar-link">Manage Members</a>
      <a href="admin_pendingRequest.php" class="navbar-link">Book Request</a>
  </div>

        <div class="nav-icons">
            <div class="search-container">
                <button id="searchBtn"></button>
                <div class="search-box" id="searchBox">
                  <form id="searchForm" action="ldr_searchBook.php" method="get" class="search-form">
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
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Notification Icon -->
            <button id="notificationBtn">
                <i class="fa-regular fa-bell"></i>
                <span id="notificationBadge" class="notification-badge" style="display:none;">0</span>
            </button>

            <!-- Notification Dropdown -->
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header">
                    <h4>Notifications</h4>
                    <button id="clearNotifications">Clear All</button>
                </div>
                <div class="notification-list" id="notificationList">
                    <!-- Notifications will be dynamically inserted here -->
                </div>
                <div class="notification-footer">
                    <a href="admin_allNotification.php">View All</a>
                </div>
            </div>
            <button id="userMenuBtn"><i class="fa-regular fa-user"></i></button>
            <div class="user-dropdown" id="userDropdown">
              <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>
    
        <script>
    function adjustNavigation() {
        const desktopDropdown = document.querySelector('.desktop-dropdown');
        const mobileLinks = document.querySelector('.mobile-links');
        const navLinks = document.getElementById('navLinks');

        if (window.innerWidth <= 768) {
            // Mobile view
            if (desktopDropdown) {
                desktopDropdown.style.display = 'none';
            }
            mobileLinks.style.display = 'block';
        } else {
            // Desktop view
            if (desktopDropdown) {
                desktopDropdown.style.display = 'block';
            }
            mobileLinks.style.display = 'none';
        }
    }

    // Initial call
    adjustNavigation();

    // Add event listener for window resize
    window.addEventListener('resize', adjustNavigation);
</script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // User Menu Dropdown
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

        // Navbar Scroll Behavior
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

        // Mobile Menu
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navLinks = document.getElementById('navLinks');

        // Toggle mobile menu
        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
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

        // Notifications
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationList = document.getElementById('notificationList');
        const notificationBadge = document.getElementById('notificationBadge');
        let unreadCount = 0;

        // Pusher Configuration with Enhanced Logging
        const pusher = new Pusher('d634f1649c151fec12e6', {
            cluster: 'eu',
            forceTLS: true,
            encrypted: true,
            disableStats: true,
            autoReconnect: true,
            channelAuthorization: {
                endpoint: 'pusher_auth.php'
            }
        });

        // Detailed Pusher Connection Logging
        pusher.connection.bind('state_change', function(states) {
            console.log('Pusher connection state:', states.current);
        });

        pusher.connection.bind('error', function(err) {
            console.error('Pusher Connection Error:', err);
        });

        function loadNotifications() {
            fetch('admin_getNotifications.php')
                .then(response => response.json())
                .then(data => {
                    // Clear existing notifications
                    notificationList.innerHTML = '';

                    // Reset unread count
                    unreadCount = 0;

                    // Process each notification
                    data.forEach(notification => {
                        const notificationElement = createNotificationElement(
                            notification.title,
                            notification.time,
                            notification.is_unread === '1',
                            notification.id
                        );
                        notificationList.appendChild(notificationElement);

                        // Increment unread count for unread notifications
                        if (notification.is_unread === '1') {
                            unreadCount++;
                        }
                    });

                    // Update badge with the actual count
                    updateBadge();
                })
                .catch(error => console.error('Error loading notifications:', error));
        }

        function updateBadge() {
            if (unreadCount > 0) {
                notificationBadge.textContent = unreadCount;
                notificationBadge.style.display = 'flex';
            } else {
                notificationBadge.style.display = 'none';
            }
        }

        function createNotificationElement(title, time, isUnread, id) {
            const notificationItem = document.createElement('div');
            notificationItem.className = 'notification-item unread';
            notificationItem.setAttribute('data-id', id);

            notificationItem.innerHTML = `
                <div class="notification-title">${title}</div>
                <div class="notification-time">${time}</div>
                <button class="mark-as-read">Mark as Read</button>
            `;

            notificationItem.querySelector('.mark-as-read').addEventListener('click', () => {
                markAsRead(id);
            });

            return notificationItem;
        }

        function markAsRead(notificationId) {
            fetch('admin_markasread.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    notification_id: notificationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const notificationElement = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                    if (notificationElement) {
                        notificationElement.classList.remove('unread');
                        notificationElement.classList.add('read');
                        notificationElement.querySelector('.mark-as-read')?.remove();
                        updateUnreadCount(-1);
                    }
                } else {
                    console.error('Failed to mark notification as read');
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }

        function updateUnreadCount(change) {
            unreadCount += change;
            notificationBadge.textContent = unreadCount;
            notificationBadge.style.display = unreadCount > 0 ? 'flex' : 'none';
        }

        // Subscribe to channels with enhanced error handling
        function subscribeToChannels() {
            try {
                const adminChannel = pusher.subscribe('admin-channel');
                const userChannel = pusher.subscribe('user-channel');

                adminChannel.bind('book-borrowed', function(data) {
                    console.log('Book Borrowed Event Received:', data);
                    handleNotification(data, 'borrowed');
                });

                adminChannel.bind('book-returned', function(data) {
                    console.log('Book Returned Event Received:', data);
                    handleNotification(data, 'returned');
                });

                userChannel.bind('new-notification', function(data) {
                    console.log('User Notification Received:', data);
                    // Handle user-specific notifications if needed
                });

                adminChannel.bind('pusher:subscription_succeeded', function() {
                    console.log('Successfully subscribed to admin-channel');
                    loadNotifications();
                });

                adminChannel.bind('pusher:subscription_error', function(err) {
                    console.error('Admin Channel Subscription Error:', err);
                });
            } catch (error) {
                console.error('Channel Subscription Error:', error);
            }
        }

        function handleNotification(data, action) {
            const time = new Date().toLocaleString();
            const title = action === 'borrowed'
                ? `${data.user_name} request to borrow "${data.book_title}"`
                : `Book returned: "${data.book_title}" by ${data.member_id}`;

            const notificationElement = createNotificationElement(
                title,
                time,
                true,
                data.notification_id || Date.now()
            );

            notificationList.insertBefore(notificationElement, notificationList.firstChild);
            updateUnreadCount(1);

            fetch('admin_storeNotification.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    title: title,
                    time: time,
                    member_id: data.member_id,
                    notification_id: data.notification_id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'success') {
                    console.error('Failed to save notification to database');
                }
            })
            .catch(error => console.error('Error saving notification to database:', error));
        }

        // Notification Dropdown Toggle
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('active');
            }
        });

        // Clear Notifications
        function clearNotifications() {
            fetch('clear_notifications.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    notificationList.innerHTML = '';
                    unreadCount = 0;
                    updateBadge();
                } else {
                    console.error('Failed to clear notifications');
                }
            })
            .catch(error => console.error('Error clearing notifications:', error));
        }

        // Initialize Pusher and Subscribe to Channels
        pusher.connection.bind('connected', function() {
            console.log('Pusher connected successfully');
            subscribeToChannels();
        });

        // Initial load of notifications
        loadNotifications();

        // Dropdown functionality
        document.querySelectorAll('.dropdown-toggle').forEach(dropdownToggle => {
            dropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                this.parentNode.classList.toggle('active');

                // Close other dropdowns
                document.querySelectorAll('.dropdown').forEach(dropdown => {
                    if (dropdown !== this.parentNode) {
                        dropdown.classList.remove('active');
                    }
                });
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown').forEach(dropdown => {
                    dropdown.classList.remove('active');
                });
            }
        });

        // Dropdown functionality for mobile
        document.querySelectorAll('.dropdown-toggle').forEach(dropdownToggle => {
            dropdownToggle.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    this.parentNode.classList.toggle('active');
                }
            });
        });
    });
    </script>



</body>
</html>
