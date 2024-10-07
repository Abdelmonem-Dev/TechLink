<?php

session_start();

require_once '../Controllers/UserController.php';
require_once '../Controllers/PostController.php';

$isAuthenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;

if (!isset($_SESSION['user_id'])) {
    die("Error: User ID not Found");
}

$user = UserController::FetchByUserID1($_SESSION['user_id']);
$projects = PostController::getByPostID($_SESSION['user_id']);
// Uncomment these lines if you have completedTasks and messages methods available
// $completedTasks = PostController::getCompletedTasks($_SESSION['user_id']);
// $messages = PostController::getMessages($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancer Workspace</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
    /* Global Styles */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f5f5f5; /* Light background */
    color: #333; /* Dark text color */
    margin: 0;
    padding: 0;
}

a {
    text-decoration: none;
    color: #007bff; /* Primary color */
}

a.active {
    color: #0056b3; /* Active link color */
}

h1, h2, h3 {
    margin: 0 0 1rem;
    color: #333; /* Dark text color */
}

ul {
    list-style-type: none;
    padding: 0;
}

button {
    cursor: pointer;
    border: none;
}

/* Container */
.workspace-container {
    display: flex;
    height: 100vh;
}

/* Sidebar */
.sidebar {
    width: 250px;
    background-color: #343a40; /* Dark sidebar background */
    padding: 20px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
    position: relative;
}

.sidebar-header {
    margin-bottom: 2rem;
    color: #ffffff; /* Light text color */
}

.sidebar-nav ul {
    padding: 0;
}

.sidebar-nav li {
    margin-bottom: 1rem;
}

.sidebar-nav a {
    display: block;
    padding: 10px;
    border-radius: 4px;
    transition: background-color 0.3s, color 0.3s;
}

.sidebar-nav a:hover,
.sidebar-nav a.active {
    background-color: #495057; /* Hover color */
    color: #ffffff; /* Light text color */
}

/* Main Content */
.workspace-main {
    flex: 1;
    padding: 20px;
    background-color: #ffffff; /* White background */
    overflow-y: auto;
}

.workspace-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.btn {
    padding: 10px 20px;
    border-radius: 4px;
    color: #ffffff; /* Light text color */
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: #007bff; /* Primary button color */
}

.btn-primary:hover {
    background-color: #0056b3; /* Primary button hover color */
}

.btn-secondary {
    background-color: #6c757d; /* Secondary button color */
}

.btn-secondary:hover {
    background-color: #5a6268; /* Secondary button hover color */
}

/* Sections */
.complete-tasks-section,
.projects-tasks-section,
.messages-section,
.settings-section {
    margin-bottom: 2rem;
}

.tasks-list,
.projects-list,
.messages-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.task-card,
.project-card,
.message-card,
.file-card {
    background-color: #e9ecef; /* Light gray background */
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.task-card h3,
.project-card h3,
.message-card p,
.file-card p {
    margin: 0;
    color: #333; /* Dark text color */
}

</style>
<body>
<?php include "Layout/header.php"; ?>

    <div class="workspace-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Freelancer Workspace</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#complete-tasks" class="<?= ($currentSection == 'complete-tasks') ? 'active' : '' ?>"><i class="fas fa-check-circle"></i> Complete Tasks</a></li>
                    <li><a href="#projects-tasks" class="<?= ($currentSection == 'projects-tasks') ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Projects or Tasks</a></li>
                    <li><a href="#messages" class="<?= ($currentSection == 'messages') ? 'active' : '' ?>"><i class="fas fa-comments"></i> Messages</a></li>
                    <li><a href="#settings" class="<?= ($currentSection == 'settings') ? 'active' : '' ?>"><i class="fas fa-cog"></i> Settings</a></li>
                </ul>
            </nav>
        </aside>

        <main class="workspace-main">
            <header class="workspace-header">
                <div class="header-left">
                    <h1>Welcome, <?= htmlspecialchars($user->getFirstName()) ?></h1>
                </div>
                <div class="header-right">
                    <button class="btn btn-primary"><i class="fas fa-plus"></i> New Project</button>
                </div>
            </header>

            <section id="complete-tasks" class="complete-tasks-section">
                <h2>Complete Tasks</h2>
                <div class="tasks-list">
                    <!-- Uncomment the following code if you have completedTasks data -->
                    <!-- <?php foreach ($completedTasks as $task): ?> -->
                        <!-- <div class="task-card"> -->
                            <!-- <h3><?php echo htmlspecialchars($task['title']); ?></h3> -->
                            <!-- <p>Project: <?php echo htmlspecialchars($task['project_title']); ?></p> -->
                            <!-- <p>Completed On: <?php echo htmlspecialchars($task['completed_date']); ?></p> -->
                        <!-- </div> -->
                    <!-- <?php endforeach; ?> -->
                </div>
            </section>

            <section id="projects-tasks" class="projects-tasks-section">
                <h2>Projects or Tasks</h2>
                <div class="projects-list">
                    <?php foreach ($projects as $project): ?>
                        <div class="project-card">
                            <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                            <p>Status: <?php echo htmlspecialchars($project['status']); ?></p>
                            <p>Deadline: <?php echo htmlspecialchars($project['deadline']); ?></p>
                            <button class="btn btn-secondary">View Details</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section id="messages" class="messages-section">
                <h2>Messages</h2>
                <div class="messages-list">
                    <!-- Uncomment the following code if you have messages data -->
                    <!-- <?php foreach ($messages as $message): ?> -->
                        <!-- <div class="message-card"> -->
                            <!-- <p>From: <?php echo htmlspecialchars($message['sender_name']); ?></p> -->
                            <!-- <p><?php echo htmlspecialchars($message['content']); ?></p> -->
                            <!-- <button class="btn btn-secondary">Reply</button> -->
                        <!-- </div> -->
                    <!-- <?php endforeach; ?> -->
                </div>
            </section>

            <section id="settings" class="settings-section">
                <h2>Settings</h2>
                <form id="settings-form" method="POST" action="update_settings.php">
                    <div class="form-group">
                        <label for="email_notifications">Email Notifications:</label>
                    </div>
                    <div class="form-group">
                        <label for="sms_notifications">SMS Notifications:</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </section>
        </main>
    </div>

    <script>
        // Add smooth scroll behavior to sidebar links
        document.querySelectorAll('.sidebar-nav a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
                document.querySelectorAll('.sidebar-nav a').forEach(link => link.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
