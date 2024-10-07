<?php
session_start();

// Include the TaskController to fetch tasks
include_once __DIR__ . '/../Controllers/TaskController.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch tasks assigned to the logged-in freelancer
try {
    $tasks = TaskController::getAllTasks();
} catch (Exception $e) {
    error_log("Error fetching tasks: " . $e->getMessage());
    $tasks = []; // Initialize as empty to prevent errors in the view
}
$userType = $_SESSION['account_type']; // Replace with actual method to get user type

$freelancerPage = 'TaskFreelancerInfo.php';
$clientPage = 'TasksUserInfo.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks</title>
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
        .view-user-button, .switch-button {
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

        .view-user-button:hover, .switch-button:hover {
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
                                <?php if ($task->getFilePath()): ?>
                                    <a href="<?php echo htmlspecialchars($task->getFilePath(), ENT_QUOTES, 'UTF-8'); ?>" target="_blank">View File</a>
                                <?php else: ?>
                                    No file
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Button to view user data -->
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
    </main>
</body>

</html>
