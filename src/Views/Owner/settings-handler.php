<?php
session_start();
include_once '../../Controllers/UserController.php';

// Check if the user is authenticated
if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header('Location: login.php');
    exit();
}

// Get the user ID from the session
$userId = $_SESSION['user_id'];

// Initialize error and success messages
$_SESSION['error'] = $_SESSION['error'] ?? '';
$_SESSION['success'] = $_SESSION['success'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $FirstName = trim($_POST['first_name']);
    $email = trim($_POST['email']);
    $phoneNumber = trim($_POST['phone_number']);
    $country = trim($_POST['country']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_new_password'];

    // Validate required fields
    if (empty($FirstName) || empty($email) || empty($phoneNumber) || empty($country)) {
        $_SESSION['error'] = 'Please fill in all required fields.';
        header("Location: Settings.php");
        exit();
    }

    // Fetch current user data
    $userData = UserController::FetchByUserID1($userId);

    // Update user details
    $userData->setFirstName($FirstName);
    $userData->setEmail($email);
    $userData->setPhoneNumber($phoneNumber);
    $userData->setCountry($country);

    // Check if password change is requested
    if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
        if (password_verify($currentPassword, $userData->getPasswordHash())) {
            if ($newPassword === $confirmPassword) {
                // Hash the new password and update it
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $userData->setPasswordHash($hashedPassword);
            } else {
                $_SESSION['error'] = 'New password and confirm password do not match.';
            }
        } else {
            $_SESSION['error'] = 'Current password is incorrect.';
        }
    }

    if (empty($_SESSION['error'])) {
        // Save updated user details to the database
        $updateSuccessUser = UserController::Update($userData);

        if ($updateSuccessUser) {
            $_SESSION['success'] = 'Profile updated successfully!';
            header("Location: Settings.php");
            exit();
        } else {
            $_SESSION['error'] = 'An error occurred while updating your profile. Please try again.';
            header("Location: Settings.php");
            exit();
        }
    } else {
        header("Location: Settings.php");
        exit();
    }
}
?>
