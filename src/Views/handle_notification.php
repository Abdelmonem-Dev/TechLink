<?php
session_start();

include_once __DIR__ . '/../Controllers/UserController.php';
include_once __DIR__ . '/../Controllers/TaskController.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?status=error&message=You need to be logged in to perform this action.");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $notificationSerialized = $_GET['notification'] ?? null;
    $action = $_GET['action'] ?? null;
    $postId = $_GET['post_id'] ?? null;

    if ($notificationSerialized) {
        // Decode the notification object from the URL
        $notification = json_decode(urldecode($notificationSerialized), true);
        if (!$notification) {
            header("Location: notifications.php?status=error&message=Failed to decode notification.");
            exit();
        }

        $notificationId = $notification['notification_id'];
        $freelancer_id = $notification['sender_user_id'];

        if ($notificationId) {
            switch ($action) {
                case 'approve':
                    $result = UserController::approveNotification($notificationId);
                    if ($result) {
                        $task = new Task();
                        $task->setPostID($postId);
                        $task->setUserID($user_id);
                        $task->setfreelancerID($freelancer_id);
                        $task->setStatus('ongoing');
                        $task->setDeadline(0);
                        TaskController::createTask($task);

                        // Send a notification to the freelancer
                        UserController::sendApprovalNotificationToFreelancer($notificationId);

                        $status = 'success';
                        $message = 'Notification approved successfully.';
                    } else {
                        $status = 'error';
                        $message = 'Failed to approve notification.';
                    }
                    break;

                case 'reject':
                    $result = UserController::rejectNotification($notificationId);
                    $status = $result ? 'success' : 'error';
                    $message = $result ? 'Notification rejected successfully.' : 'Failed to reject notification.';
                    break;

                case 'delete':
                    $result = UserController::deleteNotification($notificationId);
                    $status = $result ? 'success' : 'error';
                    $message = $result ? 'Notification deleted successfully.' : 'Failed to delete notification.';
                    break;

                default:
                    $status = 'error';
                    $message = 'Invalid action specified.';
                    break;
            }

            header("Location: notifications.php?status={$status}&message=" . urlencode($message));
        } else {
            header("Location: notifications.php?status=error&message=Notification ID is missing.");
        }
    } else {
        header("Location: notifications.php?status=error&message=Notification is missing.");
    }
} else {
    header("Location: notifications.php?status=error&message=Invalid request method.");
}
exit();

