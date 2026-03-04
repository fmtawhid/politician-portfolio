<?php
require_once 'auth.php';

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM endorsements WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: endorsements.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_path = uploadImage($_FILES['image']);
    if ($image_path) {
        $stmt = $pdo->prepare("INSERT INTO endorsements (name_bn, name_en, quote_bn, quote_en, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name_bn'], $_POST['name_en'], 
            $_POST['quote_bn'], $_POST['quote_en'], $image_path
        ]);
    }
}

$items = $pdo->query("SELECT * FROM endorsements ORDER BY id DESC")->fetchAll();
require_once 'layout_head.php';
?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Endorsements</h2>

<div class="bg-white p-6 rounded-lg shadow mb-8">
    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="text" name="name_bn" placeholder="Name (Bangla)" class="border p-2 rounded" required>
        <input type="text" name="name_en" placeholder="Name (English)" class="border p-2 rounded" required>
        <textarea name="quote_bn" placeholder="Quote (Bangla)" class="border p-2 rounded" rows="3" required></textarea>
        <textarea name="quote_en" placeholder="Quote (English)" class="border p-2 rounded" rows="3" required></textarea>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Photo</label>
            <input type="file" name="image" class="border p-2 rounded w-full" accept="image/*" required>
        </div>
        <button type="submit" class="bg-[#00523A] text-white font-bold py-2 rounded hover:bg-green-800 md:col-span-2">Add Endorsement</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach ($items as $item): ?>
        <div class="bg-white rounded shadow p-6 border-l-4 border-yellow-400 relative">
            <div class="flex items-center gap-4 mb-4">
                <img src="../<?php echo $item['image']; ?>" class="w-12 h-12 rounded-full object-cover">
                <div>
                    <h4 class="font-bold text-sm"><?php echo $item['name_en']; ?></h4>
                    <p class="text-xs text-gray-500"><?php echo $item['name_bn']; ?></p>
                </div>
            </div>
            <p class="text-sm text-gray-600 italic">"<?php echo $item['quote_en']; ?>"</p>
            
            <a href="?delete=<?php echo $item['id']; ?>" onclick="return confirm('Delete?')" class="absolute top-2 right-2 text-red-400 hover:text-red-600">
                <i class="fas fa-trash"></i>
            </a>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>