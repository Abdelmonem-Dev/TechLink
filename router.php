<?php
include_once 'src/Controllers/UserController.php';

// Remove trailing slash and parse the request URI and method
$requestUri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$requestMethod = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');

// Debugging log with timestamp and additional information
error_log("[" . date('Y-m-d H:i:s') . "] Request URI: $requestUri, Method: $requestMethod");

switch ($requestUri) {
    case '/TechLine/router.php/notifications/add':
        if ($requestMethod === 'POST') {
            UserController::handleAddNotification();
        } else {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
        }
        break;

    case '/TechLine/router.php/notifications/get':
        if ($requestMethod === 'GET') {
            UserController::handleGetNotifications();
        } else {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
        }
        break;

    case '/TechLine/router.php/notifications/mark-as-read':
        if ($requestMethod === 'POST') {
            UserController::handleMarkAsRead();
        } else {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Not Found']);
        break;
}

// Centralized function for unexpected errors
function handleUnexpectedError($exception) {
    error_log("[" . date('Y-m-d H:i:s') . "] Unexpected Error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Internal Server Error']);
}

// Example of using try-catch for more complex operations
try {
    // Your code that might throw exceptions
} catch (Exception $e) {
    handleUnexpectedError($e);
}
