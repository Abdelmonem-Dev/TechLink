<?php
session_start();

require_once '../Controllers/PostController.php';
$isAuthenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
$_SESSION['account_type'] = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : null;
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = null; // or handle it appropriately if the user is not logged in
}

function escapeHtml($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function generatePost($post) {
    $imageHtml = '';
    $image = $post->getImages();
    $postId = $post->getPostId();

    if (!empty($image) && !empty($image[0])) {
        $imageUrl = escapeHtml($image[0]);
        $imageHtml = "<div class='post-image'><img src='" . $imageUrl . "' alt='Post Image'></div>";
    }
    
    $userProfileUrl = isset($_SESSION['user_id']) 
        ? "profile.php?id=" . escapeHtml($post->getUserId()) 
        : "../Views/auth/login.php";
    
    $userImageHtml = $post->getUserImageUrl() 
        ? "<a href='" . $userProfileUrl . "'><img src='" . escapeHtml($post->getUserImageUrl()) . "' alt='User Image' class='user-img'></a>" 
        : "<a href='" . $userProfileUrl . "'><img src='default-user.png' alt='User Image' class='user-img'></a>";

        if (($_SESSION['account_type'] === 'freelancer' || $_SESSION['account_type'] === 'owner') &&  $_SESSION['user_id'] != $post->getUserID()) {
            $showInfoPost = "<a href='showInfoPost.php?id=$postId' class='btn btn-primary'>Show Info Post</a>";
        }else{
            $showInfoPost = null;
        }
    $dropdownMenu = generateDropdownMenu($post);
    $formattedDate = timeAgo($post->getCreatedAt());
    
    $title = htmlspecialchars_decode($post->getTitle(), ENT_QUOTES);
    $subtitle = htmlspecialchars_decode($post->getSubtitle(), ENT_QUOTES);
    $description = htmlspecialchars_decode($post->getDescription(), ENT_QUOTES);
    $commentsCount = 5; // Dynamic comment count (Example value)
    
    $checkIfLiked = PostController::checkIfLiked($_SESSION['user_id'], $post->getPostId());

    $likeButtonHtml = $checkIfLiked['liked'] 
        ? "<button class='like-btn liked'><i class='fa fa-thumbs-up'></i> <span class='like-count'> " . escapeHtml($post->getLike_count()) . "</span></button>"
        : "<button class='like-btn'><i class='fa-regular fa-thumbs-up'></i> <span class='like-count'> " . escapeHtml($post->getLike_count()) . "</span></button>";
    return "
    <div class='post-container' data-post-id='" . escapeHtml($post->getPostId()) . "'>
        <div class='post-header'>
            <div class='user-info'>
                $userImageHtml
                <div class='user-details'>
                    <span class='user-name'>" . escapeHtml($post->getUserName()) . "</span>
                    <span class='hours-played'>$formattedDate</span>
                </div>
            </div>
            <button class='settings-btn btn btn-secondary dropdown-toggle' type='button' id='settingsDropdown' data-bs-toggle='dropdown' aria-expanded='false'>
                <i class='fa fa-cog'></i>
            </button>
            <ul class='dropdown-menu' aria-labelledby='settingsDropdown'>
                $dropdownMenu
            </ul>
        </div>
        <div class='post-content'>
            <h3 class='post-title'>" . escapeHtml($title) . "</h3>
            <div class='subtitle-container'>
                <p class='post-subtitle'>" . escapeHtml($subtitle) . "</p>
            </div>
            <p class='post-description'><small>" . escapeHtml($description) . "</small></p>
        </div>
        $imageHtml
        <div class='post-footer'>
            <button class='comment-btn' onclick='toggleComments(" . escapeHtml($post->getPostId()) . ")' style='float: left;'><i class='fa fa-comments'></i> Comments $commentsCount</button>
            $showInfoPost
            $likeButtonHtml
        </div>
        <!-- Comments Section -->
        <div id='comments_" . escapeHtml($post->getPostId()) . "' class='comments-section hidden'>
            <div class='comments-header'>
                <h4>Comments</h4>
                <button class='close-comments-btn' onclick='toggleComments(" . escapeHtml($post->getPostId()) . ")'>Close</button>
            </div>
            <div class='comments-body'>
                " . generateComments($post->getPostId()) . "
            </div>
            <div class='comments-footer'>
                <input type='text' placeholder='Add a comment...' class='comment-input'>
                <button class='comment-send-btn'>Send</button>
            </div>
        </div>
    </div>";
}


function generateComments($postId) {
    $comments = PostController::getComments($postId);
    $commentsHtml = '';
    foreach ($comments as $comment) {
        $commentId = escapeHtml($comment['comment_id']);
        $userImage = $comment['user_image_url'] ? escapeHtml($comment['user_image_url']) : 'default-user.png';
        $userName = escapeHtml($comment['user_name']);
        $commentText = escapeHtml($comment['comment_text']);
        $likeCount = escapeHtml($comment['like_count']);
        
        // Generate the nested replies
        $repliesHtml = generateComments($commentId);

        $commentsHtml .= "
        <div class='comment'>
            <img src='$userImage' alt='User Image' class='comment-user-img'>
            <div class='comment-details'>
                <span class='comment-username'>$userName</span>
                <span class='comment-text'>$commentText</span>
                <div class='comment-actions'>
                    <button class='comment-like-btn'><i class='fa fa-thumbs-up'></i> $likeCount</button>
                    <button class='comment-reply-btn' onclick='toggleReplyForm($commentId)'>Reply</button>
                </div>
                <div id='replyForm_$commentId' class='reply-form' style='display: none;'>
                    <input type='text' placeholder='Reply...' class='reply-input'>
                    <button class='reply-send-btn' onclick='sendReply($commentId, $postId)'>Send</button>
                </div>
                $repliesHtml
            </div>
        </div>";
    }

    return $commentsHtml;
}

function generateDropdownMenu($post) {
    $userId = $post->getUserId();
    $postId = $post->getPostId();
    
    if (isset($_SESSION['account_type']) && $_SESSION['account_type'] === 'owner') {
        return "
            <li><a class='dropdown-item' href='deletePost.php' data-action='delete' data-post-id='" . escapeHtml($postId) . "'>Delete</a></li>
        ";
    } else {
        if (isset($_SESSION['user_id'])) {
            if ($userId === $_SESSION['user_id']) {
                return "
                    <li><a class='dropdown-item' href='#' data-action='update' data-post-id='" . escapeHtml($postId) . "'>Update</a></li>
                    <li><a class='dropdown-item' href='deletePost.php' data-action='delete' data-post-id='" . escapeHtml($postId) . "'>Delete</a></li>
                ";
            } else {
                return "
                    <li><a class='dropdown-item' href='#' data-action='block' data-post-id='" . escapeHtml($postId) . "'>Block</a></li>
                    <li><a class='dropdown-item' href='#' data-action='report' data-post-id='" . escapeHtml($postId) . "'>Report</a></li>
                ";
            }
        } else {
            return "
                <li><a class='dropdown-item' href='auth/login.php' data-action='Login'>Login to interact with posts</a></li>
            ";
        }
    }
}


$searchTerm = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$budget = $_GET['budget'] ?? '';
$deliveryTime = $_GET['delivery_time'] ?? '';

$posts = fetchPosts($searchTerm, $category, $budget, $deliveryTime);
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Explore new places, food, culture around the world and many more">
    <meta name="keywords" content="explore, travel, food, culture, world">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">




    <!-- Custom CSS -->
    <link rel="stylesheet" href="communityStyle.css">
    <style>
    body {
        background-color: #1b1b1b;
        color: #ccc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
    }

    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .post-container {
        width: 100%;
        max-width: 700px;
        background-color: #202225;
        color: #ccc;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease-in-out;
        text-align: left;
        /* Align text to the left */
    }

    .post-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
    }

    .post-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #333;
        padding-bottom: 10px;
    }

    .user-info {
        display: flex;
        align-items: center;
    }

    .user-img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .user-img:hover {
        transform: scale(1.1);
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
    }

    .user-name {
        color: #fff;
        font-weight: bold;
        font-size: 1.1em;
    }

    .hours-played {
        color: #aaa;
        font-size: 0.8em;
    }

    .settings-btn,
    .like-btn,
    .info-btn {
        background: none;
        border: none;
        color: #1DA1F2;
        font-size: 16px;
        cursor: pointer;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .settings-btn:hover,
    .like-btn:hover,
    .info-btn:hover {
        color: #00aced;
        transform: scale(1.1);
    }

    .post-content {
        margin-top: 10px;
    }

    .post-title {
        color: #fff;
        font-size: 1.3em;
        font-weight: bold;
    }

    .post-subtitle {
        color: #1DA1F2;
        /* Choose a color that stands out */
        font-size: 1.1em;
        font-weight: bold;
    }

    .post-description {
        color: #ccc;
        /* Adjust to a readable size */
    }

    .post-image img {
        width: 100%;
        border-radius: 8px;
        margin-top: 10px;
        transition: opacity 0.3s ease;
    }

    .post-image img:hover {
        opacity: 0.8;
    }

    .post-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.9em;
        border-top: 1px solid #333;
        padding-top: 10px;
        margin-top: 10px;
    }

    .helpful-count {
        color: #888;
    }

    .info-btn {
        background-color: #4CAF50;
        color: white;
        padding: 8px 16px;
        margin: 5px 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .info-btn:hover {
        background-color: #45a049;
    }

    .modal-content {
        background: #202225;
        color: #ccc;
        border-radius: 8px;
    }

    .modal-content h2 {
        color: #1DA1F2;
    }

    .image-upload-placeholder {
        border: 2px dashed #444;
        padding: 20px;
        border-radius: 8px;
        background-color: #333;
        cursor: pointer;
    }

    .image-upload-placeholder img {
        width: 100px;
        height: 100px;
    }

    .image-upload-placeholder p {
        color: #aaa;
        margin-top: 10px;
    }

    @media (max-width: 768px) {
        .post-container {
            padding: 15px;
            margin-bottom: 15px;
        }

        .post-title {
            font-size: 1.1em;
        }

        .post-subtitle {
            font-size: 0.9em;
        }
    }

    .btn-outline-secondary {
        border-color: #ccc;
        color: #ccc;
    }

    .btn-outline-secondary:hover {
        background-color: #444;
        border-color: #444;
        color: #fff;
    }

    .dropdown-menu {
        background-color: #202225;
        color: #ccc;
    }

    .dropdown-item {
        color: #ccc;
    }

    .dropdown-item:hover {
        background-color: #333;
        color: #fff;
    }

    /* Styles for the success message */
    .message {
        padding: 15px;
        margin: 10px 0;
        border-radius: 5px;
        font-size: 16px;
        color: #fff;
        position: relative;
        transition: opacity 0.5s ease-in-out;
    }

    /* Base styles for message container */
    .message {
        padding: 15px;
        margin: 20px auto;
        max-width: 600px;
        border-radius: 5px;
        font-size: 16px;
        color: #fff;
        position: relative;
        text-align: center;
        opacity: 1;
        visibility: visible;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }

    /* Success message styling */
    .message.success {
        background-color: #28a745;
        /* Green */
    }

    /* Error message styling */
    .message.error {
        background-color: #dc3545;
        /* Red */
    }

    /* Warning message styling */
    .message.warning {
        background-color: #ffc107;
        /* Yellow */
        color: #333;
        /* Dark text for better contrast */
    }

    /* Hide message with class 'hide' */
    .message.hide {
        opacity: 0;
        visibility: hidden;
    }

    .container {
        display: flex;
        position: relative;
    }

    .left-content {
        flex: 1;
        padding: 10px;
    }

    .post-container {
        margin-bottom: 20px;
        border: 1px solid #444;
        padding: 15px;
        background-color: #1f1f1f;
        color: white;
        border-radius: 8px;
        position: relative;
    }

    .post-container {
        margin-bottom: 20px;
        border: 1px solid #444;
        padding: 15px;
        background-color: #1f1f1f;
        color: white;
        border-radius: 8px;
        position: relative;
    }

    .post-container {
        margin-bottom: 20px;
        border: 1px solid #444;
        padding: 15px;
        background-color: #1f1f1f;
        color: white;
        border-radius: 8px;
        position: relative;
    }

    .post-container {
        margin-bottom: 20px;
        border: 1px solid #444;
        padding: 15px;
        background-color: #1f1f1f;
        color: white;
        border-radius: 8px;
        position: relative;
    }

    .comments-section {
        margin-top: 10px;
        display: none;
        background-color: #2a2a2a;
        border-top: 1px solid #444;
        padding: 0;
        animation: slide-down 0.3s ease-out;
    }

    .comments-section.open {
        display: block;
        max-height: 300px;
        overflow-y: auto;
    }

    .comments-header {
        padding: 10px;
        background-color: #333;
        border-bottom: 1px solid #444;
        position: sticky;
        top: 0;
        z-index: 10;
        display: flex;
        justify-content: space-between;
    }

    .comments-body {
        padding: 10px;
    }

    .comment,
    .reply {
        display: flex;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #444;
    }

    .comment-user-img,
    .reply-user-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .comment-details,
    .reply-details {
        flex-grow: 1;
    }

    .comment-username,
    .reply-username {
        font-weight: bold;
        display: block;
        margin-bottom: 2px;
    }

    .comment-text,
    .reply-text {
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .comment-actions,
    .reply-actions {
        margin-top: 5px;
        display: flex;
        gap: 15px;
    }

    .comment-actions button,
    .reply-actions button {
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        font-size: 14px;
    }

    .comment-actions button:hover,
    .reply-actions button:hover {
        color: white;
    }

    .comments-footer {
        padding: 10px;
        background-color: #333;
        display: flex;
        gap: 10px;
        position: sticky;
        bottom: 0;
    }

    .comments-footer input {
        flex-grow: 1;
        padding: 8px;
        background-color: #444;
        border: none;
        border-radius: 5px;
        color: white;
    }

    .comments-footer button {
        padding: 8px 12px;
        background-color: #ff9900;
        border: none;
        color: white;
        cursor: pointer;
        border-radius: 5px;
    }

    .reply-section {
        margin-left: 50px;
        padding-left: 10px;
        border-left: 1px solid #444;
    }

    .badge {
        background-color: #007bff;
        padding: 2px 6px;
        border-radius: 12px;
        font-size: 12px;
        margin-left: 5px;
    }

    @keyframes slide-down {
        from {
            max-height: 0;
            opacity: 0;
        }

        to {
            max-height: 300px;
            opacity: 1;
        }
    }



    /* Comment button styling */
    .comment-btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }


    /* Comments Header */
    .comments-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .comments-header h4 {
        margin: 0;
        font-size: 1.2em;
    }

    /* Comments Body */
    .comments-body {
        max-height: 200px;
        overflow-y: auto;
        padding: 5px 0;
    }

    /* Individual comment structure */
    .comment {
        display: flex;
        margin-bottom: 10px;
        padding: 8px;
        background-color: #fff;
        border-radius: 6px;
        box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1);
    }

    .comment .user-avatar {
        margin-right: 10px;
    }

    .comment .user-avatar img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .comment .comment-content {
        flex: 1;
    }

    .comment .comment-content .comment-text {
        margin: 0;
        font-size: 0.9em;
    }

    .comment .comment-content .comment-meta {
        font-size: 0.8em;
        color: #777;
        margin-top: 5px;
    }

    /* Comments Footer */
    .comments-footer {
        display: flex;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid #ddd;
    }

    .comment-input {
        flex: 1;
        padding: 8px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-right: 10px;
    }

    .comment-send-btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .comment-send-btn:hover {
        background-color: #0056b3;
    }
    </style>



    <title>Explore New Places</title>
