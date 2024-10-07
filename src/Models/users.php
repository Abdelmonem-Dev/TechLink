<?php
include_once __DIR__. '/DbConnection.php';
class User 
{
    private $_UserID;
    private $_Email;
    private $_PasswordHash;
    private $_FirstName;
    private $_LastName;
    private $_ProfilePicture;
    private $_Bio;
    private $_Country;
    private $_Address;
    private $_PhoneNumber;
    private $_AccountType;
    private $_Balance;
    private $_IsActive;
    private $_IsVerified;
    private $_LastLogin;
    private $_CreatedAt;
    private $_UpdatedAt;
    private $_DeletedAt;
    private $_ImageUrl;
    private $_ImageDescription;

    public static function fromDB($result)
    {
        $user = new User();
        $user->_UserID = $result['user_id'];
        $user->_Email = $result['email'];
        $user->_PasswordHash = $result['password_hash'];
        $user->_FirstName = $result['first_name'];
        $user->_LastName = $result['last_name'];
        $user->_ProfilePicture = $result['profile_picture'] ?? '';
        $user->_Bio = $result['bio'] ?? '';
        $user->_Country = $result['country'] ?? '';
        $user->_Address = $result['address'] ?? '';
        $user->_PhoneNumber = $result['phone_number'] ?? '';
        $user->_AccountType = $result['account_type'];
        $user->_Balance = $result['balance'] ?? 0;
        $user->_IsActive = $result['is_active'] ?? true;
        $user->_IsVerified = $result['is_verified'] ?? false;
        $user->_LastLogin = $result['last_login'] ?? null;
        $user->_CreatedAt = $result['created_at'] ?? null;
        $user->_UpdatedAt = $result['updated_at'] ?? null;
        $user->_DeletedAt = $result['deleted_at'] ?? null;
        $user->_ImageUrl = $result['imageUrl'] ?? '';
        $user->_ImageDescription = $result['description'] ?? '';
        return $user;
    }
    
    public static function UserCreate(User $user): bool
    {
        try {
            $conn = DbConnection::getConnection();
    
            // Check if email already exists
            $checkEmailQuery = "SELECT COUNT(*) FROM users WHERE email = :email";
            $stmt = $conn->prepare($checkEmailQuery);
            $stmt->bindParam(':email', $user->_Email);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Email already registered.');
            }
    
            // Insert user data
            $query = "INSERT INTO users (email, password_hash, first_name, last_name, profile_picture, bio, country, address, phone_number, account_type, balance, is_active, is_verified, last_login, created_at, updated_at, deleted_at) 
                      VALUES (:email, :password_hash, :first_name, :last_name, :profile_picture, :bio, :country, :address, :phone_number, :account_type, :balance, :is_active, :is_verified, :last_login, :created_at, :updated_at, :deleted_at)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':email', $user->_Email);
            $stmt->bindParam(':password_hash', $user->_PasswordHash);
            $stmt->bindParam(':first_name', $user->_FirstName);
            $stmt->bindParam(':last_name', $user->_LastName);
            $stmt->bindParam(':profile_picture', $user->_ProfilePicture);
            $stmt->bindParam(':bio', $user->_Bio);
            $stmt->bindParam(':country', $user->_Country);
            $stmt->bindParam(':address', $user->_Address);
            $stmt->bindParam(':phone_number', $user->_PhoneNumber);
            $stmt->bindParam(':account_type', $user->_AccountType);
            $stmt->bindParam(':balance', $user->_Balance);
            $stmt->bindParam(':is_active', $user->_IsActive);
            $stmt->bindParam(':is_verified', $user->_IsVerified);
            $stmt->bindParam(':last_login', $user->_LastLogin);
            $stmt->bindParam(':created_at', $user->_CreatedAt);
            $stmt->bindParam(':updated_at', $user->_UpdatedAt);
            $stmt->bindParam(':deleted_at', $user->_DeletedAt);
            
            $stmt->execute();
    
            // Get the last inserted ID
            $UserID = $conn->lastInsertId();
    
            // Insert into profile_images table
            $query = "INSERT INTO profile_images (user_id) VALUES (:user_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $UserID);
            $result = $stmt->execute();
    
