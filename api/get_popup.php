<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../db.php';

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/";

try {
    $stmt = $pdo->query("SELECT id, image_url FROM popups WHERE is_active = 1 ORDER BY id DESC LIMIT 1");
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        if (!filter_var($data['image_url'], FILTER_VALIDATE_URL)) {
            $data['image_url'] = $baseUrl . $data['image_url'];
        }
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        // Return success but with null data if no popup is active
        echo json_encode(['status' => 'success', 'data' => null]);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>