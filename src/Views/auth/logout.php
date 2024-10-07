<?php

session_start(); // Start the session

// Check if a session exists
if (isset($_SESSION['authenticated'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    if (session_id() !== '' || isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }

    session_destroy(); // Destroy the session data
}

// Redirect to the login page
header("Location: login.php");
exit();
?>
