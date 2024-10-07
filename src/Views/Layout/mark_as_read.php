<?php
session_start();
include_once '../../Controllers/UserController.php'; 

$response = array('success' => false, 'redirect' => 'notifications.php');

if (!isset($_SESSION['user_id'])) {
    $response['message'] = "User not logged in.";
    echo json_encode($response);
    exit();
}

if (isset($_GET['notification_id']) && is_numeric($_GET['notification_id'])) {
    $notification_id = (int)$_GET['notification_id'];
    
    // Call the UserController to mark the notification as read
    $result = UserController::markAsRead($notification_id);
    
    if ($result) {
        $response['success'] = true;
        // Optionally set a redirect URL
        $response['redirect'] = $_SERVER['HTTP_REFERER']; // Redirect to the previous page
    } else {
        $response['message'] = "Failed to mark the notification as read.";
    }
} else {
    $response['message'] = "Invalid notification ID.";
}

echo json_encode($response);
?>
