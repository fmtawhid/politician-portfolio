<?php
require_once 'auth.php';

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
}

// Fetch Complaints
$stmt = $pdo->query("SELECT * FROM complaints ORDER BY created_at DESC");
$complaints = $stmt->fetchAll();

require_once 'layout_head.php';
?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Manage Complaints</h2>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-100 border-b">
                <th class="p-4 font-semibold text-gray-700">Date</th>
                <th class="p-4 font-semibold text-gray-700">Name / Phone</th>
                <th class="p-4 font-semibold text-gray-700">Problem</th>
                <th class="p-4 font-semibold text-gray-700">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($complaints as $row): ?>
            <tr class="border-b hover:bg-gray-50">
                <td class="p-4 text-sm text-gray-600 whitespace-nowrap">
                    <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                </td>
                <td class="p-4">
                    <div class="font-bold text-gray-800"><?php echo htmlspecialchars($row['name']); ?></div>
                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($row['phone']); ?></div>
                </td>
                <td class="p-4 text-gray-700 max-w-xs">
                    <?php echo htmlspecialchars($row['problem']); ?>
                </td>
                <td class="p-4">
                    <form method="POST" class="flex items-center">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="update_status" value="1">
                        
                        <select name="status" onchange="this.form.submit()" 
                            class="p-2 rounded text-sm font-bold border cursor-pointer
                            <?php 
                                echo $row['status'] == 'pending' ? 'bg-red-100 text-red-800' : 
                                    ($row['status'] == 'reviewed' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'); 
                            ?>">
                            <option value="pending" <?php echo $row['status']=='pending'?'selected':''; ?>>Pending</option>
                            <option value="reviewed" <?php echo $row['status']=='reviewed'?'selected':''; ?>>Reviewed</option>
                            <option value="resolved" <?php echo $row['status']=='resolved'?'selected':''; ?>>Resolved</option>
                        </select>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>