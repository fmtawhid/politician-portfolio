<?php
require_once 'auth.php';

// --- DELETE ACTION ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Optional: Delete image file from folder here if you want strict cleanup
    $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: gallery.php");
    exit;
}

// --- CREATE ACTION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alt_bn = $_POST['alt_bn'];
    $alt_en = $_POST['alt_en'];
    $image_path = uploadImage($_FILES['image']); // Uses helper from auth.php

    if ($image_path) {
        $stmt = $pdo->prepare("INSERT INTO gallery (src, alt_bn, alt_en) VALUES (?, ?, ?)");
        $stmt->execute([$image_path, $alt_bn, $alt_en]);
    }
}

// --- READ ACTION ---
$gallery = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();

require_once 'layout_head.php';
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Gallery Manager</h2>
</div>

<!-- Add New Form -->
<div class="bg-white p-6 rounded-lg shadow mb-8">
    <h3 class="font-bold text-lg mb-4">Add New Photo</h3>
    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="text" name="alt_bn" placeholder="Caption (Bangla)" class="border p-2 rounded" required>
        <input type="text" name="alt_en" placeholder="Caption (English)" class="border p-2 rounded" required>
        <input type="file" name="image" class="border p-2 rounded" accept="image/*" required>
        <button type="submit" class="bg-[#00523A] text-white font-bold py-2 rounded hover:bg-green-800 col-span-1 md:col-span-3">Upload Photo</button>
    </form>
</div>

<!-- List View -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <?php foreach ($gallery as $item): ?>
        <div class="bg-white rounded shadow overflow-hidden relative group">
            <img src="../<?php echo $item['src']; ?>" class="w-full h-48 object-cover">
            <div class="p-4">
                <p class="font-bold text-sm"><?php echo $item['alt_bn']; ?></p>
                <p class="text-xs text-gray-500"><?php echo $item['alt_en']; ?></p>
            </div>
            <!-- Delete Button -->
            <a href="?delete=<?php echo $item['id']; ?>" onclick="return confirm('Are you sure?')" 
               class="absolute top-2 right-2 bg-red-600 text-white p-2 rounded-full shadow hover:bg-red-700">
                <i class="fas fa-trash"></i>
            </a>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>