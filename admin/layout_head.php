<!-- admin/layout_head.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex min-h-screen">

<!-- Sidebar -->
<aside class="w-64 bg-[#00523A] text-white flex flex-col hidden md:flex">
    <div class="p-6 text-center text-2xl font-bold border-b border-green-700">Noman Admin</div>
    <nav class="flex-grow p-4 space-y-2">
        <a href="dashboard.php" class="block p-3 rounded hover:bg-green-700"><i class="fas fa-tachometer-alt w-6"></i> Dashboard</a>
        <a href="complaints.php" class="block p-3 rounded hover:bg-green-700"><i class="fas fa-envelope-open-text w-6"></i> Complaints</a>
        <a href="gallery.php" class="block p-3 rounded hover:bg-green-700"><i class="fas fa-images w-6"></i> Gallery</a>
        <a href="press.php" class="block p-3 rounded hover:bg-green-700"><i class="fas fa-newspaper w-6"></i> Press</a>
        <a href="endorsements.php" class="block p-3 rounded hover:bg-green-700"><i class="fas fa-users w-6"></i> Endorsements</a>
        <a href="popups.php" class="block p-3 rounded hover:bg-green-700"><i class="fas fa-window-restore w-6"></i> Popups</a>
        <a href="manifesto.php" class="block p-3 rounded hover:bg-green-700"><i class="fas fa-book w-6"></i> Manifesto</a>
    </nav>
    <div class="p-4 border-t border-green-700">
        <a href="logout.php" class="block text-center bg-red-600 py-2 rounded hover:bg-red-700">Logout</a>
    </div>
</aside>

<!-- Main Content -->
<div class="flex-1 flex flex-col">
    <!-- Mobile Header -->
    <header class="bg-white shadow p-4 md:hidden flex justify-between items-center">
        <h1 class="font-bold text-xl text-[#00523A]">Admin Panel</h1>
        <a href="logout.php" class="text-red-600"><i class="fas fa-sign-out-alt"></i></a>
    </header>
    
    <main class="p-6 flex-grow overflow-y-auto">