<?php

include_once '../../vendor/autoload.php';
include_once '../Controllers/TaskController.php'; // Assuming this is where your Task class is

use Google\Cloud\Storage\StorageClient;

$Task_id = $_GET['task_id'];
$Task = TaskController::fetchByTaskID($Task_id);
$post_id = $Task->getPostID();
function uploadToGoogleCloud($bucketName, $filePath, $uploadName) {
    $storage = new StorageClient([
        'keyFilePath' => '../../strong-keyword-431709-a9-d10d38b03536.json'
    ]);
    $bucket = $storage->bucket($bucketName);
    $file = fopen($filePath, 'r');
    
    // Upload the file without any ACL options, since UBLA is enabled
    $object = $bucket->upload($file, [
        'name' => $uploadName
    ]);

    // Return the public URL of the uploaded file
    return sprintf('https://storage.googleapis.com/%s/%s', $bucketName, $uploadName);
}        

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files']) && isset($Task_id)) {

    $bucketName = 'imagephp';
    $taskId = $Task_id;
    $files = $_FILES['files'];
    $urls = [];

    for ($i = 0; $i < count($files['name']); $i++) {
        $filePath = $files['tmp_name'][$i];
        $originalName = $files['name'][$i];
        
        // Use the task ID as part of the folder structure
        $uploadName = 'task_files/' . $taskId . '/' . basename($originalName);

        $url = uploadToGoogleCloud($bucketName, $filePath, $uploadName);
        $urls[] = $url;
    }

    // Get the title and description from the form input
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = 'completed'; // Set the status to 'completed'

    // Add the project with the files, title, description, and status
    $success = TaskController::addProject($taskId, $title, $description, $urls);

    if ($success) {
    
    header("Location: showInfoPost.php?id={$post_id}&confirm=ture");
    exit();
    } else {
        echo "Failed to add the project.";
        header("Location: showInfoPost.php?confirm=false");
        exit();
    }
} else {
    echo "No files uploaded or task ID missing.";
}
