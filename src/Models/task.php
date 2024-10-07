<?php
include_once __DIR__ . '/DbConnection.php';

class Task
{
    private $_taskID;
    private $_postID;
    private $_userID;
    private $_freelancerID;
    private $_title;
    private $_subtitle;
    private $_description;
    private $_status;
    private $_deadline;
    private $_createdAt;
    private $_updatedAt;
    private $_filePath; // New property for file path

    // Method to create an instance of Task from a database result
    public static function fromDB($result): Task
    {
        $task = new Task();
        $task->_taskID = $result['task_id'];
        $task->_postID = $result['post_id'];
        $task->_userID = $result['user_id'];
        $task->_title = $result['title'];
        $task->_subtitle = $result['subtitle'] ?? '';
        $task->_description = $result['description'] ?? '';
        $task->_status = $result['status'] ?? '';
        $task->_deadline = $result['deadline'] ?? null;
        $task->_createdAt = $result['created_at'] ?? '';
        $task->_updatedAt = $result['updated_at'] ?? '';
        $task->_filePath = $result['file_path'] ?? ''; // Initialize file path
        return $task;
    }

    // Method to create a new task
    public static function create($task): bool
    {
        try {
            $conn = DbConnection::getConnection();
            $query = "INSERT INTO tasks (post_id, user_id,freelancer_id, status, deadline) 
                      VALUES (:post_id, :user_id,:freelancer_id, :status, :deadline)";
            $stmt = $conn->prepare($query);

            $stmt->bindValue(':post_id', $task->_postID);
            $stmt->bindValue(':user_id', $task->_userID);
            $stmt->bindValue(':freelancer_id', $task->_freelancerID);
            $stmt->bindValue(':status', $task->_status);
            $stmt->bindValue(':deadline', $task->_deadline);
            $result = $stmt->execute();
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("SQL Error in Task::create: " . $errorInfo[2]);
            }

            return $result;
        } catch (Throwable $th) {
            error_log("Exception in Task::create: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::Close();
        }
    }

    // Update existing task
    public static function update($task): bool
    {
        try {
            $conn = DbConnection::getConnection();
            $query = "UPDATE tasks 
                      SET title = :title, 
                          subtitle = :subtitle, 
                          description = :description, 
                          status = :status, 
                          deadline = :deadline,
                          file_path = :file_path
                      WHERE task_id = :task_id";
            $stmt = $conn->prepare($query);

            $stmt->bindValue(':task_id', $task->_taskID, PDO::PARAM_INT);
            $stmt->bindValue(':title', $task->_title, PDO::PARAM_STR);
            $stmt->bindValue(':subtitle', $task->_subtitle, PDO::PARAM_STR);
            $stmt->bindValue(':description', $task->_description, PDO::PARAM_STR);
            $stmt->bindValue(':status', $task->_status, PDO::PARAM_STR);
            $stmt->bindValue(':deadline', $task->_deadline, PDO::PARAM_STR);
            $stmt->bindValue(':file_path', $task->_filePath, PDO::PARAM_STR); // Bind file path

            $result = $stmt->execute();
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("SQL Error in Task::update: " . $errorInfo[2]);
            }

            return $result;
        } catch (Throwable $th) {
            error_log("Exception in Task::update: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::Close();
        }
    }

    public static function addProject($task_id, $title, $description, $files, $status = 'completed') {
        $conn = DbConnection::getConnection();
        
        try {
            // Start the transaction
            $conn->beginTransaction();
    
            // Update the tasks table
            $query = "UPDATE tasks SET title = :title, description = :description, status = :status, updated_at = NOW() 
                      WHERE task_id = :task_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':task_id', $task_id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
    
            // Handle file uploads
            $fileQuery = "INSERT INTO files (task_id, file_path, created_at) VALUES (:task_id, :file_path, NOW())";
            $fileStmt = $conn->prepare($fileQuery);
    
            foreach ($files as $file) {
                $fileStmt->bindParam(':task_id', $task_id);
                $fileStmt->bindParam(':file_path', $file);
                $fileStmt->execute();
            }
    
            // Commit the transaction
            $conn->commit();
            return true; // Return true if everything is successful
        } catch (Throwable $th) {
            if ($conn->inTransaction()) {
                // Roll back the transaction in case of an error
                $conn->rollBack();
            }
            error_log("Failed to add project: " . $th->getMessage());
            return false; // Return false if there is an error
        }
    }
    
    
    
