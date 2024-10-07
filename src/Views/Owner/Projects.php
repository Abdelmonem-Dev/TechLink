<?php
session_start();

require_once '../../Controllers/PostController.php';

// Authentication check
$isAuthenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$posts = fetchPosts($searchTerm);

$categories = PostController::getCategories();

// Display messages based on query parameters
$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancer App Posts</title>
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
        margin-left: 60px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .header h1 {
        margin: 0;
        font-size: 2em;
    }

    .header button {
        background-color: var(--button-bg);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1em;
        transition: background-color 0.3s;
    }

    .header button:hover {
        background-color: var(--hover-color);
    }

    /* Posts Table */
    .posts-container {
        margin-top: 20px;
    }

    .posts-table {
        width: 100%;
        border-collapse: collapse;
    }

    .posts-table th,
    .posts-table td {
        padding: 12px;
        border: 1px solid var(--border-color);
        text-align: left;
    }

    .posts-table th {
        background-color: var(--primary-color);
        color: #fff;
    }

    .posts-table tr:nth-child(even) {
        background-color: var(--card-bg-color);
    }

    .posts-table tr:hover {
        background-color: var(--hover-color);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sidebar {
            width: 80px;
        }

        .main-content {
            margin-left: 80px;
        }

        .posts-table th,
        .posts-table td {
            font-size: 0.9em;
        }
    }

    .search-filter {
        margin-bottom: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .search-filter form {
        display: flex;
        width: 100%;
        max-width: 600px;
        /* Adjust as needed */
        background-color: var(--card-bg-color);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .search-filter input[type="text"] {
        flex: 1;
        padding: 10px 15px;
        border: none;
        border-radius: 20px 0 0 20px;
        background-color: var(--card-bg-color);
        color: var(--text-color);
        font-size: 1em;
        outline: none;
    }

    .search-filter input[type="text"]::placeholder {
        color: var(--text-color);
        opacity: 0.7;
    }

    .search-filter button {
        padding: 10px 20px;
        border: none;
        border-radius: 0 20px 20px 0;
        background-color: var(--button-bg);
        color: white;
        font-size: 1em;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .search-filter button:hover {
        background-color: var(--hover-color);
    }

    /* Categories Management */
    .categories-management {
        margin-bottom: 30px;
        padding: 20px;
        background-color: var(--card-bg-color);
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .categories-management h2 {
        margin-bottom: 20px;
        font-size: 1.5em;
        color: var(--primary-color);
    }

    .add-category-form {
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-control {
        width: calc(100% - 100px);
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background-color: var(--card-bg-color);
        color: var(--text-color);
    }

    .btn-primary {
        background-color: var(--primary-color);
        border: none;
        color: white;
        padding: 10px 15px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: var(--hover-color);
    }

    .categories-management {
    margin: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.btn-primary {
    background-color: #007bff;
    border: none;
    color: white;
}

.btn-danger {
    background-color: #dc3545;
    border: none;
    color: white;
}

.btn-warning {
    background-color: #ffc107;
    border: none;
    color: black;
}

.category-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.category-name {
    margin-right: 15px;
    flex: 1;
}

.category-actions {
    display: flex;
    gap: 10px;
}

.remove-category-form,
.update-category-form {
    display: inline;
}

.update-category-form input[type="text"] {
    margin-right: 10px;
    width: 150px;
}
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

    </style>
</head>

<body>

    <!-- Navbar -->
    <?php include 'Layout/header.php';?>

    <!-- Sidebar -->
    <?php include 'Layout/sidebar.php';?>
    <div class="container">
    <!-- Main Content -->
    <div class="main-content" id="mainContent">

        <div class="header">
            <h1>Posts</h1>
            <button id="darkModeToggle">Toggle Dark Mode</button>
        </div>

        <?php
            $categories = PostController::getCategories();      
?>
<div class="categories-management">
<?php if ($message): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
    <h2>Manage Categories</h2>

    <!-- Add Category Form -->
    <div class="add-category-form">
        <form action="categoryHandler.php" method="post">
            <div class="form-group">
                <label for="newCategory" class="form-label">Add New Category</label>
                <input type="text" class="form-control" id="newCategory" name="category" placeholder="Enter new category" required>
            </div>
            <button type="submit" name="action" value="add" class="btn btn-primary">Add Category</button>
        </form>
    </div>

    <!-- Existing Categories -->
    <div class="existing-categories">
        <h3>Existing Categories</h3>
        <ul class="categories-list">
            <?php foreach ($categories as $category): ?>
            <li class="category-item">
                <span class="category-name"><?php echo htmlspecialchars($category); ?></span>
                <div class="category-actions">
                    <form action="categoryHandler.php" method="post" class="remove-category-form">
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                        <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Remove</button>
                    </form>
                    <form action="categoryHandler.php" method="post" class="update-category-form">
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                        <input type="text" name="newCategory" placeholder="New name" required>
                        <button type="submit" name="action" value="update" class="btn btn-warning btn-sm">Update</button>
                    </form>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

        <!-- Search and Filters -->
        <div class="search-filter">
            <form method="GET" action="">
                <input type="text" id="postSearch" name="search"
                    placeholder="Search posts by title, category, or status..."
                    value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button type="submit">Search</button>
            </form>
        </div>


        <!-- Posts Table -->
        <div class="posts-container">
            <table class="posts-table">
                <thead>
                    <tr>
                        <th>Post ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($post->getPostId()); ?></td>
                        <td><?php echo htmlspecialchars($post->getTitle()); ?></td>
                        <td><?php echo htmlspecialchars($post->getSubtitle()); ?></td>
                        <td><?php echo htmlspecialchars($post->getSubtitle()); ?></td>
                        <td><?php echo htmlspecialchars($post->getCreatedAt()); ?></td>
                        <td>
                            <a href="view_post.php?id=<?php echo htmlspecialchars($post->getPostId()); ?>">View</a> |
                            <a href="edit_post.php?id=<?php echo htmlspecialchars($post->getPostId()); ?>">Edit</a> |
                            <a href="delete_post.php?id=<?php echo htmlspecialchars($post->getPostId()); ?>"
                                onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dark Mode and Sidebar Toggle Scripts -->
    <script>
    // Toggle dark mode
    const toggleButton = document.getElementById('darkModeToggle');
    const bodyElement = document.body;

    toggleButton.addEventListener('click', () => {
        bodyElement.classList.toggle('light-mode');
    });
    // Toggle sidebar
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