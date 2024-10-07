<?php
session_start();

require_once  '../../Controllers/UserController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    function validate($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }
    
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    if (empty($email) || empty($password)) {
        header("Location: login.php?error=emptyfields");
        exit();
    }

    $newUser = UserController::LogIn($email, $password);  
    if ($newUser && password_verify($password, $newUser->getPasswordHash())) {  
        $_SESSION['user_id'] = $newUser->getUserID();
        $_SESSION['email'] = $newUser->getEmail();
        $_SESSION['account_type'] = $newUser->getAccountType();
        $_SESSION['authenticated'] = true;
        if($newUser->getAccountType() === 'owner'){
            
            header("Location: ../Owner/Dashboard.php");
        }else{
            header("Location: ../main1.php");
        }
        exit();
    } else {
        header("Location: login.php?error=invalidcredentials");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
