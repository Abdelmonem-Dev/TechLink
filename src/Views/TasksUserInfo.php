<?php
session_start();

// Include the TaskController to fetch tasks
include_once __DIR__ . '/../Controllers/TaskController.php';

// Include UserController to fetch user data
include_once __DIR__ . '/../Controllers/UserController.php';

$user_id = $_SESSION['user_id'];
$tasks = TaskController::fetchByUserID($user_id);

$userType = $_SESSION['account_type']; // Replace with actual method to get user type
$freelancerPage = 'TaskFreelancerInfo.php';
$clientPage = 'TasksUserInfo.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
        }

        main {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff; /* Ensure table has a white background */
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Button styles */
        .view-user-button, .switch-button, .view-files-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }

        .view-user-button:hover, .switch-button:hover, .view-files-button:hover {
            background-color: #0056b3;
        }

        /* Freelancer-specific styles */
        .freelancer .view-user-button {
            background-color: #28a745; /* Different color for freelancers */
        }

        .freelancer .view-user-button:hover {
            background-color: #218838;
        }

        /* Freelancer layout adjustments */
        .freelancer .tasks-table {
            border: 2px solid #28a745; /* Green border for freelancers */
        }

        .freelancer .tasks-table th {
            background-color: #d4edda; /* Light green background for table headers */
        }

        /* User-specific styles */
        .user .view-user-button {
            background-color: #007bff; /* Blue button for users */
        }

        .user .view-user-button:hover {
            background-color: #0056b3;
        }

        /* User layout adjustments */
        .user .tasks-table {
            border: 2px solid #007bff; /* Blue border for users */
        }

        .user .tasks-table th {
            background-color: #cce5ff; /* Light blue background for table headers */
        }

        /* Switch buttons container */
        .switch-buttons {
            margin-bottom: 20px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            table {
                font-size: 14px;
            }
        }

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

.close-button {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close-button:hover,
.close-button:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
.rating {
    display: flex;
    justify-content: center;
    margin: 20px 0;
}

.star {
    font-size: 30px;
    color: #ccc;
    cursor: pointer;
}

.star:hover,
.star:hover ~ .star,
.star.selected {
    color: #f0ad4e;
}

    </style>
</head>
<body>
    <?php include "Layout/header.php"; ?>

    <header>
        <h1>Tasks List</h1>
    </header>
    <main>
        <!-- Switch buttons -->
        <?php if($userType === 'freelancer') { ?>
        <div class="switch-buttons">
            <button type="button" class="switch-button" onclick="window.location.href='<?php echo htmlspecialchars($freelancerPage, ENT_QUOTES, 'UTF-8'); ?>'">Freelancer Tasks</button>
            <button type="button" class="switch-button" onclick="window.location.href='<?php echo htmlspecialchars($clientPage, ENT_QUOTES, 'UTF-8'); ?>'">Client Tasks</button>
        </div>
        <?php } ?>

        <!-- Table to display tasks -->
        <table class="tasks-table">
            <thead>
                <tr>
                    <th>Task ID</th>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>File</th> <!-- New column for file -->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    <?php if (!empty($tasks)): ?>
        <?php foreach ($tasks as $task): ?>
            <?php 
            // Fetch files for the current task
            $tasks = TaskController::fetchByUserID($user_id);
            $files = TaskController::fetchFilesByTaskID($task->getTaskID()); 
            ?>
            <tr>
                <td><?php echo htmlspecialchars($task->getTaskID(), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($task->getTitle(), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($task->getSubtitle(), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($task->getDescription(), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($task->getStatus(), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($task->getDeadline(), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($task->getCreatedAt(), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($task->getUpdatedAt(), ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <button type="button" class="view-files-button" onclick="openModal(<?php echo $task->getTaskID(); ?>)">View Files</button>
                    
                    <!-- Modal structure -->
                    <div id="modal-<?php echo $task->getTaskID(); ?>" class="modal">
                        <div class="modal-content">
                            <span class="close-button" onclick="closeModal(<?php echo $task->getTaskID(); ?>)">&times;</span>
                            <h2>Files for Task ID: <?php echo $task->getTaskID(); ?></h2>
                            <ul>
                                <?php if ($files): ?>
                                    <?php foreach ($files as $filePath): ?>
                                        <li><a href="<?php echo htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlspecialchars(basename($filePath), ENT_QUOTES, 'UTF-8'); ?></a></li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>No files available for this task.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    
                <a href="profile.php?id=<?php echo htmlspecialchars($task->getFreelancerID(), ENT_QUOTES, 'UTF-8'); ?>">View Profile</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="10">No tasks found.</td>
        </tr>
    <?php endif; ?>
</tbody>
        </table>

        <div id="rating-modal-<?php echo $task->getTaskID(); ?>" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeRatingModal(<?php echo $task->getTaskID(); ?>)">&times;</span>
        <h2>Rate Task ID: <?php echo $task->getTaskID(); ?></h2>
        <div class="rating">
            <span class="star" data-value="5">&#9733;</span>
            <span class="star" data-value="4">&#9733;</span>
            <span class="star" data-value="3">&#9733;</span>
            <span class="star" data-value="2">&#9733;</span>
            <span class="star" data-value="1">&#9733;</span>
        </div>
        <!-- Add a text area for user comments -->
        <textarea id="comment-<?php echo $task->getTaskID(); ?>" placeholder="Write your comments here..." rows="4" style="width: 100%; margin-top: 20px;"></textarea>
        <button onclick="submitRating(<?php echo $task->getTaskID(); ?>)">Submit Rating</button>
    </div>
</div>
        

    </main>
    <script>
    function openModal(taskID) {
        document.getElementById('modal-' + taskID).style.display = 'block';
    }

    function closeModal(taskID) {
        document.getElementById('modal-' + taskID).style.display = 'none';
    }

    // Close the modal if the user clicks anywhere outside of it
    window.onclick = function(event) {
        var modals = document.getElementsByClassName('modal');
        for (var i = 0; i < modals.length; i++) {
            if (event.target == modals[i]) {
                modals[i].style.display = 'none';
            }
        }
    }
    function openModal(taskID) {
    document.getElementById('modal-' + taskID).style.display = 'block';
}

function closeModal(taskID) {
    document.getElementById('modal-' + taskID).style.display = 'none';
    openRatingModal(taskID);
}

function openRatingModal(taskID) {
    document.getElementById('rating-modal-' + taskID).style.display = 'block';
}

function closeRatingModal(taskID) {
    document.getElementById('rating-modal-' + taskID).style.display = 'none';
}

function submitRating(taskID) {
    const selectedStar = document.querySelector('#rating-modal-' + taskID + ' .star.selected');
    if (selectedStar) {
        const ratingValue = selectedStar.getAttribute('data-value');
        alert('You rated ' + ratingValue + ' stars for Task ID: ' + taskID);
        closeRatingModal(taskID);
    } else {
        alert('Please select a rating before submitting.');
    }
}

// Handle star selection within the modal scope
document.querySelectorAll('.rating').forEach(ratingElement => {
    ratingElement.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            const stars = this.parentNode.querySelectorAll('.star');
            stars.forEach(star => star.classList.remove('selected'));
            for (let i = 0; i < value; i++) {
                stars[i].classList.add('selected');
            }
        });
    });
});

// Close the modal if the user clicks anywhere outside of it
window.onclick = function(event) {
    var modals = document.getElementsByClassName('modal');
    for (var i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = 'none';
        }
    }
}

</script>

</body>
</html>
