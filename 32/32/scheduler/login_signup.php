<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
ob_start();

require_once 'db_connect.php';

$signup_error = $login_error = $signup_success = "";


// ==================== SIGNUP LOGIC ====================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($email) || empty($password)) {
        $signup_error = "সব ঘর পূরণ করুন।";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_error = "একটি বৈধ ইমেইল ঠিকানা লিখুন।";
    } elseif (strlen($password) < 6) {
        $signup_error = "পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $signup_error = "এই ইমেইলটি ইতিমধ্যে নিবন্ধিত।";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, status, role) VALUES (?, ?, ?, 'pending', 'user')");
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("sss", $username, $email, $hashed);
            if ($stmt->execute()) {
                $signup_success = "অ্যাকাউন্ট তৈরি হয়েছে! অ্যাডমিন অনুমোদনের পর লগইন করতে পারবেন।";
            } else {
                $signup_error = "একটি ত্রুটি ঘটেছে, আবার চেষ্টা করুন।";
            }
            $stmt->close();
        }
        $check->close();
    }
}


// ==================== LOGIN LOGIC ====================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        $login_error = "ইমেইল এবং পাসওয়ার্ড দিন।";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, status, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $username, $hashed_password, $status, $role);
            $stmt->fetch();

            if ($status !== 'approved') {
                $login_error = "আপনার অ্যাকাউন্ট এখনও অনুমোদিত হয়নি।";
            } elseif (password_verify($password, $hashed_password)) {
                session_regenerate_id(true);

                $_SESSION["loggedin"] = true;
                $_SESSION["user_id"] = $id;       
                $_SESSION["username"] = $username;
                $_SESSION["role"] = $role ?? 'user';
                $_SESSION["last_activity"] = time();

                header("Location: dashboard.php");
                exit;
            } else {
                $login_error = "ভুল পাসওয়ার্ড।";
            }
        } else {
            $login_error = "এই ইমেইল দিয়ে কোনো অ্যাকাউন্ট পাওয়া যায়নি।";
        }

        $stmt->close();
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লগইন এবং সাইনআপ | ক্যাম্পেইন শিডিউলার</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Bengali', sans-serif; }
        .input-field {
            width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 1rem;
            box-sizing: border-box; transition: all 0.2s ease-in-out;
        }
        .input-field:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2); }
        .icon-wrapper { position: relative; margin-bottom: 1.25rem; }
        .icon-wrapper .icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #a0aec0; }
        .icon-wrapper input { padding-left: 3rem; }

        .flip-container { perspective: 1000px; }
        .flipper { transition: 0.6s; transform-style: preserve-d; position: relative; min-height: 480px; } /* Adjusted height */
        .front, .back {
            backface-visibility: hidden;
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
        }
        .front { z-index: 2; transform: rotateY(0deg); }
        .back { transform: rotateY(180deg); }
        .flip-container.is-flipped .flipper { transform: rotateY(180deg); }

        /* Gradient Button Styles */
        .gradient-btn-blue {
             background-image: linear-gradient(to right, #3b82f6, #2563eb);
        }
        .gradient-btn-blue:hover {
             background-image: linear-gradient(to right, #2563eb, #1d4ed8); 
        }
         .gradient-btn-green {
             background-image: linear-gradient(to right, #10b981, #059669);
        }
        .gradient-btn-green:hover {
             background-image: linear-gradient(to right, #059669, #047857); 
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
<body class="bg-gray-100">

    <div class="flex flex-col md:flex-row items-stretch min-h-screen">
        <!-- Left Pane: Image -->
        <div class="w-full h-64 md:h-auto md:w-1/2 bg-cover bg-center" style="background-image: url('./uploads/Noman_haque.jpg');">
             <!-- Gradient Overlay on Image -->
            <div class="flex items-end h-full p-12 bg-gradient-to-t from-black via-black/70 to-transparent">
                <div>
                    <h1 class="text-4xl font-extrabold text-white">ক্যাম্পেইন শিডিউলার</h1>
                    <p class="text-lg text-gray-200 mt-2">সংগঠিত করুন, সময়সূচী করুন এবং জিতুন। আপনার প্রচারণার কেন্দ্রীয় হাব।</p>
                </div>
            </div>
        </div>

        <!-- Right Pane: Form -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-6">
            <div id="form-container" class="w-full max-w-sm flip-container <?php if(!empty($signup_error) || !empty($signup_success)) echo 'is-flipped'; ?>">
                <div class="flipper">
                    <!-- LOGIN FORM (FRONT) -->
                    <div class="front">
                        <div class="text-center md:text-left mb-10">
                            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">আবার স্বাগতম</h2>
                            <p class="text-gray-600">চালিয়ে যেতে আপনার অ্যাকাউন্টে লগইন করুন।</p>
                        </div>

                        <?php if (!empty($login_error)) : ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-6 rounded-md" role="alert">
                            <p><?php echo htmlspecialchars($login_error); ?></p>
                        </div>
                        <?php endif; ?>

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="mt-8">
                            <div class="icon-wrapper">
                                <i data-feather="mail" class="icon"></i>
                                <input type="email" name="email" class="input-field" required placeholder="ইমেল ঠিকানা">
                            </div>
                            <div class="icon-wrapper">
                                <i data-feather="lock" class="icon"></i>
                              
                                <input type="password" name="password" class="input-field" required placeholder="পাসওয়ার্ড">
                            </div>
                            <button type="submit" name="login" class="gradient-btn-blue w-full text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-px">
                                লগইন
                            </button>
                        </form>
                        <p class="mt-8 text-center text-gray-600 text-sm">
                            <span>অ্যাকাউন্ট নেই? </span>
                            <a href="#" id="flip-to-signup" class="font-medium text-blue-600 hover:text-blue-800">এখানে সাইন আপ করুন</a>
                        </p>
                    </div>

                    <!-- SIGNUP FORM -->
                    <div class="back">
                         <div class="text-center md:text-left mb-10">
                            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">অ্যাকাউন্ট তৈরি করুন</h2>
                            <p class="text-gray-600">আপনার নতুন অ্যাকাউন্ট তৈরি করে শুরু করুন।</p>
                        </div>

                        <?php if (!empty($signup_error)) : ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-6 rounded-md" role="alert">
                            <p><?php echo htmlspecialchars($signup_error); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($signup_success)) : ?>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-6 rounded-md" role="alert">
                            <p><?php echo htmlspecialchars($signup_success); ?></p>
                        </div>
                        <?php endif; ?>

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="mt-8">
                            <div class="icon-wrapper">
                                <i data-feather="user" class="icon"></i>
                                <input type="text" name="username" class="input-field" required placeholder="একটি ব্যবহারকারীর নাম চয়ন করুন">
                            </div>
                             <div class="icon-wrapper">
                                <i data-feather="mail" class="icon"></i>
                                <input type="email" name="email" class="input-field" required placeholder="আপনার ইমেল ঠিকানা">
                            </div>
                            <div class="icon-wrapper">
                                <i data-feather="key" class="icon"></i>
                                <input type="password" name="password" class="input-field" required placeholder="একটি পাসওয়ার্ড তৈরি করুন (সর্বনিম্ন ৬ অক্ষর)">
                            </div>
                             
                            <button type="submit" name="signup" class="gradient-btn-green w-full text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-px">
                                অ্যাকাউন্ট তৈরি করুন
                            </button>
                        </form>
                        <p class="mt-8 text-center text-gray-600 text-sm">
                           <span>ইতিমধ্যে একটি অ্যাকাউন্ট আছে? </span>
                           <a href="#" id="flip-to-login" class="font-medium text-blue-600 hover:text-blue-800">পরিবর্তে লগইন করুন</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        feather.replace();
        const container = document.getElementById('form-container');
        document.getElementById('flip-to-signup').addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.add('is-flipped');
        });
        document.getElementById('flip-to-login').addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.remove('is-flipped');
        });

        // Show signup form if there was a signup error OR a signup success
        <?php if(!empty($signup_error) || !empty($signup_success)): ?>
            container.classList.add('is-flipped');
        <?php endif; ?>
    </script>
</body>
</html>

