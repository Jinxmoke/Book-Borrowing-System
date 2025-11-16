<?php
include('../config/connect.php');
include 'navbar.php';

function fetchNotifications($conn, $limit = null) {
    $sql = "SELECT n.id, n.title AS notification_text, n.time AS timestamp, n.is_unread AS status, u.profile_picture
            FROM notifications n
            JOIN user_info u ON n.member_id = u.member_id
            ORDER BY n.time DESC" . ($limit ? " LIMIT $limit" : "");
    $result = mysqli_query($conn, $sql);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

$initial_notifications = fetchNotifications($conn, 5);
$all_notifications = fetchNotifications($conn);

function timeAgo($timestamp) {
    $time_diff = time() - strtotime($timestamp);
    $intervals = [
        31536000 => 'year', 2592000 => 'month', 604800 => 'week', 86400 => 'day',
        3600 => 'hour', 60 => 'minute', 1 => 'second'
    ];
    foreach ($intervals as $seconds => $label) {
        $interval = floor($time_diff / $seconds);
        if ($interval >= 1) {
            return $interval . ' ' . $label . ($interval > 1 ? 's' : '') . ' ago';
        }
    }
    return "Just now";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: ##f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .admin-container {
            max-width: 600px;
            margin: auto;
            margin-top: 100px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .admin-header {
            background-color: #0A5C3D;
            color: white;
            padding: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-title { font-size: 1.2rem; font-weight: bold; }
        .admin-delete-btn {
            background-color:  #063A26;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            cursor: pointer;
        }
        .admin-notification-list { padding: 0.5rem; }
        .admin-notification-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        .admin-content { flex-grow: 1; }
        .admin-text { margin: 0; font-size: 0.9rem; }
        .admin-time { font-size: 0.8rem; color: #777; }
        .admin-unread {
            width: 8px;
            height: 8px;
            background-color: #FD8418;
            border-radius: 50%;
            margin-left: 0.5rem;
        }
        .admin-see-more {
            display: block;
            width: 100%;
            padding: 0.5rem;
            background-color: #0A5C3D;
            color: white;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }
        .admin-hidden { display: none; }
    </style>
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1 class="admin-title">Notifications</h1>
            <button class="admin-delete-btn">Delete All</button>
        </header>
        <div class="admin-notification-list">
            <?php foreach ($initial_notifications as $notification): ?>
                <div class="admin-notification-item">
                    <img src="../uploads/<?php echo htmlspecialchars($notification['profile_picture']); ?>" alt="User avatar" class="admin-avatar">
                    <div class="admin-content">
                        <p class="admin-text"><?php echo htmlspecialchars($notification['notification_text']); ?></p>
                        <span class="admin-time"><?php echo timeAgo($notification['timestamp']); ?></span>
                    </div>
                    <?php if ($notification['status'] == 1): ?>
                        <div class="admin-unread"></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($all_notifications) > count($initial_notifications)): ?>
            <div id="admin-additional-notifications" class="admin-hidden">
                <?php foreach (array_slice($all_notifications, 5) as $notification): ?>
                    <div class="admin-notification-item">
                        <img src="../uploads/<?php echo htmlspecialchars($notification['profile_picture']); ?>" alt="User avatar" class="admin-avatar">
                        <div class="admin-content">
                            <p class="admin-text"><?php echo htmlspecialchars($notification['notification_text']); ?></p>
                            <span class="admin-time"><?php echo timeAgo($notification['timestamp']); ?></span>
                        </div>
                        <?php if ($notification['status'] == 1): ?>
                            <div class="admin-unread"></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <button id="admin-see-more" class="admin-see-more">See More</button>
        <?php endif; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const seeMoreBtn = document.getElementById('admin-see-more');
            const additionalNotifications = document.getElementById('admin-additional-notifications');
            if (seeMoreBtn) {
                seeMoreBtn.addEventListener('click', function() {
                    if (additionalNotifications.classList.contains('admin-hidden')) {
                        additionalNotifications.classList.remove('admin-hidden');
                        seeMoreBtn.textContent = 'See Less';
                    } else {
                        additionalNotifications.classList.add('admin-hidden');
                        seeMoreBtn.textContent = 'See More';
                    }
                });
            }
        });
    </script>
</body>
</html>
