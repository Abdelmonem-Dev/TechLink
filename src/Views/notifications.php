<?php
include_once '../Controllers/UserController.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$isAuthenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;
$user_id = $_SESSION['user_id'];

if ($isAuthenticated) {
    $AllNotifications = UserController::getNotifications($user_id); 
}

function renderNotificationWithButtons($notification) {
    $sender_user_id = htmlspecialchars($notification['sender_user_id']);
    $UserSenderData = UserController::FetchByUserID1($sender_user_id);
    $senderName = htmlspecialchars($UserSenderData->getFirstName() . ' ' . $UserSenderData->getLastName());
    $senderImage = htmlspecialchars($UserSenderData->getImageUrl());

    $timeAgo = timeAgo($notification['created_at']);
    $notificationId = htmlspecialchars($notification['notification_id']);
    $notificationMessage = htmlspecialchars($notification['message']);
    $isApprove = $notification['is_approve']; // Cast to integer for strict comparison
    $post_id = htmlspecialchars($notification['post_id']); // Ensure post_id is escaped

    // Serialize and encode the $notification array to pass it in the URL
    $notificationEncoded = urlencode(json_encode($notification));

    // Determine the content based on approval status
    if ($isApprove === 1) {
        $actionMessage = "<span class='notification-status approved'>Approved</span>";
    } elseif ($isApprove === 0) {
        $actionMessage = "<span class='notification-status rejected'>Rejected</span>";
    } else {
        $actionMessage = <<<HTML
        <div class="notification-actions">
            <a href="handle_notification.php?notification={$notificationEncoded}&action=approve&post_id={$post_id}" class="btn btn-success">Approve</a>
            <a href="handle_notification.php?notification_id={$notificationId}&action=reject" class="btn btn-danger">Reject</a>
            <a href="handle_notification.php?notification_id={$notificationId}&action=delete" class="btn btn-danger delete-button">Delete</a>
        </div>
HTML;
    }

    return <<<HTML
<div class="notification-item" role="listitem">
    <a href="profile.php?id={$sender_user_id}" class="user-image-link">
        <img src="{$senderImage}" alt="{$senderName}'s image" class="user-image">
    </a>
    <div class="notification-content">
        <div class="notification-header">
            <strong class="user-name">{$senderName}</strong>
            <small class="notification-time">{$timeAgo}</small>
        </div>
        <p class="notification-message">{$notificationMessage}</p>
        {$actionMessage}
    </div>
</div>
HTML;
}


function renderNotification($notification) {
    // Check the type of notification and render accordingly
    if ($notification['notification_type'] === 'action') {
        return renderNotificationWithButtons($notification);
    } else {
        return renderNotificationItem1($notification);
    }
}

function renderNotificationItem1($notification) {
    $notification_id = htmlspecialchars($notification['notification_id']);
    $sender_user_id = htmlspecialchars($notification['sender_user_id']);
    $message = htmlspecialchars($notification['message']);
    $link = htmlspecialchars($notification['link']);
    $created_at = htmlspecialchars($notification['created_at']);
    $is_read = (bool) $notification['is_read'];

    $timeAgo = timeAgo($created_at); // Format the date/time correctly
    $isReadClass = $is_read ? 'read' : 'unread';

    // Fetch sender user data
    $UserSenderData = UserController::FetchByUserID1($sender_user_id);
    $senderName = htmlspecialchars($UserSenderData->getFirstName() . ' ' . $UserSenderData->getLastName());
    $senderImage = htmlspecialchars($UserSenderData->getImageUrl());

    return <<<HTML
<li class="dropdown-item {$isReadClass}">
    <a href="profile.php?id={$sender_user_id}" class="user-image-link">
        <img src="{$senderImage}" alt="{$senderName}'s image" class="user-image">
    </a>
    <div class="notification-content">
        <div class="notification-header">
            <strong class="user-name">{$senderName}</strong>
            <small class="notification-time">{$timeAgo}</small>
        </div>
        <p class="notification-message">{$message}</p>
        <div class="notification-actions">
            <a href="{$link}" class="notification-link">View</a>
            <a href="mark_as_read.php?notification_id={$notification_id}" class="mark-as-read">Mark as Read</a>
            <a href="handle_notification.php?notification_id={$notification_id}&action=delete" class="btn btn-danger delete-button">Delete</a>
        </div>
    </div>
</li>
HTML;
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <style>
/* Global Styles */
body {
    background-color: #f8f9fa;
    font-family: Arial, sans-serif;
}

.container {
    margin-top: 30px;
}

/* Header Section */
.notification-header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.notification-header-section h2 {
    margin: 0;
    color: #343a40;
}

.mark-all-read {
    background-color: #007bff;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.mark-all-read:hover {
    background-color: #0056b3;
}

/* Notifications List */
.notifications-list {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    position: relative;
}

/* Loading Spinner */
.loading-spinner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: none;
}

