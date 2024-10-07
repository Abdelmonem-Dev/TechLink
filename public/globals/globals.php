<?php
include_once __DIR__ . '/../../vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;


function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $now = new DateTime();
    $past = new DateTime("@$timestamp");
    $interval = $now->diff($past);

    if ($interval->y > 0) {
        return $interval->y . ' years ago' . ($interval->y > 1 ? '' : '');
    } elseif ($interval->m > 0) {
        return $interval->m . ' months ago' . ($interval->m > 1 ? '' : '');
    } elseif ($interval->d > 0) {
        return $interval->d . ' days ago' . ($interval->d > 1 ? '' : '');
    } elseif ($interval->h > 0) {
        return $interval->h . ' hours ago' . ($interval->h > 1 ? '' : '');
    } elseif ( 59 - $interval->i > 0) {
        return 59 - $interval->i . ' minutes ago' . ((59 - $interval->i)  > 1 ? '' : '');
    } elseif (59 - $interval->s > 0) {
        return 59 - $interval->s . ' seconds ago' . (59 - $interval->s > 1 ? '' : '');
    } else {
        return 'Just now';
    }

}
function fetchPosts($searchTerm = '', $category = '', $budget = '', $deliveryTime = '') 
{
    // Base query
    $query = "
        SELECT 
            posts.post_id,
            posts.user_id,
            posts.title,
            posts.subtitle,
            posts.description,
            posts.comments,
            posts.userName,
            posts.postType,
            posts.createdAt,
            posts.budget,
            posts.delivery_time,
            post_images.imageUrl AS image_url,
            profile_images.imageUrl AS userImageUrl,
            profile_images.description AS descriptionUserImage,
            COUNT(post_likes.post_id) AS like_count
        FROM 
            posts
        LEFT JOIN 
            post_images ON posts.post_id = post_images.post_id
        LEFT JOIN 
            profile_images ON posts.user_id = profile_images.user_id
        LEFT JOIN
            post_likes ON posts.post_id = post_likes.post_id";

    $parameters = [];

    $conditions = [];

    if (!empty($searchTerm)) {
        $conditions[] = "(
            posts.userName LIKE :searchTerm OR
            posts.title LIKE :searchTerm OR 
            posts.subtitle LIKE :searchTerm OR
            posts.description LIKE :searchTerm
        )";
        $parameters['searchTerm'] = '%' . $searchTerm . '%';
    }

    if (!empty($category)) {
        $conditions[] = "(
            posts.subtitle LIKE :searchTerm
        )";
                $parameters['searchTerm'] = $category;
    }

    if (!empty($budget)) {
        $conditions[] = "(
            posts.subtitle LIKE :searchTerm
        )";
        $parameters['searchTerm'] = $category;

    }

    if (!empty($deliveryTime)) {
        $conditions[] = "(
            posts.subtitle LIKE :searchTerm
        )";
                $parameters['searchTerm'] = $deliveryTime;
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    $query .= "
        GROUP BY 
            posts.post_id, posts.user_id, posts.title, posts.subtitle, posts.description, 
            posts.comments, posts.userName, posts.postType, posts.createdAt,posts.budget,posts.delivery_time,
            post_images.imageUrl, profile_images.imageUrl, profile_images.description

        ORDER BY 
            posts.createdAt DESC";

    return PostController::getPosts($query, $parameters);
}

function fetchUsers($searchTerm = '', $email = '', $role = '', $status = '') 
{
    // Base query
    $query = "
        SELECT 
            u.user_id,
            u.first_name,
            u.last_name,
            u.email,
            u.profile_picture,
            u.bio,
            u.country,
            u.address,
            u.phone_number,
            u.password_hash,
            u.account_type,
            u.balance,
            u.is_active,
            u.is_verified,
            u.last_login,
            u.created_at,
            pi.imageUrl AS profile_image_url
        FROM 
            users u
        LEFT JOIN 
            profile_images pi ON u.user_id = pi.user_id";

    $parameters = [];

    $conditions = [];

    if (!empty($searchTerm)) {
        $conditions[] = "(
            u.first_name LIKE :searchTerm OR
            u.last_name LIKE :searchTerm OR
            u.email LIKE :searchTerm OR
            u.bio LIKE :searchTerm
        )";
        $parameters['searchTerm'] = '%' . $searchTerm . '%';
    }

    if (!empty($email)) {
        $conditions[] = "(
            u.email = :email
        )";
        $parameters['email'] = $email;
    }

    if (!empty($role)) {
        $conditions[] = "(
            u.account_type = :role
        )";
        $parameters['role'] = $role;
    }

    if (!empty($status)) {
        $conditions[] = "(
            u.is_active = :status
        )";
        $parameters['status'] = $status;
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    $query .= "
        ORDER BY 
            u.created_at DESC";

    return UserController::getUsers($query, $parameters);
}

