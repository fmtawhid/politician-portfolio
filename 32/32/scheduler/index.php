<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Redirect to dashboard if already logged in
    header('Location: dashboard.php');
    exit;
} 
else {
    // Redirect to login page if not logged in
    header('Location: login_signup.php');
    exit;
}
?>