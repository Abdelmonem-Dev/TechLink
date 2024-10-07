<?php
include_once __DIR__. '/DbConnection.php';

class FreelancerDetails
{
    private $_ID;
    private $_UserID;
    private $_UserName;
    private $_Bio;        // Added Bio property
    private $_HourlyRate;
    private $_Experience;
    private $_main_service;
    private $_Skills;
    private $_rating;
    private $_Availability;

    // Method to create an instance of FreelancerDetails from a database result
    public static function fromDB($result): FreelancerDetails
    {
        $details = new FreelancerDetails();
        $details->_ID = $result['id'];
        $details->_UserID = $result['user_id'];
        $details->_UserName = $result['username'] ?? '';
        $details->_Bio = $result['bio'] ?? '';        // Fetch bio from result
        $details->_HourlyRate = $result['hourly_rate'] ?? '';
        $details->_Experience = $result['experience'] ?? '';
        $details->_main_service = $result['main_service'] ?? '';
        $details->_Skills = $result['skills'] ?? '';
        $details->_rating = $result['rating'] ?? '';
        $details->_Availability = $result['availability'] ?? '';
        return $details;
    }

    // Method to create new freelancer details
    public static function create( $details): bool
    {
        try {
            $conn = DbConnection::getConnection();
            $query = "INSERT INTO freelancer_details (user_id, username, bio, hourly_rate, experience, main_service, skills, rating, availability) 
                      VALUES (:user_id, :username, :bio, :hourly_rate, :experience, :main_service, :skills, :rating, :availability)";
            $stmt = $conn->prepare($query);

            $stmt->bindValue(':user_id', $details->_UserID);
            $stmt->bindValue(':username', $details->_UserName ?? '');
            $stmt->bindValue(':bio', $details->_Bio ?? '');        // Bind bio
            $stmt->bindValue(':hourly_rate', $details->_HourlyRate);
            $stmt->bindValue(':experience', $details->_Experience);
            $stmt->bindValue(':main_service', $details->_main_service);
            $stmt->bindValue(':skills', $details->_Skills);
            $stmt->bindValue(':rating', $details->_rating);
            $stmt->bindValue(':availability', $details->_Availability ?? 'full_time');

            $result = $stmt->execute();
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("SQL Error in FreelancerDetails::create: " . $errorInfo[2]);
            }

            return $result;
        } catch (Throwable $th) {
            error_log("Exception in FreelancerDetails::create: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::Close();
        }
    }

    // Update existing record
    public static function update( $details): bool
    {
        try {
            $conn = DbConnection::getConnection();
            $query = "UPDATE freelancer_details 
                      SET username = :username, 
                          bio = :bio, 
                          hourly_rate = :hourly_rate, 
                          experience = :experience, 
                          main_service = :main_service, 
                          skills = :skills, 
                          rating = :rating, 
                          availability = :availability 
                      WHERE user_id = :user_id";
            $stmt = $conn->prepare($query);
    
            $stmt->bindValue(':user_id', $details->_UserID, PDO::PARAM_INT);
            $stmt->bindValue(':username', $details->_UserName ?? '', PDO::PARAM_STR);
            $stmt->bindValue(':bio', $details->_Bio ?? '', PDO::PARAM_STR);  
            $stmt->bindValue(':hourly_rate', $details->_HourlyRate ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':experience', $details->_Experience ?? '', PDO::PARAM_STR);
            $stmt->bindValue(':main_service', $details->_main_service ?? '', PDO::PARAM_STR);
            $stmt->bindValue(':skills', $details->_Skills ?? '', PDO::PARAM_STR);
            $stmt->bindValue(':rating', $details->_rating ?? 0, PDO::PARAM_INT);
            $stmt->bindValue(':availability', $details->_Availability ?? 'part_time', PDO::PARAM_STR);
    
            $result = $stmt->execute();
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("SQL Error in FreelancerDetails::update: " . $errorInfo[2]);
            }
    
            return $result;
        } catch (Throwable $th) {
            error_log("Exception in FreelancerDetails::update: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::Close();
        }
    }
    

    // Delete a record
    public static function delete( $userID): bool
    {
        try {
            $conn = DbConnection::getConnection();
            $query = "DELETE FROM freelancer_details WHERE user_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':user_id', $userID, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Throwable $th) {
            error_log("Exception in FreelancerDetails::delete: " . $th->getMessage());
            return false;
        } finally {
            DbConnection::Close();
        }
    }

    // Fetch a record by user ID
    public static function fetchByUserID(int $userID): ?FreelancerDetails
    {
        try {
            $conn = DbConnection::getConnection();
            $query = "SELECT * FROM freelancer_details WHERE user_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':user_id', $userID, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $details = new FreelancerDetails();
                $details->setUserID($result['user_id']);
                $details->setUserName($result['username']);
                $details->setBio($result['bio']);      // Fetch bio
                $details->setHourlyRate($result['hourly_rate']);
                $details->setExperience($result['experience']);
                $details->setMainService($result['main_service']);
                $details->setSkills($result['skills']);
                $details->setRating($result['rating']);
                $details->setAvailability($result['availability']);
                return $details;
            } else {
                error_log("No freelancer details found for user_id: " . $userID);
                return null;
            }
        } catch (Throwable $th) {
            error_log("Exception in FreelancerDetails::fetchByUserID: " . $th->getMessage());
            return null;
        } finally {
            DbConnection::Close();
        }
    }

    // Getters and Setters
    public function getID()
    {
        return $this->_ID;
    }
    public function setID($ID)
    {
        $this->_ID = $ID;
    }

    public function getUserID()
    {
        return $this->_UserID;
    }
    public function setUserID($UserID)
    {
        $this->_UserID = $UserID;
    }

    public function getUserName()
    {
        return $this->_UserName;
    }
    public function setUserName($UserName)
    {
        $this->_UserName = $UserName;
    }

    public function getBio()
    {
        return $this->_Bio;
    }
    public function setBio($Bio)
    {
        $this->_Bio = $Bio;
    }

    public function getHourlyRate()
    {
        return $this->_HourlyRate;
    }
    public function setHourlyRate($HourlyRate)
    {
        $this->_HourlyRate = $HourlyRate;
    }

    public function getExperience()
    {
        return $this->_Experience;
    }
    public function setExperience($Experience)
    {
        $this->_Experience = $Experience;
    }

    public function getMainService()
    {
        return $this->_main_service;
    }
    public function setMainService($MainService)
    {
        $this->_main_service = $MainService;
    }

    public function getSkills()
    {
        return $this->_Skills;
    }
    public function setSkills($Skills)
    {
        $this->_Skills = $Skills;
    }

    public function getRating()
    {
        return $this->_rating;
    }
    public function setRating($Rating)
    {
        $this->_rating = $Rating;
    }

    public function getAvailability()
    {
        return $this->_Availability;
    }
    public function setAvailability($Availability)
    {
        $this->_Availability = $Availability;
    }
}