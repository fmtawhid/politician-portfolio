<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../db.php';

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/";

try {
    $stmt = $pdo->query("SELECT id, title_bn, title_en, source_bn, source_en, url, publish_date, image FROM press ORDER BY publish_date DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as &$item) {
        if (!filter_var($item['image'], FILTER_VALIDATE_URL)) {
            $item['image'] = $baseUrl . $item['image'];
        }
    }

    echo json_encode(['status' => 'success', 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>