/* Notification Items */
.notification-item, .dropdown-item {
    display: flex;
    align-items: flex-start;
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
    background-color: #fff;
    border-radius: 8px;
    transition: background-color 0.3s ease;
    margin-bottom: 10px;
}

.notification-item.unread {
    background-color: #e8f0fe;
}

.notification-item.read {
    background-color: #f8f9fa;
}

.notification-item:hover {
    background-color: #f1f3f5;
}

.user-image-link {
    margin-right: 15px;
}

.user-image {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #dee2e6;
}

.notification-content {
    flex-grow: 1;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.user-name {
    font-size: 1.2em;
    color: #495057;
}

.notification-message {
    font-size: 1em;
    color: #495057;
}

.notification-time {
    font-size: 0.85em;
    color: #868e96;
}

.notification-status {
    font-weight: bold;
}

.notification-status.approved {
    color: green;
}

.notification-status.rejected {
    color: red;
}

.notification-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.btn {
    padding: 5px 10px;
    text-decoration: none;
    color: #fff;
    border-radius: 4px;
}

.btn-success {
    background-color: #28a745;
}

.btn-danger {
    background-color: #dc3545;
}

.delete-button {
    margin-left: auto;
}

.mark-as-read {
    font-size: 0.85em;
    color: #007bff;
    margin-top: 5px;
    display: inline-block;
    text-decoration: none;
    transition: color 0.3s ease;
}

.mark-as-read:hover {
    color: #0056b3;
    text-decoration: underline;
}

.empty-state {
    text-align: center;
    color: #868e96;
    margin-top: 30px;
}

/* Responsive Styles */
@media (max-width: 767.98px) {
    .notification-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .user-image {
        width: 40px;
        height: 40px;
        margin-bottom: 10px;
    }

    .notification-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .user-name {
        font-size: 1em;
    }

    .notification-time {
        font-size: 0.75em;
        margin-top: 4px;
    }

    .mark-as-read {
        margin-top: 10px;
    }
}

@media (max-width: 575.98px) {
    .container {
        padding: 0 15px;
    }

    .notification-header-section h2 {
        font-size: 1.5em;
    }

    .mark-all-read {
        padding: 5px 8px;
        font-size: 0.9em;
    }
}

    </style>
</head>

<body>
    <?php include "Layout/header.php"; ?>

    <div class="container">
        <div class="notification-header-section">
            <h2>Notifications</h2>
            <button class="mark-all-read">Mark All as Read</button>
        </div>
        <div class="notifications-list" role="list">

            <?php if (!empty($AllNotifications)) { ?>
            <?php foreach ($AllNotifications as $notification) {
                echo renderNotification($notification);
            } ?>
            <?php } else { ?>
            <div class="empty-state">No new notifications</div>
            <?php } ?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.mark-as-read').forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const url = this.getAttribute('href');
                fetch(url, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log('Notification marked as read');
                        this.closest('.dropdown-item').classList.add('read');
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    });
    </script>
</body>

</html>