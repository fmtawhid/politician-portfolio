<?php
require_once 'auth.php';
require_once 'layout_head.php';

// Fetch Stats
$complaintsCount = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'pending'")->fetchColumn();
$galleryCount = $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
$pressCount = $pdo->query("SELECT COUNT(*) FROM press")->fetchColumn();
?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Overview</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Card 1: Complaints -->
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-red-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500">Pending Complaints</p>
                <h3 class="text-3xl font-bold"><?php echo $complaintsCount; ?></h3>
            </div>
            <i class="fas fa-exclamation-circle text-red-500 text-4xl"></i>
        </div>
        <a href="complaints.php" class="text-red-500 text-sm mt-4 inline-block hover:underline font-bold">View All &rarr;</a>
    </div>

    <!-- Card 2: Gallery -->
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500">Gallery Photos</p>
                <h3 class="text-3xl font-bold"><?php echo $galleryCount; ?></h3>
            </div>
            <i class="fas fa-images text-blue-500 text-4xl"></i>
        </div>
        <a href="gallery.php" class="text-blue-500 text-sm mt-4 inline-block hover:underline font-bold">Manage &rarr;</a>
    </div>

    <!-- Card 3: Press -->
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500">Press Articles</p>
                <h3 class="text-3xl font-bold"><?php echo $pressCount; ?></h3>
            </div>
            <i class="fas fa-newspaper text-green-500 text-4xl"></i>
        </div>
        <a href="press.php" class="text-green-500 text-sm mt-4 inline-block hover:underline font-bold">Manage &rarr;</a>
    </div>
</div>

</body>
</html>