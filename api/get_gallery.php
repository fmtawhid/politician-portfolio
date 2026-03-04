<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow Mobile App Access
require_once '../db.php';

// Define Base URL for Images
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/";

try {
    $stmt = $pdo->query("SELECT id, src, alt_bn, alt_en, created_at FROM gallery ORDER BY created_at DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add Full URL to image path
    foreach ($data as &$item) {
        if (!filter_var($item['src'], FILTER_VALIDATE_URL)) {
            $item['src'] = $baseUrl . $item['src'];
        }
    }

    echo json_encode(['status' => 'success', 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>