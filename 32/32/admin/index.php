<?php
session_start();
require_once '../db.php';

// If already logged in, go to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // UNHASHED LOGIN LOGIC (Direct String Comparison)
    if ($user && $password === $user['password']) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center h-screen px-4">
    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-sm">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black text-[#00523A]">Admin Panel</h1>
            <p class="text-gray-500 text-sm">Login to manage campaign</p>
        </div>
        
        <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4 text-sm text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input type="text" name="username" class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#00523A]" placeholder="admin" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#00523A]" placeholder="password123" required>
            </div>
            <button type="submit" class="w-full bg-[#00523A] text-white font-bold py-3 rounded hover:bg-green-800 transition-colors">
                Login to Dashboard
            </button>
        </form>
    </div>
</body>
</html>