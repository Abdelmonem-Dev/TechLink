<?php
session_start();

require_once '../../Controllers/PostController.php';
require_once '../../Controllers/FreelancerController.php';
require_once '../../Controllers/UserController.php';

// Authentication check
$isAuthenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$role = isset($_GET['role']) ? $_GET['role'] : '';

$users = fetchUsers($searchTerm, $role);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancer App Dashboard</title>
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
    }

    .stats-container {
        display: flex;
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background-color: var(--card-bg-color);
        padding: 20px;
        border-radius: 8px;
        flex: 1;
        text-align: center;
        transition: background-color 0.3s, color 0.3s;
        border: 1px solid var(--border-color);
    }

    .stat-card h3 {
        margin-bottom: 10px;
        color: var(--primary-color);
    }

    /* Table Styling */
    
    

    table {
        width: 100%;
        border-collapse: collapse;
        color: var(--text-color);
    }

    th,
    td {
        padding: 12px;
        border-bottom: 1px solid var(--border-color);
    }

    th {
        background-color: var(--border-color);
    }

    td {
        text-align: center;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sidebar {
            width: 80px;
        }

        .sidebar span.label {
            display: none;
        }

        .main-content {
            margin-left: 80px;
        }

        .stats-container {
            flex-direction: column;
        }

        .navbar .search-bar input {
            width: 100%;
        }
    }

    #userSearch {
        padding: 10px;
        margin-left: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        width: 410px;
        height: 25px;
    }

    .table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .table-header h2 {
        margin: 0;
        flex: 1;
    }

    form input[type="text"],
    form select,
    form button {
        margin: 0 5px;
        border: none;
        border-radius: 5px;
        outline: none;
    }

    /* Search Input */
    form input[type="text"] {
        padding: 10px;
        background-color: var(--card-bg-color);
        border: 1px solid var(--border-color);
        color: var(--text-color);
        width: 300px;
        height: 40px;
        font-size: 16px;
    }

    /* Search Button */
    form button {
        padding: 10px 15px;
        background-color: var(--button-bg);
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    form button:hover {
        background-color: var(--hover-color);
    }

    /* Select Dropdown */
    form select {
        padding: 10px;
        background-color: var(--card-bg-color);
        border: 1px solid var(--border-color);
        color: var(--text-color);
        width: 160px;
        height: 40px;
        font-size: 16px;
    }

    /* Adjust form layout on smaller screens */
    @media (max-width: 768px) {
        form {
            display: flex;
            flex-direction: column;
        }

        form input[type="text"],
        form select,
        form button {
            width: 100%;
            margin: 5px 0;
        }

        form select {
            width: 100%;
        }
    }
    </style>

</head>

<body>
    <?php include 'Layout/header.php';?>
    <?php include 'Layout/sidebar.php';?>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="header">
            <h1>Dashboard</h1>
            <a href="../../Views/community.php" class="btn btn-primary">Go To Community</a>
            <button id="darkModeToggle">Toggle Dark Mode</button>
        </div>

        <!-- Stats Section -->
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p>2,000</p>
            </div>
            <div class="stat-card">
                <h3>Active Freelancers</h3>
                <p>1,200</p>
            </div>
            <div class="stat-card">
                <h3>Completed Projects</h3>
                <p>850</p>
            </div>
        </div>

        <!-- User Management Table -->
        <div class="table-container">
            <!-- Search Form -->
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search users by username, email, or role..."
                    value="<?php echo htmlspecialchars($searchTerm); ?>">
                <select name="role">
                    <option value="">All Roles</option>
                    <option value="Freelancer" <?php echo $role == 'Freelancer' ? 'selected' : ''; ?>>Freelancer
                    </option>
                    <option value="Client" <?php echo $role == 'Client' ? 'selected' : ''; ?>>Client</option>
                </select>
                <button type="submit">Search</button>
            </form>

            <table id="userTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users): ?>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user->getUserID()); ?></td>
                        <td><?php echo htmlspecialchars($user->getFirstName()); ?></td>
                        <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                        <td><?php echo htmlspecialchars($user->getAccountType()); ?></td>
                        <td>
                            <a href="editUser.php?id=<?php echo htmlspecialchars($user->getUserID()); ?>">Edit</a> |
                            <a href="deleteHandler.php?table=users&column=user_id&id=<?php echo htmlspecialchars($user->getUserID()); ?>"
                                onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5">No users found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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