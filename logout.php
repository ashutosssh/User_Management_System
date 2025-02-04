<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If no session exists, redirect directly to login
    header("Location: login.php");
    exit();
}

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_unset();
session_destroy();

// Delete session cookie if exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, 
        $params["path"], $params["domain"], 
        $params["secure"], $params["httponly"]
    );
}

// Ensure headers are not already sent before redirection
if (!headers_sent()) {
    header("Location: login.php");
    exit();
} else {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}
?>
