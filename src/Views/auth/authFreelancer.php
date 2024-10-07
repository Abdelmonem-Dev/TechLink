<?php

require_once '../../Controllers/UserController.php';
require_once '../../Controllers/FreelancerController.php';

$session_lifetime = 1800; // 30 minutes

ini_set('session.gc_maxlifetime', $session_lifetime);
ini_set('session.cookie_lifetime', $session_lifetime);
session_start();

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_lifetime)) {
    session_unset();
    session_destroy();
}

$_SESSION['last_activity'] = time();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    function validate($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $first_name = validate($_POST['first_name']);
    $last_name = validate($_POST['last_name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $country = validate($_POST['country']);
    $password = validate($_POST['password']);
    $confirm_password = validate($_POST['confirm_password']);

    if (empty($first_name) || empty($email) || empty($phone) || empty($country) || empty($last_name) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'All fields are required.';
        header("Location: signup.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        header("Location: signup.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match.';
        header("Location: signup.php");
        exit();
    }

    // Hash password securely
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $newUser = new User();
    $newUser->setFirstName($first_name);
    $newUser->setLastName($last_name);
    $newUser->setEmail($email);
    $newUser->setPhoneNumber($phone);
    $newUser->setCountry($country);
    $newUser->setPasswordHash($hashed_password);
    $newUser->setAccountType('freelancer');
    
    if (UserController::SignUp($newUser)) {
        
        $newUser = UserController::getByEmail($newUser->getEmail());
        $_SESSION['authenticated'] = true;
        $_SESSION['user_id'] = $newUser->getUserID();
        $_SESSION['email'] = $newUser->getEmail();
        $_SESSION['account_type'] = $newUser->getAccountType();

        $freelancerDetails = new FreelancerDetails();
        $freelancerDetails->setUserID($newUser->getUserID());  
        $freelancerDetails->setUserName($newUser->getEmail());  

        if (FreelancerController::create($freelancerDetails)) {
            header("Location: ../Complete_your_Info/UserName.html"); 
            exit();
        } else {
            $_SESSION['error'] = 'Failed to create freelancer details.';
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = 'Registration failed. Please try again.';
        header("Location: signup.php");
        exit();
    }
} else {

}
?>