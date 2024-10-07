<?php
session_start();
include_once '../../Controllers/UserController.php';

// Check if the user is authenticated
$isAuthenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;

if (!$isAuthenticated) {
    header('Location: login.php');
    exit();
}

$UserData = UserController::FetchByUserID1($_SESSION['user_id']);

$countryJsonFile = '../../../country.json'; // Update the path to your country.json file
$countries = json_decode(file_get_contents($countryJsonFile), true)['countries'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Settings</title>
    <style>
    /* Base Styles */
/* Base Styles */
body {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    background-color: var(--bg-color);
    color: var(--text-color);
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

/* Navbar */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: var(--navbar-bg);
    padding: 10px 20px;
    border-bottom: 1px solid var(--border-color);
}

.navbar .logo {
    font-size: 24px;
    font-weight: bold;
    color: var(--primary-color);
    flex-grow: 1;
}

.navbar .search-bar {
    position: relative;
    flex-grow: 2;
}

.navbar .search-bar input {
    width: 100%;
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
    transition: width 0.3s;
}

.sidebar.collapsed {
    width: 60px;
}

.sidebar a {
    display: flex;
    align-items: center;
    padding: 20px;
    color: var(--text-color);
    text-decoration: none;
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

/* Settings Form */
.settings-container {
    background-color: var(--card-bg-color);
    padding: 20px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
}

.settings-container h2 {
    margin-bottom: 20px;
    color: var(--primary-color);
}

.settings-container label {
    display: block;
    margin-bottom: 10px;
    color: var(--text-color);
}

.settings-container input[type="text"],
.settings-container input[type="password"],
.settings-container select {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    background-color: var(--card-bg-color);
    border: 1px solid var(--border-color);
    border-radius: 5px;
    color: var(--text-color);
}

.settings-container input[type="submit"] {
    padding: 10px 20px;
    background-color: var(--primary-color);
    border: none;
    color: white;
    border-radius: 5px;
    cursor: pointer;
}

/* Alerts */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    font-size: 16px;
    line-height: 1.5;
    position: relative;
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
}

/* Success Alert */
.alert-success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

/* Error Alert */
.alert-danger {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

/* Close Button */
.alert .close {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: inherit;
}

/* Hover Effect for Close Button */
.alert .close:hover {
    color: #000;
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
}

    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include 'Layout/header.php';?>
    
    <?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_SESSION['success']); ?>
        <button type="button" class="close" onclick="this.parentElement.style.display='none';">&times;</button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error']) && !empty($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_SESSION['error']); ?>
        <button type="button" class="close" onclick="this.parentElement.style.display='none';">&times;</button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

    <!-- Sidebar -->
    <?php include 'Layout/sidebar.php';?>
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="header">
            <h1>Settings</h1>
            <button id="darkModeToggle">Toggle Dark Mode</button>
        </div>

        <div class="settings-container">
            <h2>Account Settings</h2>
            <form action="settings-handler.php" method="POST">
                <!-- First Name Field -->
                <label for="first_name">First Name</label>
                <div style="display: flex; align-items: center;">
                    <input type="text" id="first_name" name="first_name"
                        value="<?php echo htmlspecialchars($UserData->getFirstName()); ?>">
                </div>

                <!-- Email Field -->
                <label for="email">Email</label>
                <div style="display: flex; align-items: center;">
                    <input type="text" id="email" name="email"
                        value="<?php echo htmlspecialchars($UserData->getEmail()); ?>">
                </div>

                <!-- Phone Number Field -->
                <label for="phone_number">Phone Number</label>
                <div style="display: flex; align-items: center;">
                    <input type="text" id="phone_number" name="phone_number"
                        value="<?php echo htmlspecialchars($UserData->getPhoneNumber()); ?>">
                </div>

                <!-- Country Field -->
                <div class="form-group">
                        <label for="country">Country: <i class="fas fa-globe"></i></label>
                        <select id="country" name="country" class="form-control" required>
                            <option value="">Select your country</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?php echo htmlspecialchars($country['code']); ?>" <?php echo ($UserData->getCountry() === $country['code']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($country['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                <!-- Current Password Field -->
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password"
                    placeholder="Enter current password" required>

                <!-- New Password Field -->
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>

                <!-- Confirm New Password Field -->
                <label for="confirm_new_password">Confirm New Password</label>
                <input type="password" id="confirm_new_password" name="confirm_new_password"
                    placeholder="Confirm new password" required>

                <input type="submit" value="Save Changes">
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>

    // Dark Mode Toggle
    document.getElementById('darkModeToggle').addEventListener('click', () => {
        document.body.classList.toggle('light-mode');
        // Save the dark mode preference in local storage
        localStorage.setItem('darkMode', document.body.classList.contains('light-mode'));
    });

    // Load the dark mode preference on page load
    document.addEventListener('DOMContentLoaded', () => {
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('light-mode');
        }
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