            DbConnection::Close(); // Ensure the connection is closed
            return $result;
    
        } catch (Throwable $th) {
            error_log("Error creating user: " . $th->getMessage());
            return false;
        }
    }
    

    public static function UserRead(string $email, string $password)
    {
        try {
            $conn = DbConnection::getConnection();
            
            $query = "SELECT * FROM users WHERE email = :email AND password = :password";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            DbConnection::Close(); // Ensure the connection is closed
            return $user ? User::fromDB($user) : null;

        } catch (Throwable $th) {
            error_log("Error reading user: " . $th->getMessage());
            return null;
        }
    }

    public static function UserUpdate(User $user): bool
    {
        try {
            $conn = DbConnection::getConnection();
            
            $query = "UPDATE users SET
                        password_hash = :password_hash,
                        first_name = :first_name,
                        last_name = :last_name,
                        profile_picture = :profile_picture,
                        bio = :bio,
                        country = :country,
                        address = :address,
                        phone_number = :phone_number,
                        account_type = :account_type,
                        balance = :balance,
                        is_active = :is_active,
                        is_verified = :is_verified,
                        last_login = :last_login,
                        updated_at = :updated_at
                      WHERE email = :email";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':email', $user->_Email);
            $stmt->bindParam(':password_hash', $user->_PasswordHash);
            $stmt->bindParam(':first_name', $user->_FirstName);
            $stmt->bindParam(':last_name', $user->_LastName);
            $stmt->bindParam(':profile_picture', $user->_ProfilePicture);
            $stmt->bindParam(':bio', $user->_Bio);
            $stmt->bindParam(':country', $user->_Country);
            $stmt->bindParam(':address', $user->_Address);
            $stmt->bindParam(':phone_number', $user->_PhoneNumber);
            $stmt->bindParam(':account_type', $user->_AccountType);
            $stmt->bindParam(':balance', $user->_Balance);
            $stmt->bindParam(':is_active', $user->_IsActive);
            $stmt->bindParam(':is_verified', $user->_IsVerified);
            $stmt->bindParam(':last_login', $user->_LastLogin);
            $stmt->bindParam(':updated_at', $user->_UpdatedAt);
            
            $result = $stmt->execute();
            DbConnection::Close(); // Ensure the connection is closed
            return $result;

        } catch (Throwable $th) {
            error_log("Error updating user: " . $th->getMessage());
            return false;
        }
    }

    public static function UserDelete(string $email): bool
    {
        try {
            $conn = DbConnection::getConnection();
            
            $query = "DELETE FROM users WHERE email = :email";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':email', $email);
            
            $result = $stmt->execute();
            DbConnection::Close(); // Ensure the connection is closed
            return $result;

        } catch (Throwable $th) {
            error_log("Error deleting user: " . $th->getMessage());
            return false;
        }
    }
    public static function getByEmail(string $email): ?User
    {
        try {
            $conn = DbConnection::getConnection();
            
            $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            DbConnection::Close(); // Ensure the connection is closed
            return $user ? self::fromDB($user) : null;

        } catch (Throwable $th) {
            error_log("Error reading user: " . $th->getMessage());
            return null;
        }
    }
    public static function getByUserID(int $userID, string $query): ?User
    {
        try {
            $conn = DbConnection::getConnection();
            
            // Prepare and execute the query
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                return self::fromDB($userData);
            }
            return null;
            
        } catch (Throwable $th) {
            // Log the error
            error_log("Error fetching user by ID: " . $th->getMessage());
            return null;
            
        } finally {
            DbConnection::Close(); // Properly close the connection
        }
    }
    public static function getByUserID1(int $userID): ?User
    {
        try {
            $conn = DbConnection::getConnection();
            
            // SQL query with JOIN to get user data and profile image
            $query = "
                SELECT u.*, pi.imageUrl , pi.description 
                FROM users u
                LEFT JOIN profile_images pi ON u.user_id = pi.user_id
                WHERE u.user_id = :user_id
            ";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                return self::fromDB($userData);
            }
            return null;
            
        } catch (Throwable $th) {
            // Log the error
            error_log("Error fetching user by ID: " . $th->getMessage());
            return null;
            
        } finally {
        
                DbConnection::Close(); // Properly close the connection
            
        }
    }
    public static function getUsers(string $query, array $parameters): ?array
    {
        $conn = null;
        try {
            $conn = DbConnection::getConnection();
            if (!$conn) {
                error_log('Database connection failed.');
                return [];
            }
    
            $stmt = $conn->prepare($query);
    
            // Bind parameters with proper type
            foreach ($parameters as $key => $value) {
                if (is_int($value)) {
                    $stmt->bindValue(':' . $key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
                }
            }
    
            $stmt->execute();
    
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if (empty($results)) {
                error_log("No users returned from the database for query: " . $query);
                return []; // Return an empty array instead of null
            }
    
            $users = [];
            foreach ($results as $row) {
                $userId = $row['user_id'];
    
                if (!isset($users[$userId])) {
                    $users[$userId] = self::fromDB($row);
                }
            }
    
            return array_values($users); // Return users as a numerically indexed array
    
        } catch (PDOException $e) {
            error_log("PDO Error reading users: " . $e->getMessage() . " Query: " . $query);
            return [];
        } catch (Throwable $th) {
            error_log("General Error reading users: " . $th->getMessage() . " Query: " . $query);
            return [];
        } finally {
            if ($conn) {
                $conn = null; // Close the connection
            }
        }
    }
    





    

    public static function addNotification($senderUserId, $receiverUserId, $postId, $message, $link = null, $notificationType = 'action') {
        try {
            $conn = DbConnection::getConnection();
            if (!$conn) {
                throw new Exception('Database connection failed');
            }
            
            $stmt = $conn->prepare("INSERT INTO notifications (sender_user_id, receiver_user_id, post_id, notification_type, message, link) VALUES (?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([$senderUserId, $receiverUserId, $postId, $notificationType, $message, $link]);
            
            if (!$result) {
                throw new Exception('Database insert failed');
            }
    
            return $result;
        } catch (Throwable $th) {
            error_log("Error in addNotification: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::close();
        }
    }
    
    
    public static function getNotifications($receiverUserId) {
        try {
            $conn = DbConnection::getConnection();
            if (!$conn) {
                throw new Exception("Database connection failed.");
            }
    
            $stmt = $conn->prepare("SELECT * FROM notifications WHERE receiver_user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$receiverUserId]);
    
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if ($notifications === false) {
                throw new Exception("Failed to fetch notifications.");
            }
    
            return $notifications;
        } catch (Throwable $th) {
            error_log("Error in getNotifications: " . $th->getMessage());
            return [];
        } finally {
            DbConnection::close();
        }
    }
    
    
    
    
    public static function markAsRead($notificationId) {
        try {
            $conn = DbConnection::getConnection();
            $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ?");
            $result = $stmt->execute([$notificationId]);
            return $result;
        } catch (Throwable $th) {
            error_log("Error in markAsRead: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::close();
        }
    }
    
    
    public static function markAllAsRead($receiverUserId) {
        try {
            $conn = DbConnection::getConnection();
            $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE receiver_user_id = ?");
            $result = $stmt->execute([$receiverUserId]);
            return $result;
        } catch (Throwable $th) {
            error_log("Error in markAllAsRead: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::close();
        }
    }
    
    public static function getUnread($receiverUserId) {
        try {
            $conn = DbConnection::getConnection();
            $stmt = $conn->prepare("SELECT * FROM notifications WHERE receiver_user_id = ? AND is_read = 0");
            $stmt->execute([$receiverUserId]);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $notifications;
        } catch (Throwable $th) {
            error_log("Error in getUnread: " . $th->getMessage());
            return [];
        } finally {
            DbConnection::close();
        }
    }
    

    public static function getUnreadCount(int $receiverUserId): int {
        try {
            $conn = DbConnection::getConnection();
            $query = "SELECT COUNT(*) FROM notifications WHERE receiver_user_id = :receiver_user_id AND is_read = 0";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':receiver_user_id', $receiverUserId, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return (int) $count;
        } catch (Throwable $th) {
            error_log("Error in getUnreadCount: " . $th->getMessage());
            return 0;
        } finally {
            DbConnection::close();
        }
    }
    

    public static function hasRequestedTask($senderUserId, $postId) {
        try {
            $conn = DbConnection::getConnection();
            if (!$conn) {
                throw new Exception('Database connection failed');
            }
    
            $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE sender_user_id = ? AND post_id = ?");
            $stmt->execute([$senderUserId, $postId]);
            $count = $stmt->fetchColumn();
    
            return $count > 0;
        } catch (Throwable $th) {
            error_log("Error in hasRequestedTask: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::close();
        }
    }
    
    
    public static function approveNotification($notificationId) {
        try {
            $conn = DbConnection::getConnection();
            if (!$conn) {
                throw new Exception('Database connection failed');
            }
    
            $stmt = $conn->prepare("UPDATE notifications SET is_approve = 1 WHERE notification_id = ?");
            $result = $stmt->execute([$notificationId]);
    
            if (!$result) {
                throw new Exception('Failed to approve notification.');
            }
    
            return $result;
        } catch (Throwable $th) {
            error_log("Error in approveNotification: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::close();
        }
    }
    
    
    public static function rejectNotification($notificationId) {
        try {
            $conn = DbConnection::getConnection();
            if (!$conn) {
                throw new Exception('Database connection failed');
            }
    
            $stmt = $conn->prepare("UPDATE notifications SET is_approve = 0 WHERE notification_id = ?");
            $result = $stmt->execute([$notificationId]);
    
            if (!$result) {
                throw new Exception('Failed to reject notification.');
            }
    
            return $result;
        } catch (Throwable $th) {
            error_log("Error in rejectNotification: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::close();
        }
    }
    
    
    
    public static function getApprovedNotifications($receiverUserId) {
        try {
            $conn = DbConnection::getConnection();
            if (!$conn) {
                throw new Exception('Database connection failed');
            }
    
            $stmt = $conn->prepare("SELECT * FROM notifications WHERE receiver_user_id = ? AND is_approve = 1 ORDER BY created_at DESC");
            $stmt->execute([$receiverUserId]);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if ($notifications === false) {
                throw new Exception("Failed to fetch approved notifications.");
            }
    
            return $notifications;
        } catch (Throwable $th) {
            error_log("Error in getApprovedNotifications: " . $th->getMessage());
            return [];
        } finally {
            DbConnection::close();
        }
    }
    
    public static function hasApprovedNotification($senderUserId, $postId) {
        try {
            $conn = DbConnection::getConnection();
            if (!$conn) {
                throw new Exception('Database connection failed');
            }
    
            $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE sender_user_id = ? AND post_id = ? AND is_approve = 1");
            $stmt->execute([$senderUserId, $postId]);
            $count = $stmt->fetchColumn();
    
            return $count > 0;
        } catch (Throwable $th) {
            error_log("Error in hasApprovedNotification: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::close();
        }
    }
    
    public static function isApprove($notificationId) {
        try {
            $conn = DbConnection::getConnection();
            if (!$conn) {
                throw new Exception('Database connection failed');
            }
    
            $stmt = $conn->prepare("SELECT is_approve FROM notifications WHERE notification_id = ?");
            $stmt->execute([$notificationId]);
            $isApproved = $stmt->fetchColumn();
    
            if ($isApproved === false) {
                throw new Exception("Failed to check if the notification is approved.");
            }
    
            return (bool)$isApproved;
        } catch (Throwable $th) {
            error_log("Error in isApprove: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::close();
        }
    }
    
    
    public static function deleteNotification($notificationId) {
        try {
            $conn = DbConnection::getConnection();
            if (!$conn) {
                throw new Exception('Database connection failed');
            }
    
            $stmt = $conn->prepare("DELETE FROM notifications WHERE notification_id = ?");
            $result = $stmt->execute([$notificationId]);
    
            if (!$result) {
                throw new Exception('Failed to delete notification.');
            }
    
            return $result;
        } catch (Throwable $th) {
            error_log("Error in deleteNotification: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::close();
        }
    }
    

    public static function sendApprovalNotificationToFreelancer($notificationId) {
        try {
            $conn = DbConnection::getConnection();
            if (!$conn) {
                throw new Exception('Database connection failed');
            }
    
            // Fetch the original notification details to get sender and receiver information
            $stmt = $conn->prepare("SELECT sender_user_id, receiver_user_id, post_id FROM notifications WHERE notification_id = ?");
            $stmt->execute([$notificationId]);
            $notificationData = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$notificationData) {
                throw new Exception('Original notification not found.');
            }
    
            $freelancerId = $notificationData['sender_user_id']; // Assuming the freelancer is the sender
            $userId = $notificationData['receiver_user_id']; // Assuming the user is the receiver
            $postId = $notificationData['post_id'];
            
            // Create a new notification for the freelancer
            $message = "Your request for post ID {$postId} has been approved. you have a 5 days";
            $notificationType = 'approval'; // Assuming you want to set a specific type for approval notifications
            $link = "/path/to/post/{$postId}"; // Adjust this to the correct link if needed
    
            $stmt = $conn->prepare("INSERT INTO notifications (sender_user_id, receiver_user_id, post_id, notification_type, message, link, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $result = $stmt->execute([$userId, $freelancerId, $postId, $notificationType, $message, $link]);
    
            if (!$result) {
                throw new Exception('Failed to send approval notification to freelancer.');
            }
    
            return true;
        } catch (Throwable $th) {
            error_log("Error in sendApprovalNotificationToFreelancer: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::close();
        }
    }
    

    // Getter and Setter for _UserID
    public function getUserID() {
        return $this->_UserID;
    }
    public function setUserID($UserID) {
        $this->_UserID = $UserID;
    }
    // Getter and Setter for _Email
    public function getEmail() {
        return $this->_Email;
    }
    public function setEmail($Email) {
        $this->_Email = $Email;
    }

    // Getter and Setter for _PasswordHash
    public function getPasswordHash() {
        return $this->_PasswordHash;
    }
    public function setPasswordHash($PasswordHash) {
        $this->_PasswordHash = $PasswordHash;
    }

    // Getter and Setter for _FirstName
    public function getFirstName() {
        return $this->_FirstName;
    }
    public function setFirstName($FirstName) {
        $this->_FirstName = $FirstName;
    }

    // Getter and Setter for _LastName
    public function getLastName() {
        return $this->_LastName;
    }
    public function setLastName($LastName) {
        $this->_LastName = $LastName;
    }

    // Getter and Setter for _ProfilePicture
    public function getProfilePicture() {
        return $this->_ProfilePicture;
    }
    public function setProfilePicture($ProfilePicture) {
        $this->_ProfilePicture = $ProfilePicture;
    }

    // Getter and Setter for _Bio
    public function getBio() {
        return $this->_Bio;
    }
    public function setBio($Bio) {
        $this->_Bio = $Bio;
    }

    // Getter and Setter for _Country
    public function getCountry() {
        return $this->_Country;
    }
    public function setCountry($Country) {
        $this->_Country = $Country;
    }

    // Getter and Setter for _Address
    public function getAddress() {
        return $this->_Address;
    }
    public function setAddress($Address) {
        $this->_Address = $Address;
    }

    // Getter and Setter for _PhoneNumber
    public function getPhoneNumber() {
        return $this->_PhoneNumber;
    }
    public function setPhoneNumber($PhoneNumber) {
        $this->_PhoneNumber = $PhoneNumber;
    }

    // Getter and Setter for _AccountType
    public function getAccountType() {
        return $this->_AccountType;
    }
    public function setAccountType($AccountType) {
        $this->_AccountType = $AccountType;
    }

    // Getter and Setter for _Balance
    public function getBalance() {
        return $this->_Balance;
    }
    public function setBalance($Balance) {
        $this->_Balance = $Balance;
    }

    // Getter and Setter for _IsActive
    public function getIsActive() {
        return $this->_IsActive;
    }
    public function setIsActive($IsActive) {
        $this->_IsActive = $IsActive;
    }

    // Getter and Setter for _IsVerified
    public function getIsVerified() {
        return $this->_IsVerified;
    }
    public function setIsVerified($IsVerified) {
        $this->_IsVerified = $IsVerified;
    }

    // Getter and Setter for _LastLogin
    public function getLastLogin() {
        return $this->_LastLogin;
    }
    public function setLastLogin($LastLogin) {
        $this->_LastLogin = $LastLogin;
    }

    // Getter and Setter for _CreatedAt
    public function getCreatedAt() {
        return $this->_CreatedAt;
    }
    public function setCreatedAt($CreatedAt) {
        $this->_CreatedAt = $CreatedAt;
    }

    // Getter and Setter for _UpdatedAt
    public function getUpdatedAt() {
        return $this->_UpdatedAt;
    }
    public function setUpdatedAt($UpdatedAt) {
        $this->_UpdatedAt = $UpdatedAt;
    }

    // Getter and Setter for _DeletedAt
    public function getDeletedAt() {
        return $this->_DeletedAt;
    }
    public function setDeletedAt($DeletedAt) {
        $this->_DeletedAt = $DeletedAt;
    }
    
    public function getImageUrl() {
        return $this->_ImageUrl;
    }
    public function setImageUrl($ImageUrl) {
        $this->_ImageUrl = $ImageUrl;
    }
      public function getImageDescription() {
        return $this->_ImageDescription;
    }
    public function setImageDescription($ImageDescription) {
        $this->_ImageDescription = $ImageDescription;
    }
}