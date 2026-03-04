<?php
require_once 'db_connect.php'; 


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Perform redirect ONLY if no output has been sent yet.
    if (!headers_sent()) {
        session_unset();
        session_destroy();
        header('Location: login_signup.php');
        exit;
    } else {
        error_log("Headers already sent before login check in header.php");
        die("একটি সেশন ত্রুটি ঘটেছে। অনুগ্রহ করে আবার লগইন করার চেষ্টা করুন।");
    }
}

$user_id_from_session = $_SESSION['user_id'] ?? 0;
$username_from_session = $_SESSION['username'] ?? 'ব্যবহারকারী';
$role_from_session = $_SESSION['role'] ?? 0; // Use 'role'

if ($user_id_from_session > 0) {
    // Select 'role' to ensure session is up-to-date
    $stmt = $conn->prepare("SELECT id, status, role FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id_from_session);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $status, $role); // Bind 'role'
        $stmt->fetch();

        if ($stmt->num_rows == 0 || $status != 'approved') {
             if (!headers_sent()) {
                session_unset();
                session_destroy();
                header('Location: login_signup_bangla.php?error=invalid_user');
                exit;
             } else {
                 error_log("Headers already sent before invalid user check in header_bangla.php");
                 die("অবৈধ ব্যবহারকারী সেশন। অনুগ্রহ করে আবার লগইন করুন।");
             }
        }

        $_SESSION['role'] = $role;
        $role_from_session = $role; 

        $stmt->close();
    } else {
        // Handle DB prepare error
        error_log("DB prepare error in header_bangla.php: " . $conn->error);
         die("ডাটাবেস ত্রুটি।");
    }
} else {
     if (!headers_sent()) {
        session_unset();
        session_destroy();
        header('Location: login_signup_bangla.php?error=session_expired');
        exit;
     } else {
         error_log("Headers already sent before session expired check in header_bangla.php");
         die("সেশন মেয়াদোত্তীর্ণ। অনুগ্রহ করে আবার লগইন করুন।");
     }
}

$current_page = basename($_SERVER['PHP_SELF']);


?><!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ক্যাম্পেইন শিডিউলার</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        body { font-family: 'Noto Sans Bengali', sans-serif; display: flex; flex-direction: column; min-height: 100vh; }
        /* Style for mobile menu absolute positioning */
        #mobile-menu {
         
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i data-feather="calendar" class="text-blue-600"></i>
                <a href="dashboard.php" class="text-xl md:text-2xl font-extrabold text-gray-900">ক্যাম্পেইন <span class="text-blue-600">শিডিউলার</span></a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="dashboard.php" class="text-sm font-medium px-3 py-2 rounded-md <?php echo ($current_page == 'dashboard.php') ? 'text-blue-600 font-bold' : 'text-gray-700 hover:text-blue-600'; ?>">ড্যাশবোর্ড</a>
                
                <a href="events.php" class="text-sm font-medium px-3 py-2 rounded-md <?php echo ($current_page == 'events.php') ? 'text-blue-600 font-bold' : 'text-gray-700 hover:text-blue-600'; ?>">সকল ইভেন্ট</a>

                <!-- Show link if role is Admin (1) OR Super Admin (2) -->
                <?php if ($role_from_session >= 1): ?>
                    <a href="admin_approval.php" class="text-sm font-medium px-3 py-2 rounded-md <?php echo ($current_page == 'admin_approval.php') ? 'text-blue-700 bg-blue-100 font-bold' : 'text-blue-700 bg-blue-100 hover:bg-blue-200'; ?>">ব্যবহারকারী ব্যবস্থাপনা</a>
                <?php endif; ?>

                <span class="text-gray-600 font-semibold text-sm">স্বাগতম, <?php echo htmlspecialchars($username_from_session); ?></span>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition-transform transform hover:scale-105 duration-300 flex items-center space-x-2 text-sm">
                    <i data-feather="log-out" class="w-4 h-4"></i>
                    <span>লগ আউট</span>
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="text-gray-600 focus:outline-none">
                    <i data-feather="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </nav>

        <!-- Mobile Menu (Initially hidden, toggled by JS) -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t absolute w-full shadow-xl z-40"> 
            <a href="dashboard.php" class="block py-3 px-5 text-base <?php echo ($current_page == 'dashboard.php') ? 'bg-gray-100 text-blue-600 font-bold' : 'text-gray-700 hover:bg-gray-50'; ?>">ড্যাশবোর্ড</a>
            <a href="events.php" class="block py-3 px-5 text-base <?php echo ($current_page == 'events.php') ? 'bg-gray-100 text-blue-600 font-bold' : 'text-gray-700 hover:bg-gray-50'; ?>">সকল ইভেন্ট</a>

            <!-- Show link if role is Admin (1) OR Super Admin (2) -->
            <?php if ($role_from_session >= 1): ?>
                <a href="admin_approval.php" class="block py-3 px-5 text-base <?php echo ($current_page == 'admin_approval.php') ? 'bg-blue-100 text-blue-700 font-bold' : 'text-blue-700 bg-blue-50 hover:bg-blue-100'; ?>">ব্যবহারকারী ব্যবস্থাপনা</a>
            <?php endif; ?>

            <div class="border-t my-2"></div>
            <div class="px-5 py-4">
                 <span class="text-gray-800 font-semibold block mb-3">স্বাগতম, <?php echo htmlspecialchars($username_from_session); ?></span>
                 <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition-transform transform hover:scale-105 duration-300 flex items-center justify-center space-x-2 text-sm w-full">
                    <i data-feather="log-out" class="w-4 h-4"></i>
                    <span>লগ আউট</span>
                </a>
            </div>
        </div>
    </header>
    <?php 