    // Delete a task
    public static function delete($taskID): bool
    {
        try {
            $conn = DbConnection::getConnection();
            $query = "DELETE FROM tasks WHERE task_id = :task_id";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':task_id', $taskID, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Throwable $th) {
            error_log("Exception in Task::delete: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::Close();
        }
    }

    // Fetch a task by ID
    public static function fetchByID(int $taskID): ?Task
    {
        try {
            $conn = DbConnection::getConnection();
            $query = "SELECT * FROM tasks WHERE task_id = :task_id";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':task_id', $taskID, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return self::fromDB($result);
            } else {
                error_log("No task found for task_id: " . $taskID);
                return null;
            }
        } catch (Throwable $th) {
            error_log("Exception in Task::fetchByID: " . $th->getMessage());
            return null;
        } finally {
            DbConnection::Close();
        }
    }
    public static function fetchByUserID(int $UserID): ?array
{
    try {
        $conn = DbConnection::getConnection();
        $query = "SELECT * FROM tasks WHERE user_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':user_id', $UserID, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($results) {
            $tasks = [];
            foreach ($results as $result) {
                $tasks[] = self::fromDB($result);
            }
            return $tasks;
        } else {
            error_log("No tasks found for user_id: " . $UserID);
            return null;
        }
    } catch (Throwable $th) {
        error_log("Exception in Task::fetchByUserID: " . $th->getMessage());
        return null;
    } finally {
        DbConnection::Close();
    }
}
public static function fetchFilesByTaskID(int $taskID): ?array
{
    try {
        $conn = DbConnection::getConnection();
        $query = "SELECT file_path FROM files WHERE task_id = :task_id";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':task_id', $taskID, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // Fetch only the file_path column
        if ($results) {
            return $results;
        } else {
            error_log("No files found for task_id: " . $taskID);
            return null;
        }
    } catch (Throwable $th) {
        error_log("Exception in File::fetchFilePathsByTaskID: " . $th->getMessage());
        return null;
    } finally {
        DbConnection::Close();
    }
}

    public static function fetchByUserAndPostID(int $userID, int $postID,int $frelancerID): ?Task
{
    try {
        $conn = DbConnection::getConnection();
        $query = "SELECT * FROM tasks WHERE user_id = :user_id AND post_id = :post_id AND freelancer_id = :freelancer_id";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':user_id', $userID, PDO::PARAM_INT);
        $stmt->bindValue(':post_id', $postID, PDO::PARAM_INT);
        $stmt->bindValue(':freelancer_id',$frelancerID, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return self::fromDB($result);
        } else {
            error_log("No task found for user_id: " . $userID . " and post_id: " . $postID);
            return null;
        }
    } catch (Throwable $th) {
        error_log("Exception in Task::fetchByUserAndPostID: " . $th->getMessage());
        return null;
    } finally {
        DbConnection::Close();
    }
}

    // Fetch all tasks
    public static function fetchAll(): array
    {
        try {
            $conn = DbConnection::getConnection();
            $query = "SELECT * FROM tasks";
            $stmt = $conn->query($query);

            $tasks = [];
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tasks[] = self::fromDB($result);
            }

            return $tasks;
        } catch (Throwable $th) {
            error_log("Exception in Task::fetchAll: " . $th->getMessage());
            return [];
        } finally {
            DbConnection::Close();
        }
    }

    // Getters and Setters
    public function getTaskID()
    {
        return $this->_taskID;
    }
    public function setTaskID($taskID)
    {
        $this->_taskID = $taskID;
    }

    public function getPostID()
    {
        return $this->_postID;
    }
    public function setPostID($postID)
    {
        $this->_postID = $postID;
    }

    public function getUserID()
    {
        return $this->_userID;
    }
    public function setUserID($userID)
    {
        $this->_userID = $userID;
    }
    public function getfreelancerID()
    {
        return $this->_freelancerID;
    }
    public function setfreelancerID($freelancerID)
    {
        $this->_freelancerID = $freelancerID;
    }

    public function getTitle()
    {
        return $this->_title;
    }
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    public function getSubtitle()
    {
        return $this->_subtitle;
    }
    public function setSubtitle($subtitle)
    {
        $this->_subtitle = $subtitle;
    }

    public function getDescription()
    {
        return $this->_description;
    }
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    public function getStatus()
    {
        return $this->_status;
    }
    public function setStatus($status)
    {
        $this->_status = $status;
    }

    public function getDeadline()
    {
        return $this->_deadline;
    }
    public function setDeadline($deadline)
    {
        $this->_deadline = $deadline;
    }

    public function getCreatedAt()
    {
        return $this->_createdAt;
    }
    public function setCreatedAt($createdAt)
    {
        $this->_createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->_updatedAt;
    }
    public function setUpdatedAt($updatedAt)
    {
        $this->_updatedAt = $updatedAt;
    }

    public function getFilePath()
    {
        return $this->_filePath;
    }
    public function setFilePath($filePath)
    {
        $this->_filePath = $filePath;
    }
}
