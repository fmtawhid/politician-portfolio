<?php
// submit_complaint.php

// 1. CORS Headers (Backup in case .htaccess fails)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 2. Handle Preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db.php';

// 3. DEBUG LOGGING (Check error_log.txt in your server)
$logFile = 'complaint_debug_log.txt';
$inputData = file_get_contents("php://input");
$postData = print_r($_POST, true);
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Request Received\nPOST: $postData\nInput: $inputData\n", FILE_APPEND);

try {
    // 4. Validate Request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    // 5. Get Data
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $problem = $_POST['problem'] ?? '';

    if (empty($name) || empty($phone) || empty($problem)) {
        throw new Exception('All fields are required.');
    }

    // 6. Insert
    $stmt = $pdo->prepare("INSERT INTO complaints (name, phone, problem, status) VALUES (?, ?, ?, 'pending')");
    
    if ($stmt->execute([$name, $phone, $problem])) {
        echo json_encode(['status' => 'success', 'message' => 'Saved successfully']);
    } else {
        throw new Exception('Database insert failed.');
    }

} catch (Exception $e) {
    http_response_code(500); // Send Server Error Code
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    // Log error
    file_put_contents($logFile, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
}
?>