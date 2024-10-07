<?php
session_start();

require_once '../../Controllers/PostController.php';
require_once '../../Controllers/FreelancerController.php';
require_once '../../Controllers/UserController.php';

// Authentication check
$isAuthenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancer App Analytics</title>
    <style>
    /* Base Styles */
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
        margin-left: 80px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    /* Analytics Cards */
    .analytics-container {
        display: flex;
        gap: 20px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }

    .analytics-card {
        background-color: var(--card-bg-color);
        padding: 20px;
        border-radius: 8px;
        flex: 1;
        min-width: 250px;
        text-align: center;
        border: 1px solid var(--border-color);
        transition: background-color 0.3s;
    }

    .analytics-card h3 {
        color: var(--primary-color);
        margin-bottom: 10px;
    }

    .analytics-chart {
        height: 200px;
        background-color: #fff;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sidebar {
            width: 80px;
        }

        .main-content {
            margin-left: 80px;
        }

        .analytics-container {
            flex-direction: column;
        }
    }
    </style>
</head>

<body>

    <!-- Navbar -->
    <?php include 'Layout/header.php';?>

    <!-- Sidebar -->
    <?php include 'Layout/sidebar.php';?>


    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="header">
            <h1>Analytics</h1>
            <button id="darkModeToggle">Toggle Dark Mode</button>
        </div>

        <!-- Analytics Cards -->
        <div class="analytics-container">
            <div class="analytics-card">
                <h3>Total Revenue</h3>
                <div class="analytics-chart">[Chart Placeholder]</div>
            </div>
            <div class="analytics-card">
                <h3>Active Projects</h3>
                <div class="analytics-chart">[Chart Placeholder]</div>
            </div>
            <div class="analytics-card">
                <h3>New Users</h3>
                <div class="analytics-chart">[Chart Placeholder]</div>
            </div>
        </div>
    </div>

    <!-- Dark Mode and Sidebar Toggle Scripts -->
    <script>
    const toggleButton = document.getElementById('darkModeToggle');
    const bodyElement = document.body;

    toggleButton.addEventListener('click', () => {
        bodyElement.classList.toggle('light-mode');
    });

    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.getElementById('mainContent');

    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    });
    </script>

</body>

</html>