<?php
session_start();

require_once '../Controllers/PostController.php';
require_once '../Controllers/FreelancerController.php';
require_once '../Controllers/UserController.php';
require_once '../../vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

// Check authentication
if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header("Location: auth/login.php"); // Redirect to login if not authenticated
    exit();
}

// Validate session variables
if (!isset($_SESSION['user_id'])) {
    die("Error: User ID not set in session.");
}
$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['email'])) {
    die("Error: User email not set in session.");
}

function validate($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function storeImageMetadataPost($id, $imageUrl, $description) {
    $imageUrl = filter_var($imageUrl, FILTER_SANITIZE_URL);
    $description = htmlspecialchars(strip_tags($description));

    try {
        $conn = DbConnection::getConnection();

        $stmt = $conn->prepare("INSERT INTO post_images (post_id, imageUrl, description) VALUES (?, ?, ?)");
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->bindParam(2, $imageUrl, PDO::PARAM_STR);
        $stmt->bindParam(3, $description, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "Image metadata stored successfully.\n";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "Error storing metadata: " . $errorInfo[2] . "\n";
        }

        DbConnection::Close();
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        echo "Error storing metadata: " . $e->getMessage() . "\n";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_Info = FreelancerController::fetchByUserID($user_id);
    $UserData = UserController::FetchByUserID1($user_id);

    if ($UserData === null) {
        die("Error: User data not found.");
    }
    $title = validate($_POST['title']);
    $subtitle = validate($_POST['subtitle']);
    $description = validate($_POST['description']);
    $budget = validate($_POST['budget']);
    $delivery_time = validate($_POST['delivery_time']); 

    if ($_SESSION['account_type'] === 'freelancer') {
        $userName = validate($user_Info->getUserName());
    } elseif($_SESSION['account_type'] === 'client' ||$_SESSION['account_type'] === 'owner') {
        $userName = validate($UserData->getFirstName() . " " . $UserData->getLastName());
    }

    if (empty($title) || empty($description)) {
        echo "Title and description are required!";
        exit();
    }

    // Create the post first
    $post = new Post();
    $post->setUserId($user_id);
    $post->setUserName($userName);
    $post->setTitle($title);
    $post->setSubtitle($subtitle);
    $post->setComments(0);
    $post->setDescription($description);
    $post->setImageUrl('');  // Placeholder for image URL
    $post->setUserImageUrl(''); // Set to a default value or user's image URL
    $post->setbudget($budget);
    $post->setdeliveryTime($delivery_time);
    $post->setPostType('general');
    
    $Post = Post::create($post);
    if ($Post) {
        $post_id = $Post->getPostID();
    } else {
        echo "There was an error creating the post. Please try again.";
        exit();
    }

    // Process and upload images
    if (isset($_FILES['PostPictures']) && count($_FILES['PostPictures']['error']) > 0) {
        $uploadDir = '../../public/uploads/post_pictures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $keyFilePath = '../../strong-keyword-431709-a9-d10d38b03536.json';
        $storage = new StorageClient([
            'keyFilePath' => $keyFilePath
        ]);

        $bucketName = 'imagephp';
        $bucket = $storage->bucket($bucketName);

        foreach ($_FILES['PostPictures']['tmp_name'] as $index => $fileTmpPath) {
            if ($_FILES['PostPictures']['error'][$index] === UPLOAD_ERR_OK) {
                $fileName = pathinfo($_FILES['PostPictures']['name'][$index], PATHINFO_FILENAME);
                $fileExtension = pathinfo($_FILES['PostPictures']['name'][$index], PATHINFO_EXTENSION);
                $uniqueFileName = $fileName . '_' . time() . '.' . $fileExtension;
                $destPath = $uploadDir . $uniqueFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $filePath = $destPath;
                    $objectName = 'post_images/' . basename($filePath);
                    $encodedObjectName = rawurlencode($objectName);

                    try {
                        if (!file_exists($filePath)) {
                            throw new Exception("File does not exist at path: $filePath");
                        }

                        $fileContents = file_get_contents($filePath);

                        if ($fileContents === false) {
                            throw new Exception("Error: Failed to read the file at path: " . $filePath);
                        }

                        $bucket->upload($fileContents, [
                            'name' => $objectName
                        ]);

                        $objectUrl = sprintf('https://storage.googleapis.com/%s/%s', $bucketName, $encodedObjectName);
                        echo "File uploaded successfully. URL: $objectUrl\n";

                        $object = $bucket->object($objectName);
                        if ($object->exists()) {
                            echo "Image successfully stored in cloud.\n";

                            if (unlink($filePath)) {
                                echo "Local file deleted successfully.\n";
                            } else {
                                echo "Error: Failed to delete the local file.\n";
                            }
                        } else {
                            throw new Exception("Error: Image not found in cloud storage.");
                        }
                        
                        $imageAnnotator = new ImageAnnotatorClient([
                            'credentials' => $keyFilePath
                        ]);

                        $response = $imageAnnotator->labelDetection($fileContents);
                        $labels = $response->getLabelAnnotations();
                        $imageAnnotator->close();
                    
                        // Process detected labels
                        if ($labels) {
                            $labelsArray = iterator_to_array($labels); // Convert RepeatedField to array
                            $detectedLabels = implode(', ', array_map(function($label) {
                                return $label->getDescription();
                            }, $labelsArray));
                        } else {
                            $detectedLabels = 'No labels found.';
                        }

                        // Store image metadata
                        storeImageMetadataPost($post_id, $objectUrl, $detectedLabels);

                    } catch (Exception $e) {
                        echo "Error uploading file: " . $e->getMessage() . "\n";
                    }
                } else {
                    echo "Error: There was an issue uploading your file.";
                }
            }
        }
    }

    // Redirect to community page after processing
    header("Location: community.php");
    exit();
}