</head>

<body>
    <?php include "Layout/header.php"; ?>

    <?php if ($message): ?>
    <div class="message <?php echo $messageType; ?>">
        <div class="message success">
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); 
            
if (!isset($_SESSION['message_shown'])) {
    $_SESSION['message_shown'] = false;
}
            ?>
        </div>
    </div>
    <?php endif; ?>


    <section id="explore" class="explore py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 style="color: white;">Community</h2>
                <p>Explore new places, food, culture around the world and many more</p>
                <div class="container">
                    <div style=" display: flex;">
                        <div class="dropdown d-inline-block">

                            <!-- Category Dropdown -->
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="allDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    All
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="allDropdown">
                                    <li><a class="dropdown-item" href="?">All</a></li>
                                </ul>
                            </div>
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Category
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                                <li><a class="dropdown-item" href="?category=Web">Web App</a></li>
                                <li><a class="dropdown-item" href="?category=Desktop">Desktop App</a></li>
                                <li><a class="dropdown-item" href="?category=Mobile">Mobile App</a></li>
                                <li><a class="dropdown-item" href="?category=AI">AI Model</a></li>
                                <li><a class="dropdown-item" href="?category=UI-UX">UI-UX Design</a></li>
                            </ul>

                            <!-- Budget Dropdown -->
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="budgetDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Budget
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="budgetDropdown">
                                <li><a class="dropdown-item" href="?budget=0-50">$0 - $50</a></li>
                                <li><a class="dropdown-item" href="?budget=51-100">$51 - $100</a></li>
                                <li><a class="dropdown-item" href="?budget=101-200">$101 - $200</a></li>
                                <li><a class="dropdown-item" href="?budget=201-500">$201 - $500</a></li>
                                <li><a class="dropdown-item" href="?budget=500+">$500+</a></li>
                            </ul>

                            <!-- Delivery Time Dropdown -->
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                id="deliveryTimeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Delivery Time
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="deliveryTimeDropdown">
                                <li><a class="dropdown-item" href="?delivery_time=1_day">1 Day</a></li>
                                <li><a class="dropdown-item" href="?delivery_time=3_days">3 Days</a></li>
                                <li><a class="dropdown-item" href="?delivery_time=1_week">1 Week</a></li>
                                <li><a class="dropdown-item" href="?delivery_time=1_month">1 Month</a></li>
                            </ul>

                        </div>
                    </div>
                </div>
                <br><br>

                <div class="d-flex justify-content-center">
                    <div class="dropdown me-1">
                        <?php if ($isAuthenticated): ?>
                        <button id="myBtn" class="btn btn-primary" onclick="openModal()">Create Post</button>
                        <?php else: ?>
                        <p>Please log in to create a post.</p>
                        <?php endif; ?>

                        <?php $Categories = PostController::getCategories();?>

                        <div id="myModal" class="modal">
                            <!-- Modal content -->
                            <div class="modal-content">
                                <span class="close">&times;</span>
                                <h2 class="text-center text-success">Create New Post</h2>
                                <form action="ccommunity.php" method="post" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="postTitle" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="postTitle" name="title" required>
                                        <small id="titleCharCount" class="text-muted">0/128 characters</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="postSubtitle" class="form-label">Subtitle</label>
                                        <select class="form-control" id="postSubtitle" name="subtitle" required>
                                            <option value="" disabled selected>Select a subtitle</option>
                                            <?php foreach ($Categories as $subtitle): ?>
                                            <option
                                                value="<?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="postDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="postDescription" name="description" rows="3"
                                            required></textarea>
                                        <small id="charCount" class="text-muted">0/1000 characters</small>
                                    </div>

                                    <!-- Optional Budget Field -->
                                    <div class="mb-3">
                                        <label for="postBudget" class="form-label">Budget (optional)</label>
                                        <input type="number" class="form-control" id="postBudget" name="budget" min="0"
                                            step="0.01">
                                    </div>

                                    <!-- Optional Delivery Time Field -->
                                    <div class="mb-3">
                                        <label for="postDeliveryTime" class="form-label">Delivery Time (in days,
                                            optional)</label>
                                        <input type="number" class="form-control" id="postDeliveryTime"
                                            name="delivery_time" min="1" step="1">
                                    </div>

                                    <div class="mb-3">
                                        <label for="postImages" class="form-label">Upload Images (optional)</label>
                                        <input type="file" class="form-control" id="postImages" name="PostPictures[]"
                                            multiple>
                                    </div>

                                    <div class="text-center">
                                        <input type="submit" value="Create" class="btn btn-primary">
                                    </div>
                                </form>
                            </div>
                        </div>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var titleInput = document.getElementById('postTitle');
                            var titleCharCount = document.getElementById('titleCharCount');
                            var titleMaxLength = 128;

                            var descriptionTextarea = document.getElementById('postDescription');
                            var descriptionCharCount = document.getElementById('charCount');
                            var descriptionMaxLength = 1000;

                            titleInput.addEventListener('input', function() {
                                var titleLength = titleInput.value.length;
                                titleCharCount.textContent = titleLength + '/' + titleMaxLength +
                                    ' characters';

                                if (titleLength > titleMaxLength) {
                                    titleInput.value = titleInput.value.slice(0, titleMaxLength);
                                    titleCharCount.textContent = titleMaxLength + '/' + titleMaxLength +
                                        ' characters';
                                }
                            });

                            descriptionTextarea.addEventListener('input', function() {
                                var descriptionLength = descriptionTextarea.value.length;
                                descriptionCharCount.textContent = descriptionLength + '/' +
                                    descriptionMaxLength + ' characters';

                                if (descriptionLength > descriptionMaxLength) {
                                    descriptionTextarea.value = descriptionTextarea.value.slice(0,
                                        descriptionMaxLength);
                                    descriptionCharCount.textContent = descriptionMaxLength + '/' +
                                        descriptionMaxLength + ' characters';
                                }
                            });
                        });
                        </script>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Title character limit
                            var titleInput = document.getElementById('postTitle');
                            var titleCharCountDisplay = document.getElementById('titleCharCount');
                            var maxTitleChars = 128;

                            titleInput.addEventListener('input', function() {
                                var text = titleInput.value;
                                var charCount = text.length;

                                if (charCount > maxTitleChars) {
                                    // Truncate the text to the maximum character limit
                                    titleInput.value = text.slice(0, maxTitleChars);
                                    titleCharCountDisplay.textContent = maxTitleChars + '/' +
                                        maxTitleChars + ' characters'; // Fix display
                                } else {
                                    // Update character count display if within the limit
                                    titleCharCountDisplay.textContent = charCount + '/' +
                                        maxTitleChars + ' characters';
                                }
                            });

                            // Description character limit
                            var textarea = document.getElementById('postDescription');
                            var charCountDisplay = document.getElementById('charCount');
                            var maxChars = 1000; // Set your character limit here

                            textarea.addEventListener('input', function() {
                                var text = textarea.value;
                                var charCount = text.length;

                                if (charCount > maxChars) {
                                    // Truncate the text to the maximum character limit
                                    textarea.value = text.slice(0, maxChars);
                                    charCountDisplay.textContent = maxChars + '/' + maxChars +
                                        ' characters'; // Fix display
                                } else {
                                    // Update character count display if within the limit
                                    charCountDisplay.textContent = charCount + '/' + maxChars +
                                        ' characters';
                                }
                            });
                        });
                        </script>

                        <style>
                        .modal {
                            max-width: 100%;
                            width: 100%;
                        }

                        #myShowModal .modal-dialog {
                            max-width: 90% !important;
                            width: 1500px !important;
                            margin: auto !important;
                        }

                        .modal-content {
                            padding: 20px;
                        }

                        /* Ensure the content scales well */
                        .scrollable-image-gallery img {
                            max-width: 250px;
                            /* Increase the size of images */
                            height: auto;
                            margin: 5px;
                        }

                        .scrollable-image-gallery {
                            display: flex;
                            flex-wrap: wrap;
                            justify-content: center;
                            gap: 10px;
                        }

                        .lightbox img {
                            max-width: 95%;
                            max-height: 90%;
                        }

                        .lightbox .close-lightbox {
                            font-size: 48px;
                            /* Larger close button */
                        }


                        /* Adjust scrollable gallery */
                        .scrollable-image-gallery {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 10px;
                        }

                        .scrollable-image-gallery img {
                            cursor: pointer;
                            transition: transform 0.2s ease;
                            max-width: 200px;
                            height: auto;
                            flex: 1 1 auto;
                        }

                        .scrollable-image-gallery img:hover {
                            transform: scale(1.05);
                        }

                        /* Lightbox Styles */
                        .lightbox {
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background: rgba(0, 0, 0, 0.8);
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            z-index: 1050;
                            /* Ensure it's above the modal */
                        }

                        .lightbox img {
                            max-width: 90%;
                            max-height: 80%;
                            margin: auto;
                        }

                        .close-lightbox {
                            position: absolute;
                            top: 10px;
                            right: 25px;
                            color: white;
                            font-size: 36px;
                            font-weight: bold;
                            cursor: pointer;
                        }
                        </style>






                    </div>
                </div>
            </div>
            <!-- Displaying Posts -->
            <?php  
                if (is_array($posts) && !empty($posts)) {
                    foreach ($posts as $post) {
                        echo generatePost($post);     
                    }
                } else {
                    echo "<p>No posts available.</p>";
                }
                ?>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz4fnFO9gyb3GvhqSrDp9C1OujTpRD5rGhOqUew9o8t7LD6RGGiQmOri1y" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-QJHtvGhmr9J7HboiNR6LYPJOiioKD5cw5Y/6pC2VfycJ1OhWWTf9SA5KkGh/MmL7" crossorigin="anonymous">
    </script>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle dropdown actions
        document.querySelectorAll('.dropdown-item').forEach(function(item) {
            item.addEventListener('click', function(event) {
                var action = this.getAttribute('data-action');
                var postId = this.getAttribute('data-post-id');

                if (action === 'update') {
                    event.preventDefault();
                    window.location.href = 'updatePost.php?post_id=' + postId;
                } else if (action === 'delete') {
                    event.preventDefault();
                    if (confirm('Are you sure you want to delete this post?')) {
                        window.location.href = 'deletePost.php?post_id=' + postId;
                    }
                } else if (action === 'block') {
                    // Handle block action
                    event.preventDefault();
                } else if (action === 'report') {
                    // Handle report action
                    event.preventDefault();
                } else if (action === 'Login') {
                    event.preventDefault();
                    window.location.href = 'auth/login.php';
                }
            });
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var likeButtons = document.querySelectorAll('.like-btn');

        likeButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var postId = btn.closest('.post-container').getAttribute('data-post-id');
                var userId =
                    '<?php echo $_SESSION['user_id']; ?>'; // Extract PHP session data to JS

                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'likeHandler.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var data = JSON.parse(xhr.responseText);
                        if (data.success) {
                            if (data.checkIfLiked) {
                                btn.querySelector('.fa-thumbs-up').classList.add('fa');
                                btn.querySelector('.fa-thumbs-up').classList.remove(
                                    'fa-regular');
                            } else {
                                btn.querySelector('.fa').classList.add('fa-regular');
                                btn.querySelector('.fa').classList.remove('fa');
                            }
                            btn.querySelector(".like-count").textContent = data.like_count;
                        } else {
                            console.error(data.message);
                        }
                    } else {
                        console.error('Request failed. Status: ' + xhr.status);
                    }
                };

                xhr.onerror = function() {
                    console.error('Request error');
                };

                xhr.send('user_id=' + encodeURIComponent(userId) +
                    '&post_id=' + encodeURIComponent(postId) +
                    '&action=toggle');
            });
        });
    });
    </script>

    <script>
    // Modal handling
    var modal = document.getElementById("myModal");
    var btn = document.getElementById("myBtn");
    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }



    document.addEventListener('DOMContentLoaded', () => {
        const message = document.querySelector('.message');

        if (message) {
            setTimeout(() => {
                message.classList.add('hide');
            }, 5000); // Adjust this duration as needed (5000ms = 5 seconds)
        }
    });

    function toggleComments(postId) {
        const commentsSection = document.getElementById('comments_' + postId);
        if (commentsSection.style.display === 'none' || commentsSection.style.display === '') {
            commentsSection.style.display = 'block';
        } else {
            commentsSection.style.display = 'none';
        }
    }
    </script>
</body>

</html>