<?php
include('../config/connect.php');
Include 'navbar.php';

$sql = "SELECT * FROM pending_requests WHERE status = 'pending' ORDER BY request_date DESC";
$result = $conn->query($sql);

$pendingRequests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pendingRequests[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2C5282;
            --secondary-color: #4299E1;
            --background-light: #F7FAFC;
            --text-dark: #2D3748;
            --border-color: #E2E8F0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
            background-color: var(--background-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        .pending-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .pending-page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h2 {
            color: var(--primary-color);
            font-size: 28px;
            font-weight: 700;
        }

        .pending-requests-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            padding: 24px;
            overflow: hidden;
        }

        .filter-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .date-filter-select {
            padding: 10px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: white;
            font-size: 14px;
            color: var(--text-dark);
            transition: all 0.3s ease;
        }

        .date-filter-select:hover {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
        }

        .requests-grid {
            display: grid;
            gap: 15px;
        }

        .request-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
        }

        .request-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-3px);
        }

        .request-details {
            display: grid;
            gap: 8px;
        }

        .request-title {
            font-weight: 600;
            color: var(--primary-color);
        }

        .request-meta {
            color: #718096;
            font-size: 13px;
        }

        .request-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-approve {
            background-color: #48BB78;
            color: white;
        }

        .btn-approve:hover {
            background-color: #38A169;
        }

        .btn-reject {
            background-color: #E53E3E;
            color: white;
        }

        .btn-reject:hover {
            background-color: #C53030;
        }

        .empty-state {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 12px;
            color: #718096;
        }

        @media (max-width: 768px) {
            .pending-container {
                padding: 10px;
            }

            h2 {
                font-size: 22px;
            }

            .request-card {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .request-actions {
                justify-content: center;
            }

            .btn {
                flex-grow: 1;
            }
        }

        /* Loading Animation */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100px;
        }

        .loading-spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="pending-container">
        <div class="pending-page-header">
            <h2>Pending Requests</h2>
        </div>

        <div class="pending-requests-card">
            <div class="filter-container">
                <select class="date-filter-select" id="dateFilter" onchange="filterRequests()">
                    <option value="recent">Most Recent First</option>
                    <option value="old">Oldest First</option>
                </select>
            </div>

            <div id="requestsContainer" class="requests-grid">
                <!-- Requests will be dynamically loaded here -->
            </div>
        </div>
    </div>

    <script>
        let allRequests = [];

        function loadPendingRequests() {
            const container = document.getElementById('requestsContainer');
            container.innerHTML = `
                <div class="loading">
                    <div class="loading-spinner"></div>
                </div>
            `;

            fetch('admin_fetchRequest.php')
                .then(response => response.json())
                .then(data => {
                    allRequests = data;
                    filterRequests();
                })
                .catch(error => {
                    console.error('Error loading pending requests:', error);
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="ri-error-warning-line" style="font-size: 48px; color: #718096; margin-bottom: 15px;"></i>
                            <p>Failed to load requests. Please try again later.</p>
                        </div>
                    `;
                });
        }

        function filterRequests() {
            const filterValue = document.getElementById('dateFilter').value;
            let sortedRequests = [...allRequests];

            sortedRequests.sort((a, b) => {
                const dateA = new Date(a.request_date);
                const dateB = new Date(b.request_date);

                return filterValue === 'recent' ? dateB - dateA : dateA - dateB;
            });

            displayRequests(sortedRequests);
        }

        function displayRequests(requests) {
            const container = document.getElementById('requestsContainer');
            container.innerHTML = '';

            if (requests.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="ri-inbox-line" style="font-size: 48px; color: #718096; margin-bottom: 15px;"></i>
                        <p>No pending requests found</p>
                    </div>
                `;
                return;
            }

            requests.forEach(request => {
                const requestCard = document.createElement('div');
                requestCard.classList.add('request-card');
                requestCard.innerHTML = `
                    <div class="request-details">
                        <div class="request-title">${request.title}</div>
                        <div class="request-meta">
                            <strong>Member:</strong> ${request.member_name}
                            • <strong>Requested:</strong> ${request.request_date}
                        </div>
                    </div>
                    <div class="request-actions">
                        <form method="POST" action="admin_approveRequest.php">
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="id" value="${request.id}">
                            <input type="hidden" name="book_id" value="${request.book_id}">
                            <input type="hidden" name="title" value="${request.title}">
                            <input type="hidden" name="member_id" value="${request.member_id}">
                            <button type="submit" class="btn btn-approve">
                                <i class="ri-check-line"></i>Approve
                            </button>
                        </form>
                        <form action="admin_rejectRequest.php" method="POST">
                            <input type="hidden" name="request_id" value="${request.id}">
                            <button type="submit" name="action" value="reject" class="btn btn-reject">
                                <i class="ri-close-line"></i>Reject
                            </button>
                        </form>
                    </div>
                `;
                container.appendChild(requestCard);
            });
        }

        window.onload = loadPendingRequests;
    </script>
</body>
</html>
