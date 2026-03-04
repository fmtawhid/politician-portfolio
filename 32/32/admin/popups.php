<?php
require_once 'auth.php';

// --- ACTIONS ---
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM popups WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: popups.php");
    exit;
}

if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $current = $_GET['state'];
    $newState = $current == 1 ? 0 : 1;
    
    // Optional: Deactivate all others first if you only want ONE active at a time
    if ($newState == 1) {
        $pdo->query("UPDATE popups SET is_active = 0");
    }

    $stmt = $pdo->prepare("UPDATE popups SET is_active = ? WHERE id = ?");
    $stmt->execute([$newState, $id]);
    header("Location: popups.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_path = uploadImage($_FILES['image']);
    if ($image_path) {
        // Deactivate others
        $pdo->query("UPDATE popups SET is_active = 0");
        // Insert new active popup
        $stmt = $pdo->prepare("INSERT INTO popups (image_url, is_active) VALUES (?, 1)");
        $stmt->execute([$image_path]);
    }
}

$popups = $pdo->query("SELECT * FROM popups ORDER BY id DESC")->fetchAll();
require_once 'layout_head.php';
?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Popup Manager</h2>

<!-- Upload Form -->
<div class="bg-white p-6 rounded-lg shadow mb-8">
    <h3 class="font-bold text-lg mb-4">Upload New Popup</h3>
    <form method="POST" enctype="multipart/form-data" class="flex gap-4 items-end">
        <div class="flex-grow">
            <label class="block text-sm font-bold mb-1">Select Image</label>
            <input type="file" name="image" class="w-full border p-2 rounded" accept="image/*" required>
        </div>
        <button type="submit" class="bg-[#00523A] text-white font-bold py-3 px-6 rounded hover:bg-green-800">Upload & Activate</button>
    </form>
    <p class="text-xs text-gray-500 mt-2">* Uploading a new popup will automatically deactivate existing ones.</p>
</div>

<!-- List -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
    <?php foreach ($popups as $item): ?>
        <div class="bg-white rounded shadow overflow-hidden relative group border-2 <?php echo $item['is_active'] ? 'border-green-500' : 'border-transparent'; ?>">
            <img src="../<?php echo $item['image_url']; ?>" class="w-full h-48 object-contain bg-gray-100">
            
            <div class="p-4 flex justify-between items-center bg-gray-50">
                <!-- Toggle Switch -->
                <a href="?toggle=<?php echo $item['id']; ?>&state=<?php echo $item['is_active']; ?>" 
                   class="text-sm font-bold px-3 py-1 rounded <?php echo $item['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500'; ?>">
                   <?php echo $item['is_active'] ? 'Active' : 'Inactive'; ?>
                </a>
                
                <a href="?delete=<?php echo $item['id']; ?>" onclick="return confirm('Delete this popup?')" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>