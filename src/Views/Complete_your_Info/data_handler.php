<?php
session_start();

// Handle form submissions based on the page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Page 1: Handle username
    if (isset($_POST['username'])) {
        $_SESSION['username'] = $_POST['username'];
        header("Location: Freelance_Experience_Question.html");
        exit();
    }
    // Page 2: Handle experience
    if (isset($_POST['experience'])) {
        $_SESSION['experience'] = $_POST['experience'];
        header("Location: Main_Services.html");
        exit();
    }

    // Page 3: Handle custom service
    if (isset($_POST['customService'])) {
        $_SESSION['customService'] = $_POST['customService'];
        header("Location: Add_Picture.html");
        exit();
    }

    // Page 4: Handle bio
    if (isset($_POST['bio'])) {
        $_SESSION['bio'] = $_POST['bio'];
        header("Location: Set_Hourly_Rate.html");
        exit();
    }
    // Page 4: Handle profile picture upload
    if (isset($_FILES['profilePicture'])) {
        // File upload handling logic
        $uploadDir = '../../../public/uploads/profile_pictures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileTmpPath = $_FILES['profilePicture']['tmp_name'];
        $fileName = pathinfo($_FILES['profilePicture']['name'], PATHINFO_FILENAME);
        $fileExtension = pathinfo($_FILES['profilePicture']['name'], PATHINFO_EXTENSION);
        
        $uniqueFileName = $fileName . '_' . time() . '.' . $fileExtension;
        $destPath = $uploadDir . $uniqueFileName;
        
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $_SESSION['profilePicture'] = $destPath;
            header("Location: Write_Bio.html");
            exit();
        } else {
            echo "Error: There was an issue uploading your file.";
        }
    }

    // Page 5: Handle rate
    if (isset($_POST['rate'])) {
        $_SESSION['rate'] = $_POST['rate'];
        header("Location: createAccount.php");
        exit();
    }
}

// If no known POST data, redirect to the default page
header("Location: username.php");
exit();
?>