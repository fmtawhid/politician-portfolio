<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Database Connection (Adjust path to point to root db.php)
require_once '../db.php'; 

// Helper function to handle Image Uploads
function uploadImage($file) {
    $target_dir = "../uploads/";
    $filename = time() . '_' . basename($file["name"]);
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return "uploads/" . $filename; // Return relative path for DB
    }
    return false;
}
?>