<?php
require_once 'auth.php';

// --- DELETE ---
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM manifesto WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: manifesto.php");
    exit;
}

// --- CREATE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title_bn = $_POST['title_bn'];
    $title_en = $_POST['title_en'];
    $desc_bn = $_POST['desc_bn'];
    $desc_en = $_POST['desc_en'];
    
    // Upload Cover Image
    $image_path = uploadImage($_FILES['image']);
    
    // Manual PDF Upload Logic
    $pdf_path = '';
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == 0) {
        $target_dir = "../uploads/";
        $filename = time() . '_manifesto.pdf';
        if (move_uploaded_file($_FILES['pdf']['tmp_name'], $target_dir . $filename)) {
            $pdf_path = $filename; // Store just filename based on your table structure
        }
    }

    if ($image_path && $pdf_path) {
        $stmt = $pdo->prepare("INSERT INTO manifesto (title_bn, title_en, description_bn, description_en, image_url, pdf_file) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title_bn, $title_en, $desc_bn, $desc_en, $image_path, $pdf_path]);
    }
}

$items = $pdo->query("SELECT * FROM manifesto ORDER BY id DESC")->fetchAll();
require_once 'layout_head.php';
?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Manifesto Manager</h2>

<div class="bg-white p-6 rounded-lg shadow mb-8">
    <h3 class="font-bold text-lg mb-4">Add New Manifesto Point</h3>
    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="text" name="title_bn" placeholder="Title (Bangla)" class="border p-2 rounded" required>
        <input type="text" name="title_en" placeholder="Title (English)" class="border p-2 rounded" required>
        
        <textarea name="desc_bn" placeholder="Description (Bangla)" class="border p-2 rounded" rows="2" required></textarea>
        <textarea name="desc_en" placeholder="Description (English)" class="border p-2 rounded" rows="2" required></textarea>
        
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase">Cover Image</label>
            <input type="file" name="image" class="border p-2 rounded w-full" accept="image/*" required>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase">PDF File</label>
            <input type="file" name="pdf" class="border p-2 rounded w-full" accept="application/pdf" required>
        </div>
        
        <button type="submit" class="bg-[#00523A] text-white font-bold py-2 rounded hover:bg-green-800 md:col-span-2">Publish Manifesto</button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <?php foreach ($items as $item): ?>
        <div class="bg-white rounded shadow p-4 flex gap-4">
            <img src="../<?php echo $item['image_url']; ?>" class="w-24 h-32 object-cover rounded">
            <div class="flex-grow">
                <h4 class="font-bold text-[#00523A]"><?php echo $item['title_en']; ?></h4>
                <p class="text-sm text-gray-600 mb-2"><?php echo $item['title_bn']; ?></p>
                <a href="../uploads/<?php echo $item['pdf_file']; ?>" target="_blank" class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">View PDF</a>
            </div>
            <a href="?delete=<?php echo $item['id']; ?>" onclick="return confirm('Delete?')" class="text-red-600"><i class="fas fa-trash"></i></a>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>