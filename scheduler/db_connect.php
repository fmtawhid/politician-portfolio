<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('DB_SERVER', 'localhost'); 
define('DB_USERNAME', 'Nomanhaque24_Nomanhaque24');
define('DB_PASSWORD', 'Noman45@@@1'); 
define('DB_NAME', 'Nomanhaque24_scheduler'); 

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    
    die("সাইটটি অ্যাক্সেস করতে সমস্যা হচ্ছে। অনুগ্রহ করে কিছুক্ষণ পর আবার চেষ্টা করুন।");
}

if (!$conn->set_charset("utf8mb4")) {
    error_log("Error loading character set utf8mb4: " . $conn->error);
    
}

?>

