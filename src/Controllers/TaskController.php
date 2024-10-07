<?php
include __DIR__ . '/../Models/Task.php';

class TaskController
{
    // Fetch a task by its ID
    public static function fetchByTaskID(int $taskID)
    {
        return Task::fetchByID($taskID);
    }
    public static function fetchByUserAndPostID($user_id,$post_id,$freelancer_id)
    {
        return Task::fetchByUserAndPostID($user_id,$post_id,$freelancer_id);
    }
    // Create a new task
    public static function createTask($task)
    {
        return Task::create($task);
    }
    public static function addProject(int $taskId, string $title, string $description, array $fileUrls)
    {
        return Task::addProject($taskId,$title,$description,$fileUrls);
    }
    // Update an existing task
    public static function updateTask(array $taskData)
    {
        $task = Task::fetchByID($taskData['task_id']);
        if ($task === null) {
            return false; // Task not found
        }
        

        if (isset($taskData['title'])) {
            $task->setTitle($taskData['title']);
        }
        if (isset($taskData['subtitle'])) {
            $task->setSubtitle($taskData['subtitle']);
        }
        if (isset($taskData['description'])) {
            $task->setDescription($taskData['description']);
        }
        if (isset($taskData['status'])) {
            $task->setStatus($taskData['status']);
        }
        if (isset($taskData['deadline'])) {
            $task->setDeadline($taskData['deadline']);
        }

        return Task::update($task);
    }

    // Delete a task
    public static function deleteTask(int $taskID)
    {
        return Task::delete($taskID);
    }

    // Fetch all tasks
    public static function getAllTasks()
    {
        return Task::fetchAll();
    }
    public static function fetchByUserID($user_id)
    {
        return Task::fetchByUserID($user_id);
    }
    
    public static function fetchFilesByTaskID($TaskID)
    {
        return Task::fetchFilesByTaskID($TaskID);
    }
}
