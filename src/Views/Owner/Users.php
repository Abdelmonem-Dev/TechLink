<?php 
    session_start(); // Ensure session is started
    $isAuthenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users and Freelancers</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link your external CSS -->
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
        transition: background-color 0.3s, color 0.3s;
    }

    /* Light and Dark Mode Variables */
    :root {
        --bg-color: #121212;
        --text-color: #E0E0E0;
        --card-bg-color: #1E1E1E;
        --border-color: #333;
        --navbar-bg: #1E1E1E;
        --sidebar-bg: #1E1E1E;
        --primary-color: #FF5722;
        --hover-color: #FF7849;
        --active-bg: #333;
        --button-bg: #FF5722;
    }

    body.light-mode {
        --bg-color: #F5F5F5;
        --text-color: #333;
        --card-bg-color: #FFFFFF;
        --border-color: #DDD;
        --navbar-bg: #FFFFFF;
        --sidebar-bg: #FFFFFF;
        --primary-color: #FF5722;
        --hover-color: #FF7849;
        --active-bg: #EFEFEF;
        --button-bg: #FF5722;
    }

    body {
        background-color: var(--bg-color);
        color: var(--text-color);
    }

    a {
        text-decoration: none;
        color: var(--text-color);
    }

    /* Navbar */
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: var(--navbar-bg);
        padding: 10px 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .navbar .left {
        display: flex;
        align-items: center;
    }

    .navbar .logo {
        margin-left: 100px;
        font-size: 24px;
        font-weight: bold;
        color: var(--primary-color);
        margin-right: 250px;
    }

    .navbar .search-bar {
        position: relative;
        width: 100%;
    }

    .navbar .search-bar input {
        width: 300px;
        padding: 8px 12px;
        background-color: var(--card-bg-color);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        color: var(--text-color);
        outline: none;
    }

    .navbar .right {
        display: flex;
        align-items: center;
    }

    .navbar .profile-dropdown,
    .navbar .notifications {
        color: var(--text-color);
        margin-right: 20px;
        cursor: pointer;
    }

    .navbar .logout-btn {
        padding: 8px 15px;
        background-color: var(--button-bg);
        border: none;
        color: white;
        border-radius: 20px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .navbar .logout-btn:hover {
        background-color: var(--hover-color);
    }

    /* Sidebar */
    .sidebar {
        width: 250px;
        height: 100vh;
        background-color: var(--sidebar-bg);
        position: fixed;
        top: 0;
        left: 0;
        padding: 20px 0;
        display: flex;
        flex-direction: column;
        align-items: start;
        transition: width 0.3s;
    }

    .sidebar.collapsed {
        width: 60px;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        padding: 20px 0px;
        width: 100%;
        color: var(--text-color);
        transition: background-color 0.3s;
    }

    .sidebar a:hover {
        background-color: var(--hover-color);
    }

    .sidebar .icon {
        padding: 15px;
        margin-right: 15px;
        font-size: 18px;
        transition: margin 0.3s;
    }

    .sidebar.collapsed .icon {
        margin-right: 0;
        text-align: center;
        width: 100%;
    }

    .sidebar span.label {
        transition: opacity 0.3s, transform 0.3s;
    }

    .sidebar.collapsed span.label {
        opacity: 0;
        transform: translateX(-20px);
    }

    .sidebar-toggle {
        margin-left: auto;
        margin-right: 20px;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--primary-color);
    }


    /* Main Content */
    .main-content {
        margin-left: 250px;
        padding: 20px;
        transition: margin-left 0.3s;
    }

    .main-content.collapsed {
        margin-left: 60px;
    }

    .main-content .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .main-content .header h1 {
        margin: 0;
    }

    .main-content .header button {
        padding: 10px 20px;
        background-color: var(--primary-color);
        border: none;
        color: white;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .main-content .header button:hover {
        background-color: var(--hover-color);
    }

    h2 {
        color: var(--primary-color);
        text-align: center;
        margin-bottom: 30px;
    }

    .form-section {
        background-color: var(--card-bg-color);
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        border: 1px solid var(--border-color);
    }

    .form-section h3 {
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background-color: var(--card-bg-color);
        color: var(--text-color);
    }

    button.btn {
        padding: 10px 20px;
        background-color: var(--button-bg);
        border: none;
        color: var(--text-color);
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    button.btn:hover {
        background-color: var(--hover-color);
    }

    button.btn-danger {
        background-color: #D32F2F;
    }

    button.btn-warning {
        background-color: #FFA000;
    }

    button.btn-danger:hover {
        background-color: #E64A19;
    }

    button.btn-warning:hover {
        background-color: #FFB74D;
    }

    @media (max-width: 768px) {
        .navbar .search-bar input {
            width: 100%;
        }

        .sidebar {
            width: 100%;
            position: relative;
            height: auto;
        }

        .main-content {
            margin-left: 0;
        }
    }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include 'Layout/header.php';?>

    <!-- Sidebar -->
    <?php include 'Layout/sidebar.php';?>

    <div class="main-content" id="mainContent">
        <div class="header">
            <h1>Manage Users and Freelancers</h1>
            <a href="../../Views/community.php" class="btn btn-primary">Go To Community</a>
            <button id="darkModeToggle">Toggle Dark Mode</button>
        </div>

        <!-- Add User Form -->
        <div class="form-section">
            <h3>Add New User</h3>
            <form action="manageHandler.php" method="post" class="add-user-form">
                <input type="hidden" name="type" value="user">
                <div class="form-group">
                    <label for="user_username">Username</label>
                    <input type="text" id="user_username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="user_email">Email</label>
                    <input type="email" id="user_email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="user_role">Role</label>
                    <select id="user_role" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                        <option value="freelancer">Freelancer</option>
                    </select>
                </div>
                <button type="submit" name="action" value="add" class="btn btn-primary">Add User</button>
            </form>
        </div>

        <!-- Remove User Form -->
        <div class="form-section">
            <h3>Remove User</h3>
            <form action="manageHandler.php" method="post">
                <input type="hidden" name="type" value="user">
                <div class="form-group">
                    <label for="remove_user_id">Select User</label>
                    <select id="remove_user_id" name="user_id" required>
                        <?php foreach($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['id']) ?>"><?= htmlspecialchars($user['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="action" value="delete" class="btn btn-danger">Remove User</button>
            </form>
        </div>

        <!-- Update User Form -->
        <div class="form-section">
            <h3>Update User</h3>
            <form action="manageHandler.php" method="post">
                <input type="hidden" name="type" value="user">
                <div class="form-group">
                    <label for="update_user_id">Select User</label>
                    <select id="update_user_id" name="user_id" required>
                        <?php foreach($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['id']) ?>"><?= htmlspecialchars($user['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="update_username">New Username</label>
                    <input type="text" id="update_username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="update_role">New Role</label>
                    <select id="update_role" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                        <option value="freelancer">Freelancer</option>
                    </select>
                </div>
                <button type="submit" name="action" value="update" class="btn btn-warning">Update User</button>
            </form>
        </div>
    </div>

    <!-- Dark Mode and Sidebar Toggle Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleButton = document.getElementById('darkModeToggle');
        const bodyElement = document.body;

        toggleButton.addEventListener('click', () => {
            bodyElement.classList.toggle('light-mode');
        });

        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const mainContent = document.querySelector('.main-content');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        });
    });
    </script>
</body>

</html>
