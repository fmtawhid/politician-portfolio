<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../db.php';

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/";

try {
    $stmt = $pdo->query("SELECT id, title_bn, title_en, description_bn, description_en, image_url, pdf_file FROM manifesto ORDER BY id DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as &$item) {
        // Image Path
        if (!filter_var($item['image_url'], FILTER_VALIDATE_URL)) {
            $item['image_url'] = $baseUrl . $item['image_url'];
        }
        
        // PDF Path - SIMPLIFIED
        // We assume the file exists in uploads/
        // We do NOT encode the whole URL, just spaces if needed
        if (!filter_var($item['pdf_file'], FILTER_VALIDATE_URL)) {
            $cleanFilename = str_replace(' ', '%20', $item['pdf_file']); // Basic space fix
            $item['pdf_url'] = $baseUrl . "uploads/" . $cleanFilename;
        } else {
            $item['pdf_url'] = $item['pdf_file'];
        }
    }

    echo json_encode(['status' => 'success', 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>