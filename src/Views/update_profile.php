<?php
session_start();

require_once '../Controllers/UserController.php';
require_once '../Controllers/FreelancerController.php';


if (!isset($_SESSION['user_id']) || !$_SESSION['authenticated']) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['fromProfile'])) {
    $fromProfile = $_POST['fromProfile'];
    // Your code to handle the form data
} else {
    // Handle the case where 'fromProfile' is not set
    $fromProfile = null;
}

$userId = $_SESSION['user_id'];




if ($_SERVER['REQUEST_METHOD'] === 'POST' && $fromProfile == false) {

    if (isset($_FILES['profileImageUpload'])) {

        if ($_FILES['profileImageUpload']['error'] === UPLOAD_ERR_OK) {

            $uploadDir = '../../public/uploads/profile_pictures/';
            if (!is_dir($uploadDir)) {
                // Create the directory if it does not exist
                if (!mkdir($uploadDir, 0777, true)) {
                    error_log("Failed to create upload directory.");
                    echo "Failed to create upload directory.";
                    exit();
                }
            }

            $fileTmpPath = $_FILES['profileImageUpload']['tmp_name'];
            $fileName = pathinfo($_FILES['profileImageUpload']['name'], PATHINFO_FILENAME);
            $fileExtension = strtolower(pathinfo($_FILES['profileImageUpload']['name'], PATHINFO_EXTENSION)); // Ensure extension is in lowercase

            // Validate file type
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($fileExtension, $allowedExtensions)) {
                error_log("Invalid file type: " . $fileExtension);
                echo "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
                header("Location: profile.php?upload=error&type=invalid");
                exit();
            }

            $uniqueFileName = $fileName . '_' . time() . '.' . $fileExtension;
            $destPath = $uploadDir . $uniqueFileName;

            // Attempt to move the file to the designated directory
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                error_log("File successfully moved to: " . $destPath);
                $_SESSION['profilePicture'] = $destPath;

                // Cloud Storage variables
                $keyFilePath = '../../strong-keyword-431709-a9-d10d38b03536.json';
                $bucketName = 'imagephp';
                $objectName = 'profile_images/' . basename($destPath);

                try {
                    // Upload image to cloud storage
                    $objectUrl = uploadImageToCloud($destPath, $keyFilePath, $bucketName);
                    error_log("Image uploaded to cloud storage: " . $objectUrl);

                    // Detect image labels
                    $detectedLabels = detectImageLabels($destPath, $keyFilePath);

                    // Store metadata in the database
                    UpdatestoreImageMetadataProfile($userId, $objectUrl, 'Profile Image - Detected Labels: ' . $detectedLabels);


                    if (file_exists($destPath)) {
                        unlink($destPath);
                    }


                    // Delete the old image from cloud storage if it exists
                    if (isset($_POST['oldImageUrl'])) {
                        deleteOldImageFromCloud($_POST['oldImageUrl'], $keyFilePath, $bucketName);
                    }

                    // Redirect to profile page with success message
                    header("Location: profile.php?upload=success");
                    exit();

                } catch (Exception $e) {
                    error_log("Cloud upload error: " . $e->getMessage());
                    echo "Cloud upload error: " . $e->getMessage();
                    exit();
                }
            } else {
                error_log("Failed to move uploaded file.");
                echo "Failed to move uploaded file.";
                header("Location: profile.php?upload=error&reason=move_failed");
                exit();
            }
        } else {
            error_log("File upload error detected: " . $_FILES['profileImageUpload']['error']);
            // Handle different file upload errors
            $uploadError = $_FILES['profileImageUpload']['error'];
            $errorMessage = '';
        }
    } else {
        error_log("No file detected in the upload attempt.");
        echo "No file detected. Please select a file to upload.";
        header("Location: profile.php?upload=error&reason=no_file_detected");
        exit();
    }
}


    // Handle profile update
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phoneNumber = trim($_POST['phone_number']);
    $country = trim($_POST['country']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate required fields
    if (empty($username) || empty($email) || empty($phoneNumber) || empty($country)) {
        $_SESSION['error'] = 'Please fill in all required fields.';
    } else {
        // Fetch current user data
        $userData = UserController::FetchByUserID1($userId);
        $UserDetails = FreelancerController::fetchByUserID($userId);

        // Update user details
        $UserDetails->setUserName($username);
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
                    header('Location: profile-setting.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Current password is incorrect.';
                header('Location: profile-setting.php');
                exit();
            }
        }

        if (empty($_SESSION['error'])) {
            // Save updated user details to the database
            $updateSuccessUser = UserController::Update($userData);
            $updateSuccessFreelance = FreelancerController::update($UserDetails);

            if ($updateSuccessUser && $updateSuccessFreelance) {
                $_SESSION['success'] = 'Profile updated successfully!';
                header('Location: profile-setting.php');
                exit();
            } else {
                $_SESSION['error'] = 'An error occurred while updating your profile. Please try again.';
                header('Location: profile-setting.php');
                exit();
            }
        }
    }

