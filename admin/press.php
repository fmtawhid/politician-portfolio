<?php
require_once 'auth.php';

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM press WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: press.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_path = uploadImage($_FILES['image']);
    if ($image_path) {
        $stmt = $pdo->prepare("INSERT INTO press (title_bn, title_en, source_bn, source_en, url, publish_date, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['title_bn'], $_POST['title_en'], 
            $_POST['source_bn'], $_POST['source_en'], 
            $_POST['url'], $_POST['date'], $image_path
        ]);
    }
}

$items = $pdo->query("SELECT * FROM press ORDER BY publish_date DESC")->fetchAll();
require_once 'layout_head.php';
?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Press & Media</h2>

<div class="bg-white p-6 rounded-lg shadow mb-8">
    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="text" name="title_bn" placeholder="Headline (Bangla)" class="border p-2 rounded" required>
        <input type="text" name="title_en" placeholder="Headline (English)" class="border p-2 rounded" required>
        <input type="text" name="source_bn" placeholder="Source Name (Bangla - e.g. প্রথম আলো)" class="border p-2 rounded" required>
        <input type="text" name="source_en" placeholder="Source Name (English - e.g. Prothom Alo)" class="border p-2 rounded" required>
        <input type="url" name="url" placeholder="News Link (URL)" class="border p-2 rounded" required>
        <input type="date" name="date" class="border p-2 rounded" required>
        <input type="file" name="image" class="border p-2 rounded md:col-span-2" accept="image/*" required>
        
        <button type="submit" class="bg-[#00523A] text-white font-bold py-2 rounded hover:bg-green-800 md:col-span-2">Add News</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach ($items as $item): ?>
        <div class="bg-white rounded shadow overflow-hidden group relative">
            <img src="../<?php echo $item['image']; ?>" class="w-full h-40 object-cover">
            <div class="p-4">
                <p class="text-xs text-gray-500"><?php echo $item['publish_date']; ?> | <?php echo $item['source_en']; ?></p>
                <h4 class="font-bold text-sm mt-1"><?php echo $item['title_en']; ?></h4>
                <a href="<?php echo $item['url']; ?>" target="_blank" class="text-xs text-blue-600 hover:underline mt-2 block">Read Source</a>
            </div>
            <a href="?delete=<?php echo $item['id']; ?>" onclick="return confirm('Delete?')" class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded shadow hover:bg-red-700">
                <i class="fas fa-trash"></i>
            </a>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>