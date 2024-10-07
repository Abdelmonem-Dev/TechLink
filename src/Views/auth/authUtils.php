<?php

require_once  '../../Controllers/UserController.php';

// Define session lifetime
$session_lifetime = 1800; // 30 minutes

ini_set('session.gc_maxlifetime', $session_lifetime);
ini_set('session.cookie_lifetime', $session_lifetime);
session_start();
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_lifetime)) {
    session_unset();
    session_destroy();
}

$_SESSION['last_activity'] = time();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['your_Phone']) && isset($_POST['password']) && isset($_POST['comfirm_password'])) {
    function validate($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $first_name = validate($_POST['first_name']);
    $last_name = validate($_POST['last_name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['your_Phone']); 
    $password = validate($_POST['password']);
    $confirm_password = validate($_POST['comfirm_password']);
    
    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        header("Location: signup.php?error=emptyfields");
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: signup.php?error=passwordmismatch");
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $newUser = new User();
    $newUser->setFirstName($first_name);
    $newUser->setLastName($last_name);
    $newUser->setEmail($email);
    $newUser->setPhoneNumber($phone);
    $newUser->setPasswordHash($password_hash);
    $newUser->setAccountType('client');
    
    if (UserController::SignUp($newUser)) {
        $newUser = UserController::getByEmail($email);
        $_SESSION['authenticated'] = true;        
        $_SESSION['user_id'] = $newUser->getUserID();
        $_SESSION['email'] = $newUser->getEmail();
        $_SESSION['account_type'] =  $newUser->getAccountType();
        header('Location: ../main1.php');
        exit();
    } else {
        header("Location: signup.php?error=registrationfailed");
        exit();
    }
} else {
    header("Location: signup.php");
    exit();
}
?>
