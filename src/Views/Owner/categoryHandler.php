<?php
session_start();
require_once '../../Controllers/PostController.php';

// Handle category actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $category = $_POST['category'] ?? '';
    $newCategory = $_POST['newCategory'] ?? '';

        switch ($action) {
            case 'add':
                if (!empty($category)) {
                    PostController::addCategory($category);
                    header("Location: Projects.php?message=Category added successfully");
                } else {
                    header("Location: Projects.php?error=Category name cannot be empty");
                }
                break;
            case 'delete':
                if (!empty($category)) {
                    PostController::removeCategory($category);
                    header("Location: Projects.php?message=Category removed successfully");
                } else {
                    header("Location: Projects.php?error=Category name cannot be empty");
                }
                break;
            case 'update':
                if (!empty($category) && !empty($newCategory)) {
                    PostController::updateCategory($category, $newCategory);
                    header("Location: Projects.php?message=Category updated successfully");
                } else {
                    header("Location: Projects.php?error=Both category fields are required");
                }
                break;
            default:
                header("Location: Projects.php?error=Invalid action");
                break;
        }
        exit;
}
