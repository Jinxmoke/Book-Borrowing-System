<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .notifications-panel {
            background: white;
            width: 400px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .notifications-header {
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e4e6eb;
        }

        .notifications-title {
            font-size: 18px;
            font-weight: bold;
        }

        .notifications-tabs {
            display: flex;
            padding: 8px 16px;
            gap: 8px;
            border-bottom: 1px solid #e4e6eb;
        }

        .tab {
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 18px;
            font-size: 14px;
        }

        .tab.active {
            background-color: #e7f3ff;
            color: #1b74e4;
        }

        .earlier-section {
            padding: 8px 16px;
            color: #65676b;
            font-size: 14px;
            font-weight: 600;
        }

        .notifications-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f2f5;
        }

        .notification-item:hover {
            background-color: #f0f2f5;
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e4e6eb;
        }

        .notification-content {
            flex: 1;
        }

        .notification-text {
            font-size: 14px;
            margin-bottom: 4px;
        }

        .notification-time {
            color: #65676b;
            font-size: 12px;
        }

        .unread-dot {
            width: 8px;
            height: 8px;
            background-color: #1b74e4;
            border-radius: 50%;
            margin-left: 4px;
        }

        .see-more {
            padding: 10px;
            text-align: center;
            background-color: #f0f2f5;
            color: #1b74e4;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            user-select: none;
        }

        .hidden {
            display: none;
        }

        .settings-button {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .settings-button:hover {
            background-color: #f0f2f5;
        }
    </style>
</head>
<body>
    <div class="notifications-panel">
        <div class="notifications-header">
            <h1 class="notifications-title">Notifications</h1>
            <div class="settings-button">
                <svg viewBox="0 0 24 24" width="20" height="20">
                    <circle cx="12" cy="12" r="2" fill="#65676b"/>
                    <circle cx="3" cy="12" r="2" fill="#65676b"/>
                    <circle cx="21" cy="12" r="2" fill="#65676b"/>
                </svg>
            </div>
        </div>

        <div class="notifications-tabs">
            <div class="tab active">All</div>
            <div class="tab">Unread</div>
        </div>

        <div class="earlier-section">Earlier</div>

        <div class="notifications-list">
            <!-- Current notifications -->
            <div class="notification-item">
                <div class="profile-pic"></div>
                <div class="notification-content">
                    <div class="notification-text">
                        <strong>Kidd Andrei N. Torres</strong>, <strong>Vince Baena</strong> and <strong>Bernadeth Llorca</strong> reacted to a photo you shared.
                    </div>
                    <div class="notification-time">21h</div>
                </div>
                <div class="unread-dot"></div>
            </div>

            <div class="notification-item">
                <div class="profile-pic"></div>
                <div class="notification-content">
                    <div class="notification-text">
                        <strong>Juan Miguel</strong> and <strong>Kidd Andrei N. Torres</strong> reacted to a photo you shared.
                    </div>
                    <div class="notification-time">21h</div>
                </div>
            </div>
        </div>

        <div class="see-more" id="seeMoreBtn">
            See previous notifications
        </div>

        <div class="notifications-list hidden" id="previousNotifications">
            <!-- Previous notifications -->
            <div class="notification-item">
                <div class="profile-pic"></div>
                <div class="notification-content">
                    <div class="notification-text">
                        <strong>RJ Mercado</strong> mentioned you in a comment in <strong>TIPID PC(Computer Parts Buy & Sell)</strong>.
                    </div>
                    <div class="notification-time">5d</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const seeMoreBtn = document.getElementById('seeMoreBtn');
        const previousNotifications = document.getElementById('previousNotifications');

        seeMoreBtn.addEventListener('click', () => {
            const isHidden = previousNotifications.classList.toggle('hidden');
            seeMoreBtn.textContent = isHidden ? 'See previous notifications' : 'Hide previous notifications';
        });

        // Tab switching functionality
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
            });
        });

        // Notification item click handling
        const notifications = document.querySelectorAll('.notification-item');
        notifications.forEach(notification => {
            notification.addEventListener('click', () => {
                const unreadDot = notification.querySelector('.unread-dot');
                if (unreadDot) {
                    unreadDot.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
