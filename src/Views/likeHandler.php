<?php
require_once '../Controllers/PostController.php';

header('Content-Type: application/json');

$user_id = $_POST['user_id'];
$post_id = $_POST['post_id'];
$action = $_POST['action'];

$response = [];
$liked = LikeModel::checkIfLiked($user_id, $post_id);

if ($action === 'toggle') {
    if ($liked) {
        $success = LikeModel::removeLike($user_id, $post_id);
    } else {
        $success = LikeModel::addLike($user_id, $post_id);
    }

    if ($success) {
        $like_count = LikeModel::getLikeCount($post_id);
        $response = ['success' => true, 'checkIfLiked' => !$liked, 'like_count' => $like_count];
    } else {
        $response = ['success' => false, 'message' => 'Unable to process like'];
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid action'];
}

http_response_code($response['success'] ? 200 : 400);
echo json_encode($response);
