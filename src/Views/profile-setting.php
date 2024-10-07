<?php
session_start();

require_once '../Controllers/UserController.php';
require_once '../Controllers/FreelancerController.php';
$countryJsonFile = '../../country.json'; // Update the path to your country.json file
$countries = json_decode(file_get_contents($countryJsonFile), true)['countries'];
$query = "
    SELECT *
    FROM users 
    WHERE  user_id = :user_id";


$isAuthenticated = isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;

$userId = $_SESSION['user_id'];

// Fetch user and freelancer details
if($_SESSION['account_type'] == 'freelancer'){
$UserDetails = FreelancerController::fetchByUserID($userId);
$UserData = UserController::FetchByUserID($userId ,$query);
$UserName = $UserDetails->getUserName();
}else{
    $UserData = UserController::FetchByUserID($userId ,$query);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Profile settings page to update user information.">
    <title>Profile Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
    body {
        background-color: #f8f9fa;
        color: #212529;
        font-family: Arial, sans-serif;
    }

    .container {
        margin-top: 30px;
    }

    .settings-section {
        background-color: #fff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .settings-section h2 {
        margin-bottom: 30px;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        font-size: 1.75rem;
        color: #007bff;
        text-align: center;
    }

    .form-group {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }

    .form-group label {
        font-weight: bold;
        font-size: 1rem;
        flex: 1;
        position: relative;
    }

    .form-group label .required-star {
        color: #dc3545;
        font-size: 1.25rem;
        position: absolute;
        right: 0;
        top: 0;
    }

    .form-control {
        flex: 3;
        max-width: 600px;
    }

    .edit-btn {
        flex: 0 0 auto;
        margin-left: 10px;
        margin-top: 5px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        width: 100%;
        padding: 10px;
        font-size: 1.25rem;
        margin-top: 30px;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .btn-link {
        color: #007bff;
    }

    .btn-link:hover {
        color: #0056b3;
    }

    .tooltip-inner {
        background-color: #007bff;
        color: #fff;
    }

    .tooltip-arrow {
        border-bottom-color: #007bff !important;
    }

    .warning {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 5px;
    }

    @media (max-width: 768px) {
        .form-control {
            max-width: 100%;
        }

        .btn-primary {
            font-size: 1rem;
        }

        .settings-section {
            padding: 20px;
        }
    }
    </style>
</head>

<body>

    <?php include "Layout/header.php";
    
    
if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}
    
    ?>

    <div class="container">
        <main>
            <section id="settings" class="settings-section card p-4 shadow-sm">
                <h2 class="text-center mb-4">Profile Settings</h2>
                <form id="profile-form" method="POST" action="update_profile.php">
                    <?php if($_SESSION['account_type'] == 'freelancer'){?>

                    <div class="form-group">
                        <input type="hidden" name="fromProfile" value="true">

                        <label for="username">Username: <i class="fas fa-user"></i></label>
                        <input type="text" id="username" name="username" class="form-control"
                            value="<?php echo htmlspecialchars($UserName); ?>" required minlength="3" maxlength="20"
                            pattern="[A-Za-z0-9_]+" data-toggle="tooltip" title="Enter your username" readonly>
                        <button type="button" class="btn btn-sm btn-link edit-btn">Edit</button>
                    </div>
                    <?php }?>
                    <div class="form-group">
                        <label for="first_name">First Name: <i class="fas fa-id-card"></i></label>
                        <input type="text" id="first_name" name="first_name" class="form-control"
                            value="<?php echo htmlspecialchars($UserData->getFirstName()); ?>" required readonly>
                        <button type="button" class="btn btn-sm btn-link edit-btn">Edit</button>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name: <i class="fas fa-id-card"></i></label>
                        <input type="text" id="last_name" name="last_name" class="form-control"
                            value="<?php echo htmlspecialchars($UserData->getLastName()); ?>" required readonly>
                        <button type="button" class="btn btn-sm btn-link edit-btn">Edit</button>
                    </div>

                    <div class="form-group">
                        <label for="email">Email: <i class="fas fa-envelope"></i></label>
                        <input type="email" id="email" name="email" class="form-control"
                            value="<?php echo htmlspecialchars($UserData->getEmail()); ?>" required readonly>
                        <button type="button" class="btn btn-sm btn-link edit-btn">Edit</button>
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Phone Number: <i class="fas fa-phone"></i></label>
                        <input type="text" id="phone_number" name="phone_number" class="form-control"
                            value="<?php echo htmlspecialchars($UserData->getPhoneNumber()); ?>" required readonly>
                        <button type="button" class="btn btn-sm btn-link edit-btn">Edit</button>
                    </div>

                    <div class="form-group">
                        <label for="country">Country: <i class="fas fa-globe"></i></label>
                        <select id="country" name="country" class="form-control" required>
                            <option value="">Select your country</option>
                            <?php foreach ($countries as $country): ?>
                            <option value="<?php echo htmlspecialchars($country['code']); ?>"
                                <?php echo ($UserData->getCountry() === $country['code']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($country['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-sm btn-link edit-btn">Edit</button>
                    </div>

                    <div class="form-group">
                        <label for="dob">Date of Birth: <i class="fas fa-calendar-alt"></i></label>
                        <input type="date" id="dob" name="dob" class="form-control"
                            value="<?php echo htmlspecialchars(''); ?>" required readonly>
                        <button type="button" class="btn btn-sm btn-link edit-btn">Edit</button>
                    </div>

                    <!-- Password fields -->
                    <div class="form-group mt-4">
                        <label for="current_password">Current Password: <i class="fas fa-lock"></i><span
                                class="required-star">*</span></label>
                        <input type="password" id="current_password" name="current_password" class="form-control"
                            data-toggle="tooltip" title="Enter your current password"><br>
                        <div class="warning" id="current_password-warning"></div>
                        <button type="button" class="btn btn-sm btn-link edit-btn">Edit</button>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password: <i class="fas fa-key"></i><span
                                class="required-star">*</span></label>
                        <input type="password" id="new_password" name="new_password" class="form-control"
                            data-toggle="tooltip" title="Enter your new password"><br>
                        <div class="warning" id="new_password-warning"></div>
                        <button type="button" class="btn btn-sm btn-link edit-btn">Edit</button>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password:<span class="required-star">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                            data-toggle="tooltip" title="Re-enter your new password"><br>
                        <div class="warning" id="confirm_password-warning"></div>
                        <button type="button" class="btn btn-sm btn-link edit-btn">Edit</button>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Save Changes</button>
                </form>
            </section>
        </main>
    </div>

    <footer class="text-center mt-4">
        <p>&copy; <?php echo date("Y"); ?> Your Company Name. All rights reserved.</p>
        <a href="privacy_policy.php">Privacy Policy</a> |
        <a href="terms_of_service.php">Terms of Service</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-fQ93lKR6YmeY2I1B+e1Jb/s5W/8j9s2lm/52cGZ50UgDZK29DltSC2fh7XrM/JV9" crossorigin="anonymous">
    </script>

    <script>
    // Function to enable editing on input fields when "Edit" is clicked
    document.querySelectorAll('.edit-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var inputField = this.previousElementSibling;
            inputField.removeAttribute('readonly');
            inputField.focus();
        });
    });

    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Custom validation warnings
    document.getElementById('profile-form').addEventListener('submit', function(event) {
        var isValid = true;

        // Check current password
        var currentPassword = document.getElementById('current_password');
        var currentPasswordWarning = document.getElementById('current_password-warning');
        if (!currentPassword.value.trim()) {
            currentPasswordWarning.textContent = 'Current password is required.';
            isValid = false;
        } else {
            currentPasswordWarning.textContent = '';
        }

        // Check new password
        var newPassword = document.getElementById('new_password');
        var newPasswordWarning = document.getElementById('new_password-warning');
        if (!newPassword.value.trim()) {
            newPasswordWarning.textContent = 'New password is required.';
            isValid = false;
        } else if (newPassword.value.length < 8) {
            newPasswordWarning.textContent = 'New password must be at least 8 characters.';
            isValid = false;
        } else {
            newPasswordWarning.textContent = '';
        }

        // Check confirm password
        var confirmPassword = document.getElementById('confirm_password');
        var confirmPasswordWarning = document.getElementById('confirm_password-warning');
        if (!confirmPassword.value.trim()) {
            confirmPasswordWarning.textContent = 'Please confirm your new password.';
            isValid = false;
        } else if (confirmPassword.value !== newPassword.value) {
            confirmPasswordWarning.textContent = 'Passwords do not match.';
            isValid = false;
        } else {
            confirmPasswordWarning.textContent = '';
        }

        if (!isValid) {
            event.preventDefault(); // Prevent form submission if any field is invalid
        }
    });
    </script>
</body>

</html>