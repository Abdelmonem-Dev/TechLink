<?php




include_once __DIR__ . '/../Models/users.php';

class UserController
{
    
    public static function getUsers(string $query, array $parameters)
    {
        return User::getUsers( $query,  $parameters);
    } 
    public static function getByEmail($email)
    {
        return User::getByEmail($email);
    } 

    public static function FetchByUserID($userID,$query)
    {
        return User::getByUserID($userID,$query);
    }
    public static function FetchByUserID1($userID)
    {
        return User::getByUserID1($userID);
    }

    public static function LogIn($email, $pass)
    {
        return User::getByEmail($email);
    }

    public static function SignUp($newUser)
    {
        return User::UserCreate($newUser);
    }

    public static function Delete($userID)
    {
        return User::UserDelete($userID);
    }

    public static function Update($user)
    {
        return User::UserUpdate($user);
    }




    

    
    public static function addNotification($senderUserId, $receiverUserId, $postId, $message, $link = null) {
        return User::addNotification($senderUserId, $receiverUserId, $postId, $message, $link);
    }
    
    public static function getNotifications($receiverUserId) {
        return User::getNotifications($receiverUserId);
    }
    
    public static function markAsRead($notificationId) {
        return User::markAsRead($notificationId);
    }
    public static function checkTaskRequest($userId, $postId) {
        return User::hasRequestedTask($userId, $postId);
    }
    public static function getUnreadCount($receiverUserId) {
        return User::getUnreadCount($receiverUserId);
    }
    public static function getUnread($receiverUserId) {
        return User::getUnread($receiverUserId);
    }
    public static function isApprove($notificationId) {
        return User::isApprove($notificationId);
    }
    public static function deleteNotification($notificationId) {
        return User::deleteNotification($notificationId);
    }
    public static function sendApprovalNotificationToFreelancer($notificationId) {
        return User::sendApprovalNotificationToFreelancer($notificationId);
    }
    
    

    public static function handleAddNotification() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $senderUserId = $_POST['sender_id'] ?? null;
            $receiverUserId = $_POST['receiver_id'] ?? null; // Get receiver_user_id from POST data
            $postId = $_POST['post_id'] ?? null; 
            $message = $_POST['message'] ?? '';
            $link = $_POST['link'] ?? null;
    
            // Log received data for debugging
            error_log("Received POST data - Sender User ID: $senderUserId, Receiver User ID: $receiverUserId, Post ID: $postId, Message: $message, Link: $link");
    
            if ($senderUserId && $receiverUserId && $postId && $message) {
                try {
                    $result = self::addNotification($senderUserId, $receiverUserId, $postId, $message, $link);
                    // Log the result of addNotification
                    error_log("Add Notification Result: " . ($result ? 'Success' : 'Failure'));
    
                    if ($result) {
                        echo json_encode(['status' => 'success', 'message' => 'Notification added successfully.']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to add notification.']);
                    }
                } catch (Exception $e) {
                    error_log("Exception: " . $e->getMessage());
                    echo json_encode(['status' => 'error', 'message' => 'Exception occurred: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
        }
    }
    private static function handleError($message, $code = 400) {
        http_response_code($code);
        echo json_encode(['status' => 'error', 'message' => $message]);
        exit;
    }
    
    public static function handleGetNotifications() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $userId = $_GET['user_id'] ?? null;

            if ($userId) {
                $notifications = self::getNotifications($userId);
                echo json_encode(['status' => 'success', 'data' => $notifications]);
            } else {
                self::handleError('User ID is required.');
            }
        } else {
            self::handleError('Invalid request method.', 405);
        }
    }

    public static function handleMarkAsRead() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notificationId = $_POST['notification_id'] ?? null;

            if ($notificationId) {
                $result = self::markAsRead($notificationId);
                if ($result) {
                    echo json_encode(['status' => 'success', 'message' => 'Notification marked as read.']);
                } else {
                    self::handleError('Failed to mark notification as read.');
                }
            } else {
                self::handleError('Notification ID is required.');
            }
        } else {
            self::handleError('Invalid request method.', 405);
        }
    }
    public static function approveNotification($notificationId) {
        return User::approveNotification($notificationId);
    }
    
    public static function rejectNotification($notificationId) {
        return User::rejectNotification($notificationId);
    }
    
    public static function handleApproveNotification() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notificationId = $_POST['notification_id'] ?? null;
    
            if ($notificationId) {
                $result = self::approveNotification($notificationId);
                if ($result) {
                    echo json_encode(['status' => 'success', 'message' => 'Notification approved.']);
                } else {
                    self::handleError('Failed to approve notification.');
                }
            } else {
                self::handleError('Notification ID is required.');
            }
        } else {
            self::handleError('Invalid request method.', 405);
        }
    }
    
    public static function handleRejectNotification() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notificationId = $_POST['notification_id'] ?? null;
    
            if ($notificationId) {
                $result = self::rejectNotification($notificationId);
                if ($result) {
                    echo json_encode(['status' => 'success', 'message' => 'Notification rejected.']);
                } else {
                    self::handleError('Failed to reject notification.');
                }
            } else {
                self::handleError('Notification ID is required.');
            }
        } else {
            self::handleError('Invalid request method.', 405);
        }
    }
    


}
?>