function deleteHandler(string $table, array $conditions, array $parameters): bool
{
    $conn = null;
    try {
        if (empty($table) || empty($conditions)) {
            throw new InvalidArgumentException("Table name and conditions are required for deletion.");
        }

        // Start building the delete query
        $query = "DELETE FROM " . $table . " WHERE ";

        // Dynamically build condition clauses
        $clauses = [];
        foreach ($conditions as $column => $param) {
            $clauses[] = $column . " = :" . $param;
        }

        // Combine conditions into the query
        $query .= implode(' AND ', $clauses);

        // Prepare and execute query
        $conn = DbConnection::getConnection();
        if (!$conn) {
            error_log("Database connection failed.");
            return false;
        }

        $stmt = $conn->prepare($query);

        // Bind parameters dynamically
        foreach ($parameters as $key => $value) {
            $stmt->bindValue(':' . $key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }

        // Execute query and return result
        return $stmt->execute();

    } catch (PDOException $e) {
        error_log("PDO Error during deletion: " . $e->getMessage() . " Query: " . $query);
        return false;
    } catch (Throwable $th) {
        error_log("General Error during deletion: " . $th->getMessage() . " Query: " . $query);
        return false;
    } finally {
        if ($conn) {
            $conn = null; // Close the connection
        }
    }
}



// Function to upload the image to Google Cloud Storage
function uploadImageToCloud($filePath, $keyFilePath, $bucketName) {
    $storage = new StorageClient(['keyFilePath' => $keyFilePath]);
    $bucket = $storage->bucket($bucketName);
    $objectName = 'profile_images/' . basename($filePath);
    $fileContents = file_get_contents($filePath);

    $bucket->upload($fileContents, ['name' => $objectName]);
    if ($bucket->object($objectName)->exists()) {
        $encodedObjectName = rawurlencode($objectName);
        return sprintf('https://storage.googleapis.com/%s/%s', $bucketName, $encodedObjectName);
    } else {
        throw new Exception("Error: Image not found in cloud storage.");
    }
}

// Function to detect image labels using Google Cloud Vision API
function detectImageLabels($filePath, $keyFilePath) {
    $imageAnnotator = new ImageAnnotatorClient(['credentials' => $keyFilePath]);
    $fileContents = file_get_contents($filePath);
    
    $response = $imageAnnotator->labelDetection($fileContents);
    $labels = $response->getLabelAnnotations();
    $imageAnnotator->close();

    if ($labels) {
        return implode(', ', array_map(function ($label) {
            return $label->getDescription();
        }, iterator_to_array($labels)));
    } else {
        return 'No labels found.';
    }
}


// Function to store image metadata in the database
function storeImageMetadataProfile($userId, $imageUrl, $description) {
    $conn = DbConnection::getConnection();

    $stmt = $conn->prepare("INSERT INTO profile_images (user_id, imageUrl, description) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->bindParam(2, $imageUrl, PDO::PARAM_STR);
    $stmt->bindParam(3, $description, PDO::PARAM_STR);

    if (!$stmt->execute()) {
        $errorInfo = $stmt->errorInfo();
        throw new Exception("Error storing metadata: " . $errorInfo[2]);
    }
}

function UpdatestoreImageMetadataProfile($userId, $imageUrl, $description) {
    $conn = DbConnection::getConnection();

    $stmt = $conn->prepare("UPDATE profile_images SET imageUrl = ?, description = ? WHERE user_id = ?");
    $stmt->bindParam(1, $imageUrl, PDO::PARAM_STR);
    $stmt->bindParam(2, $description, PDO::PARAM_STR);
    $stmt->bindParam(3, $userId, PDO::PARAM_INT);
    if (!$stmt->execute()) {
        $errorInfo = $stmt->errorInfo();
        throw new Exception("Error storing metadata: " . $errorInfo[2]);
    }
}
// Function to delete the local image file after successful upload
function deleteLocalImage($filePath) {
    if (file_exists($filePath)) {
        if (!unlink($filePath)) {
            echo "Error deleting the local file.\n";
        }
    } else {
        echo "Local file does not exist.\n";
    }
}


// Function to delete the old image from Google Cloud Storage
function deleteOldImageFromCloud($oldObjectUrl, $keyFilePath, $bucketName) {
    try {
        // Parse the object name from the old object URL
        $oldObjectName = parseObjectNameFromUrl($oldObjectUrl);
        
        // Call your cloud storage API to delete the object
        deleteImageFromCloud($oldObjectName, $keyFilePath, $bucketName);
        
        echo "Old image deleted successfully.";
    } catch (Exception $e) {
        echo "Error deleting old image: " . $e->getMessage();
    }
}
function parseObjectNameFromUrl($url) {
    $parts = parse_url($url);
    return ltrim($parts['path'], '/'); // This will give you the object name like 'profile_images/yourimage.jpg'
}
function deleteImageFromCloud($objectName, $keyFilePath, $bucketName) {
    $storage = new StorageClient([
        'keyFilePath' => $keyFilePath
    ]);

    $bucket = $storage->bucket($bucketName);
    $object = $bucket->object($objectName);
    
    // Delete the object
    if ($object->exists()) {
        $object->delete();
        echo "Object $objectName deleted from bucket $bucketName.";
    } else {
        throw new Exception("Object does not exist in bucket.");
    }
}

?>