<?php
session_start();
require_once '../Controllers/PostController.php';

$_SESSION['message_shown'] = true; 

// Check if the user is authenticated and authorized
if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (isset($_GET['post_id']) && is_numeric($_GET['post_id'])) {
    $postId = intval($_GET['post_id']);

    $result = PostController::deletePost($postId);

    if ($result) {
        echo json_encode(['success' => true]);
        header("Location: community.php?message=Post+deleted+successfully");
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete post']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
}
?>
