<?php
include_once '../Controllers/UserController.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = null; // or handle it appropriately if the user is not logged in
}

$isAuthenticated = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;

if ($isAuthenticated) {
    // Fetch notification count and notifications
    $notificationsCount = UserController::getUnreadCount($_SESSION['user_id']);
    $UnreadNotifications = UserController::getUnread($_SESSION['user_id']);

    // Fetch user data
    $UserData = UserController::FetchByUserID1($_SESSION['user_id']);

    // Check if user data is valid before using it
    if ($UserData !== null) {
        $UserImage = htmlspecialchars($UserData->getImageUrl(), ENT_QUOTES, 'UTF-8');
    } else {
        // Handle case where user data is not found
        $UserImage = 'path/to/default/image.png'; // Use a default image if user data is not found
    }
} else {
    // Handle case where user is not authenticated
    $UserImage = 'path/to/default/image.png'; // Use a default image when user is not authenticated
}
function renderNotificationItem($notification) {
    // Ensure proper handling of associative array
    $notification_id = htmlspecialchars($notification['notification_id']);
    $sender_user_id = htmlspecialchars($notification['sender_user_id']);
    $message = htmlspecialchars($notification['message']);
    $link = htmlspecialchars($notification['link']);
    $created_at = htmlspecialchars($notification['created_at']);
    $is_read = (bool) $notification['is_read'];

    // Fetch sender user data
    $UserSenderData = UserController::FetchByUserID1($sender_user_id);
    $senderName = htmlspecialchars($UserSenderData->getFirstName() . ' ' . $UserSenderData->getLastName());
    $senderImage = htmlspecialchars($UserSenderData->getImageUrl());

    $timeAgo = timeAgo($created_at);
    $isReadClass = $is_read ? 'read' : 'unread';

    return <<<HTML
<div class="notification-item {$isReadClass}">
    <li class="dropdown-item">
        <a href="profile.php?id={$sender_user_id}">
            <img src="{$senderImage}" alt="User Image" class="user-image">
        </a>
        <div class="notification-content">
            <div class="notification-header">
                <strong class="user-name">{$senderName}</strong>
                <small class="notification-time">{$timeAgo}</small>
            </div>
            <p class="notification-message mb-0">{$message}</p>
            <a href="Layout/mark_as_read.php?notification_id={$notification_id}">Mark as Read</a>
        </div>
    </li>
</div>
HTML;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    .navbar {
        background-color: #ffffff;
        border-bottom: 1px solid #e9ecef;
    }

    .navbar-brand {
        font-weight: bold;
    }

    .navbar-nav .nav-link {
        color: #495057;
    }

    .navbar-nav .nav-link:hover {
        color: #007bff;
    }

    /* Dropdown Styles */
    .dropdown-menu.notifications {
        max-height: 500px;
        overflow-y: auto;
        padding: 0;
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        width: 350px;
        background-color: #ffffff;
    }

    .notification-item {
        display: flex;
        align-items: flex-start;
        padding: 12px;
        border-bottom: 1px solid #e9ecef;
        background-color: #ffffff;
        transition: background-color 0.3s ease;
    }

    .notification-item.unread {
        background-color: #f0f4ff;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item:hover {
        background-color: #f1f3f5;
    }

    .user-image {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover;
    }

    .notification-content {
        flex-grow: 1;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }

    .user-name {
        font-size: 1em;
        color: #495057;
    }

    .notification-message {
        font-size: 0.95em;
        color: #495057;
    }

    .notification-time {
        font-size: 0.75em;
        color: #868e96;
        text-align: right;
    }

    .mark-as-read {
        font-size: 0.75em;
        color: #007bff;
        margin-top: 5px;
        display: inline-block;
        text-decoration: none;
    }

    .mark-as-read:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    /* Navbar Toggle */
    .navbar-toggler {
        border-color: #007bff;
    }

    .navbar-toggler-icon {
        background-image: url('data:image/svg+xml,...');
    }

    /* Dark Mode */
    body.dark-mode {
        background-color: #343a40;
        color: #e9ecef;
    }

    body.dark-mode .navbar {
        background-color: #495057;
        border-bottom: 1px solid #343a40;
    }

    body.dark-mode .navbar-nav .nav-link {
        color: #e9ecef;
    }

    body.dark-mode .navbar-nav .nav-link:hover {
        color: #ffffff;
    }

    body.dark-mode .notification-item {
        background-color: #495057;
    }

    body.dark-mode .notification-item.unread {
        background-color: #6c757d;
    }

    body.dark-mode .notification-message {
        color: #e9ecef;
    }

    body.dark-mode .notification-time {
        color: #ced4da;
    }

    body.dark-mode .mark-as-read {
        color: #e9ecef;
    }

    body.dark-mode .mark-as-read:hover {
        color: #ffffff;
    }



    .loyalty-points:hover {
        background-color: #f8f9fa;
    }

    .points-icon {
        font-size: 28px;
        color: #ffd700; /* Gold color for the icon */
        margin-right: 10px;
    }

    .points-value {
        font-size: 26px;
        font-weight: bold;
        color: #4CAF50; /* Green color for points */
        margin-right: 5px;
    }

    .points-label {
        font-size: 16px;
        color: #555;
    }

    /* Responsive Loyalty Points */
    @media (max-width: 768px) {
        .loyalty-points {
            max-width: 200px;
            padding: 8px 12px;
        }

        .points-icon {
            font-size: 24px;
        }

        .points-value {
            font-size: 22px;
        }

        .points-label {
            font-size: 14px;
        }
    }

    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand" href="Main1.php">TechLink</a>

            <!-- Toggler for mobile view -->
            <button class="navbar-toggler" id="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar content -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- Community Link -->
                    <li class="nav-item">
                        <a class="nav-link" href="community.php">Community</a>
                    </li>

                    <!-- Dropdown for more options -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            aria-expanded="false" data-bs-toggle="dropdown">
                            Discover
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Browse Jobs</a></li>
                            <li><a class="dropdown-item" href="#">Projects</a></li>
                            <li><a class="dropdown-item" href="#">Talent</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Categories</a></li>
                        </ul>
                    </li>
                    <div class="loyalty-points">
    <span class="points-icon">⭐</span>
    <span class="points-value">0.000</span>
    <span class="points-label">Loyalty Points</span>
</div>
                </ul>
                <button id="theme-toggle" class="btn btn-outline-secondary me-3" aria-label="Toggle Dark Mode">
                    <i class="fas fa-moon"></i>
                </button>
                <!-- Right-side options based on authentication status -->
                <?php if ($isAuthenticated === true) { ?>

                <div class="dropdown">
                    <button type="button" class="btn btn-outline-info me-3 inbox-button" aria-label="Inbox"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-envelope"></i>
                        <?php if ($notificationsCount > 0) { ?>
                        <span class="badge bg-danger"><?php echo htmlspecialchars($notificationsCount); ?></span>
                        <?php } ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end notifications" aria-labelledby="dropdownMenuButton1">
                        <li class="dropdown-header">Notifications</li>
                        <?php if (!empty($UnreadNotifications)) { ?>
                        <?php foreach ($UnreadNotifications as $notification) {
            echo renderNotificationItem($notification);
        } ?>
                        <?php } else { ?>
                        <li class="dropdown-item">No new notifications</li>
                        <?php } ?>

                    </ul>
                    
                </div>
                <div class="dropdown">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" aria-label="User Menu"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <a href="profile.php?id={$UserData->getUserID()}">
                            <img src="<?php echo $UserImage?>" alt="User Image" class="user-image">
                        </a> </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="notifications.php">Notifications</a></li>
                        <li><a class="dropdown-item" href="TasksUserInfo.php">Tasks Info</a></li>
                        <li><a class="dropdown-item" href="profile-setting.php">Settings</a></li>
                        <li><a class="dropdown-item" href="auth/logout.php">Logout</a></li>
                    </ul> 
                </div>

                <?php } else { ?>
                <!-- Login and Register Buttons -->
                <a href="auth/login.php" class="btn btn-outline-primary me-3">Login</a>
                <a href="auth/signup.php" class="btn btn-outline-success">Register</a>
                <?php } ?>
            </div>
        </div>
    </nav>

    <!-- Include Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
    // Dark Mode Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    themeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        themeToggle.querySelector('i').classList.toggle('fa-sun');
        themeToggle.querySelector('i').classList.toggle('fa-moon');
    });

    document.querySelectorAll('a[href*="mark_as_read.php"]').forEach(item => {
        item.addEventListener('click', function(event) {
            event.preventDefault();
            const link = this.href;
            fetch(link)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to the URL specified in the JSON response
                        window.location.href = data.redirect;
                    } else {
                        // Handle error messages
                        alert(data.message || 'Error marking as read.');
                    }
                })
                .catch(error => console.error('Error marking as read:', error));
        });
    });
    </script>
</body>

</html>