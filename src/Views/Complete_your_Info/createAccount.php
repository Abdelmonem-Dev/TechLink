<?php
session_start();
require_once '../../Controllers/FreelancerController.php';

// Check session variables
if (!isset($_SESSION['profilePicture']) || !isset($_SESSION['user_id'])) {
    die("Error: Required session variables are not set.");
}

// Define variables
$keyFilePath = '../../../strong-keyword-431709-a9-d10d38b03536.json';
$bucketName = 'imagephp';
$filePath = $_SESSION['profilePicture'];
$objectName = 'profile_images/' . basename($filePath);

try {
    if (!file_exists($filePath)) {
        throw new Exception("File does not exist at path: $filePath");
    }

   
    $Path = '../../../public/uploads/profile_pictures/' . basename($filePath);

    // Step 1: Upload image to Google Cloud Storage
    $objectUrl = uploadImageToCloud($filePath, $keyFilePath, $bucketName);

    // Step 2: Detect labels using Google Cloud Vision API
    $detectedLabels = detectImageLabels($filePath, $keyFilePath);  

    // Step 3: Store image metadata
    storeImageMetadataProfile($_SESSION['user_id'], $objectUrl, 'Profile Image - Detected Labels: ' . $detectedLabels);
    
    // Step 4: Delete local image
    deleteLocalImage($Path);



} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}


// Update freelancer details
if (isset($_SESSION['user_id'])) {
    $freelancerDetails = new FreelancerDetails();
    $freelancerDetails->setUserID(validate($_SESSION['user_id']));
    $freelancerDetails->setUserName(validate($_SESSION['username'] ?? ''));
    $freelancerDetails->setExperience(validate($_SESSION['experience'] ?? ''));
    $freelancerDetails->setMainService(validate($_SESSION['customService'] ?? ''));
    $freelancerDetails->setBio(validate($_SESSION['bio'] ?? ''));
    $freelancerDetails->setHourlyRate(validate($_SESSION['rate']));

    if (FreelancerController::update($freelancerDetails)) {
        header("Location: Account_Created.html");
        exit();
    } else {
        header("Location: Set_Hourly_Rate.html?error=updatefailed");
        exit();
    }
} else {
    header("Location: username.php");
    exit();
}
// Sanitize and validate data
function validate($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}