<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caloocan Public Library</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" referrerpolicy="no-referrer" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
     <style>
        @import url('https://fonts.googleapis.com/css2?family=Faculty+Glyphic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap');
        * {

            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Titillium Web;

        }


        .top-bar {
            background: #0B4208;
            color: white;
            padding: 5px 24px;
            text-align: right;
            transition: transform 0.3s ease;
            position: relative;
            z-index: 1000;
            font-family: Titillium Web;
        }

        .top-bar.hidden {
            transform: translateY(-100%);
        }

        .top-bar a {
            color: white;
            text-decoration: none;
            margin-left: 24px;
            font-size: 14px;
            font-family: Titillium Web;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 24px;
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
            font-size: 24px;
            font-weight: bold;
            color: #0B5E2B;
            text-decoration: none;
            font-family: Titillium Web;
        }


        .nav-links {
            display: flex;
            margin-right: 500px;
            gap: 40px;
            font-size: 17px;
            margin-right: 100px;
            transition: all 0.3s ease-in-out;
            font-family: Titillium Web;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            position: relative; /* Needed for positioning the pseudo-element */
        }

        .nav-links a:before {
            content: '';
            width: 0;
            height: 3px; /* Slightly smaller height for a cleaner look */
            background: #115C29;
            position: absolute;
            top: 100%; /* Position the line directly under the text */
            left: 0;
            transform: translateY(30px); /* Adjusts the distance below the text */
            transition: width 0.3s ease; /* Fast transition */
            border-radius: 100px
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

        @media (max-width: 1280px) {
          .nav-links {
              display: flex;
              margin-right: 320px;
              gap: 40px;
              margin-left: auto;
              transition: all 0.3s ease-in-out;
          }

            .navbar {
                position: relative; /* Ensure proper positioning context */
            }

            .nav-links.active {
                display: flex;
            }

            .nav-icons {
                gap: 15px;
                margin-right: 0;
            }


            .top-bar {
                display: none;
            }

            .search-box {
                width: 250px;
                right: -100px;
            }

            .user-dropdown {
                right: 0;
                min-width: 200px;
            }

            }



        @media (max-width: 768px) {
          .mobile-menu-btn {
              display: block;
              margin-left: -30px;
              z-index: 1001; /* Ensure it's above the nav-links */
          }

            .nav-links {
                display: none;
                position: absolute; /* Changed from fixed to absolute */
                top: 100%; /* Position it right below the navbar */
                left: 0;
                right: 0;
                margin-left: 30px;
                background: white;
                flex-direction: column;
                padding: 20px;
                gap: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                margin: 0;
                z-index: 1000; /* Ensure it's above other content */
            }
            .navbar {
                position: relative; /* Ensure proper positioning context */
            }

            .nav-links.active {
                display: flex;
            }

            .nav-icons {
                gap: 15px;
                margin-right: 0;
            }


            .top-bar {
                display: none;
            }

            .search-box {
                width: 250px;
                right: -100px;
            }

            .user-dropdown {
                right: 0;
                min-width: 200px;
            }
            }

            @media (max-width: 480px) {
            .navbar {
                padding: 12px 16px;
            }

            .logos {
                width: 40px;
            }

            .logo {
                font-size: 20px;
            }

            .nav-icons {
                gap: 30px;
            }

            .nav-icons button {
                font-size: 18px;
            }

            .search-box {
                width: 200px;
                right: -50px;
            }
            }

        .logos {
            width: 50px;
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

      .notification-badge {
          position: absolute;
          top: 22px;
          right: 43px;
          width: 20px;
          height: 20px;
          background-color: red;
          color: white;
          font-size: 12px;
          border-radius: 50%;
          display: inline-block;
          text-align: center;
          line-height: 20px;
      }

      .notification-dropdown {
                  display: none;
                  position: absolute;
                  top: 60px;
                  right: 20px;
                  width: 320px;
                  background-color: #ffffff;
                  border-radius: 8px;
                  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                  z-index: 1000;
                  overflow: hidden;
                  transition: all 0.3s ease;
              }

              .notification-dropdown.active {
                  display: block;
                  animation: fadeIn 0.3s ease;
              }

              @keyframes fadeIn {
                  from { opacity: 0; transform: translateY(-10px); }
                  to { opacity: 1; transform: translateY(0); }
              }

              .notification-header {
                  display: flex;
                  justify-content: space-between;
                  align-items: center;
                  padding: 15px 20px;
                  background-color: #0A5C3D;
                  color: #ffffff;
              }

              .notification-header h4 {
                  margin: 0;
                  font-size: 18px;
                  font-weight: 600;
              }

              #clearNotifications {
                  background: none;
                  border: none;
                  color: #ffffff;
                  cursor: pointer;
                  font-size: 14px;
                  opacity: 0.8;
                  transition: opacity 0.2s;
              }

              #clearNotifications:hover {
                  opacity: 1;
              }

              .notification-list {
                  max-height: 300px;
                  overflow-y: auto;
              }

              .notification-item {
                  padding: 15px 20px;
                  border-bottom: 1px solid #eeeeee;
                  transition: background-color 0.2s;
              }

              .notification-item:hover {
                  background-color: #f9f9f9;
              }

              .notification-item:last-child {
                  border-bottom: none;
              }

              .notification-content {
                  display: flex;
                  align-items: flex-start;
              }

              .notification-icon {
                  margin-right: 15px;
                  font-size: 20px;
                  color: #FD8418;
              }

              .notification-text {
                  flex: 1;
              }

              .notification-title {
                  font-weight: 600;
                  margin-bottom: 5px;
              }

              .notification-message {
                  font-size: 14px;
                  color: #666666;
                  margin: 0;
              }

              .notification-time {
                  font-size: 12px;
                  color: #999999;
                  margin-top: 5px;
              }

              .notification-footer {
                  padding: 15px 20px;
                  text-align: center;
                  border-top: 1px solid #eeeeee;
              }

              .notification-footer a {
                  color: #0A5C3D;
                  text-decoration: none;
                  font-weight: 500;
                  transition: color 0.2s;
              }

              .notification-footer a:hover {
                  color: #FD8418;
              }

              .notification-list::-webkit-scrollbar {
                  width: 6px;
              }

              .notification-list::-webkit-scrollbar-track {
                  background: #f1f1f1;
              }

              .notification-list::-webkit-scrollbar-thumb {
                  background: #888888;
                  border-radius: 3px;
              }

              .notification-list::-webkit-scrollbar-thumb:hover {
                  background: #555555;
              }

              .logo {
                  font-size: 17px;
                  font-weight: bold;
                  color: #0B5E2B;
                  text-decoration: none;
              }

              .logo span {
                  display: block;
                  font-size: 13px;
                  margin-top: -5px;
                  font-weight: 500;
                  color: #666666;
              }
              </style>
          </head>
          <body>
            <div class="top-bar" id="topBar">
              <a href="/e-book/Book-Borrowing-System.apk"  class="app">Download App</a>
              <a href="#">|</a>
              <a class="open-modal-btn">How It Works</a>
              <a href="#">|</a>
              <a class="modal-trigger" id="openLibraryModal">Location</a>
            </div>

              <nav class="navbar" id="navbar">
                  <div class="logo-container">
                      <img class="logos" src="../uploads/logo.png" alt="">
                      <a href="../index.php" class="logo">Book Borrowing
                          <span >Caloocan Public Library</span>
                      </a>
                  </div>

                  <div class="nav-links" id="navLinks">
                      <a href="../index.php" class="navbar-link">Home</a>
                      <a href="../index.php#testimonial" class="navbar-link">Testimonials</a>
                      <a href="guest_catalog.php" class="navbar-link">Catalog</a>
                      <a href="user_genres.php" class="navbar-link">Categories</a>
                      <a href="../login_form.php" class="navbar-link">Join Now</a>
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
                  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                  const navLinks = document.getElementById('navLinks');

                  // Toggle mobile menu
                  mobileMenuBtn.addEventListener('click', () => {
                      navLinks.classList.toggle('active');
                  });

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
          </body>
          </html>
