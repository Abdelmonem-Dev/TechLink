<?php 
include_once '../Controllers/PostController.php';
include_once '../Controllers/UserController.php';
include_once '../Controllers/TaskController.php';
session_start();
$isAuthenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid post ID.";
    exit();
}

if (!isset($_SESSION['user_id'])) {
    die("Error: User ID not Found");
}
$freelancer_id = $_SESSION['user_id'];
$post_id = isset($_GET['id']) ? $_GET['id'] : null;

$post = PostController::getByPostID($post_id);
$user = UserController::FetchByUserID1($post['user_id']);
$user_id = $user->getUserID();

$Task = TaskController::fetchByUserAndPostID($user_id,$post_id,$freelancer_id);
if ($Task !== null) {
    $task_id = $Task->getTaskID();
    // Continue with the rest of your code using $task_id
}else{
}


$FullName = $user->getFirstName() . " " . $user->getLastName();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars("Request the Task"); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f5f5f5;
        color: #333;
        margin: 0;
        padding: 0;
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 0 15px;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    /* Sidebar section */
    .sidebar-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
        flex: 1 1 300px;
        /* Adjust based on sidebar content width */
    }

    .service-details,
    .request-task {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Main content section */
    .service-main {
        flex: 2 1 600px;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .service-details h3,
    .seller-info h4 {
        font-size: 20px;
        color: #6c757d;
        margin-bottom: 15px;
    }

    .service-details ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .service-details ul li {
        font-size: 16px;
    }

    .seller-info {
        text-align: center;
        border-top: 1px solid #ddd;
        padding-top: 15px;
    }

    .seller-info img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-bottom: 10px;
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .seller-info img:hover {
        transform: scale(1.1);
    }

    .contact-button,
    .request-button {
        background-color: #6c757d;
        /* Primary color */
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .contact-button:hover,
    .request-button:hover {
        background-color: #FF7849;
        /* Lighter shade for hover */
    }

    .image-carousel {
        position: relative;
        max-width: 100%;
        overflow: hidden;
        border-radius: 8px;
    }

    .carousel-slide {
        display: none;
        text-align: center;
    }

    .carousel-slide img {
        width: 100%;
        height: 400px;
        /* Fixed height for uniformity */
        object-fit: cover;
        border-radius: 8px;
    }

    .prev,
    .next {
        cursor: pointer;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        padding: 16px;
        color: white;
        font-weight: bold;
        font-size: 18px;
        transition: background-color 0.3s ease;
        user-select: none;
        background-color: rgba(0, 0, 0, 0.3);
        border-radius: 3px;
    }

    .prev {
        left: 10px;
    }

    .next {
        right: 10px;
    }

    .prev:hover,
    .next:hover {
        background-color: rgba(0, 0, 0, 0.6);
    }

    .related-services,
    .reviews {
        margin: 20px 0;
    }

    .related-services h3,
    .reviews h3 {
        font-size: 20px;
        color: #6c757d;
        margin-bottom: 15px;
    }

    .related-services ul,
    .reviews ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .related-services ul li,
    .reviews ul li {
        margin-bottom: 10px;
        font-size: 16px;
    }

    .footer {
        background-color: #6c757d;
        color: white;
        padding: 20px 0;
        text-align: center;
        position: relative;
        bottom: 0;
        width: 100%;
    }

    .footer h4 {
        margin: 0;
        font-size: 18px;
    }

    .footer p {
        margin: 10px 0;
        font-size: 16px;
    }

    .footer a {
        color: #fff;
        text-decoration: none;
    }

    .footer a:hover {
        text-decoration: underline;
    }

    .footer .social-media {
        margin-top: 10px;
    }

    .footer .social-media a {
        margin: 0 10px;
        font-size: 20px;
        color: #fff;
    }

    @media (max-width: 768px) {
        .container {
            flex-direction: column;
        }

        .service-main {
            margin-bottom: 20px;
        }

        .carousel-slide img {
            height: 250px;
            /* Adjust height for smaller screens */
        }

        .prev,
        .next {
            padding: 10px;
            font-size: 16px;
        }
    }

    body {
        font-family: 'Arial', sans-serif;
        background-color: #f5f5f5;
        color: #333;
        margin: 0;
        padding: 0;
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 0 15px;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .service-main {
        flex: 2 1 600px;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .form-container {
        margin-top: 20px;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .form-container h3 {
        font-size: 20px;
        color: #6c757d;
        margin-bottom: 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        font-size: 16px;
        color: #333;
    }

    .form-group input[type="text"],
    .form-group textarea,
    .form-group input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
    }

    .form-group textarea {
        resize: vertical;
    }

    .form-group input[type="submit"] {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .form-group input[type="submit"]:hover {
        background-color: #FF7849;
    }
    </style>
</head>

<body>
    <?php include "Layout/header.php"; ?>


    <main class="container">
        <!-- Sidebar Section -->
        <div class="sidebar-container">
            <aside class="service-details">
                <!-- Existing content -->
                <section>
                    <h3>User Details</h3>
                    <ul>
                        <li>Email: <span><?php echo $user->getEmail(); ?></span></li>
                        <li>Phone Number: <span><?php echo $user->getPhoneNumber();?></span></li>
                        <li>Service Price Starts From: <span><?php echo $post['budget'];?></span></li>
                        <li>Delivery Time: <span><?php echo $post['delivery_time'];?></span></li>
                    </ul>
                </section>

                <section class="seller-info">
                    <h4>User Information</h4>
                    <a href="profile.php?id=<?php echo $post['user_id'];?>">
                        <img src="<?php echo $post['userImageUrl'];?>" alt="<?php echo $post['description'];?>"
                            class="seller-image">
                    </a>
                    <p> <?php echo $FullName?></p>
                    <button class="contact-button" aria-label="Contact Seller">Contact Me</button>
                </section>
            </aside>

            <!-- New Aside Section -->
            <aside class="request-task">
                <h3>Request the Task</h3>
                <button id="requestButton" class="request-button" aria-label="Request Task"
                    onclick="requestTask()">Request Task</button>
                <button class="btn btn-secondary" onclick="goBack()" aria-label="Go Back">Back</button>

            </aside>
        </div>

        <!-- Main Content Section -->
        <article class="service-main">
            <section class="image-carousel">
                <?php 
                
                if (isset($post['images']) && is_array($post['images'])) {

    foreach ($post['images'] as $image) {

         echo '<div class="carousel-slide">
                    <img src="' . htmlspecialchars($image['image_url']) . '" alt="' . htmlspecialchars($image['descriptionPostImages']) . '">
                </div>';
    }
}
?>
                <button class="prev" onclick="plusSlides(-1)" aria-label="Previous Slide">&#10094;</button>
                <button class="next" onclick="plusSlides(1)" aria-label="Next Slide">&#10095;</button>
            </section>

            <!-- Related Services Section -->
            <section class="related-services">
                <h3><strong><?php echo $post['subtitle'];?></strong></h3>
                <h3>Description Task</h3>
                <p><?php echo $post['description'];?></p>
            </section>

        </article>
        <article class="service-main">
            <section class="image-carousel">
                <?php 
        if (isset($post['images']) && is_array($post['images'])) {
            foreach ($post['images'] as $image) {
                echo '<div class="carousel-slide">
                        <img src="' . htmlspecialchars($image['image_url']) . '" alt="' . htmlspecialchars($image['descriptionPostImages']) . '">
                      </div>';
            }
        }
        ?>
                <button class="prev" onclick="plusSlides(-1)" aria-label="Previous Slide">&#10094;</button>
                <button class="next" onclick="plusSlides(1)" aria-label="Next Slide">&#10095;</button>
            </section>



            <section class="image-carousel">
                <!-- Existing Image Carousel Code -->
                <?php 
                if (isset($post['images']) && is_array($post['images'])) {
                    foreach ($post['images'] as $image) {
                        echo '<div class="carousel-slide">
                                <img src="' . htmlspecialchars($image['image_url']) . '" alt="' . htmlspecialchars($image['descriptionPostImages']) . '">
                            </div>';
                    }
                }
                ?>
                <button class="prev" onclick="plusSlides(-1)" aria-label="Previous Slide">&#10094;</button>
                <button class="next" onclick="plusSlides(1)" aria-label="Next Slide">&#10095;</button>
            </section>
            <?php if(isset($Task)){?>
            <?php if($Task->getStatus() !== 'completed'){?>
            <h3>Add New Project</h3>
            <form action="uploadTask.php?task_id=<?= urlencode($task_id)?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Project Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Project Description</label>
                    <textarea id="description" name="description" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label for="files">Upload Files</label>
                    <input type="file" name="files[]" multiple>
                </div>
                <div class="form-group">
                    <input type="submit" value="Add Project">
                </div>
            </form>
         
            <?php } else { ?>
            <div class="alert alert-info">
                <p>This task is marked as <strong>completed</strong>. No new projects can be added.</p>
            </div>
            <?php } ?>
   <?php } ?>

            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
                integrity="sha384-oBqDVmMz4fnFO9N6AEb+H7LzKftHTFzpV7C9PPEPB2A7/h1Zp5PQUJ0T8C64d1g4O"
                crossorigin="anonymous">
            </script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
                integrity="sha384-pP0sE89aGzO7oVPI9Z6j72X6UVa0sA2D9FPr6u7doxT2k5f5jI6MD6tT9sDs8pI2z"
                crossorigin="anonymous">
            </script>
            <script>
            let slideIndex = 0;
            showSlides();

            function plusSlides(n) {
                slideIndex += n;
                showSlides();
            }

            function showSlides() {
                let slides = document.querySelectorAll('.carousel-slide');
                if (slideIndex >= slides.length) {
                    slideIndex = 0;
                }
                if (slideIndex < 0) {
                    slideIndex = slides.length - 1;
                }
                slides.forEach((slide, index) => {
                    slide.style.display = index === slideIndex ? 'block' : 'none';
                });
            }

            function goBack() {
                window.history.back();
            }

            document.addEventListener('DOMContentLoaded', (event) => {
                const requestButton = document.getElementById('requestButton');

            });

            function requestTask() {
                const sender_user_id = <?php echo $_SESSION['user_id']; ?>;
                const receiver_user_id = <?php echo $post['user_id']?>;
                const postId = <?php echo $post['post_id']; ?>; // Pass post_id from PHP to JavaScript
                const message = "A task has been requested";
                const link = "/tasks";

                fetch('/TechLine/router.php/notifications/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            sender_id: sender_user_id,
                            receiver_id: receiver_user_id,
                            post_id: postId, // Include post_id in the request
                            message: message,
                            link: link
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response Data:', data);
                        if (data.status === 'success') {
                            alert('Notification created successfully!');
                            document.getElementById('requestButton').disabled = true;
                            document.getElementById('requestButton').textContent = 'Task Requested';
                        } else {
                            alert('Failed to create duplicate notification.');
                        }
                    })
                    .catch(error => {
                        console.error('Fetch Error:', error);
                        alert('An error occurred while creating the notification.');
                    });
            }
            </script>
</body>

</html>