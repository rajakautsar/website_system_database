<?php
// api/clear_log.php - Clear log file
session_start();
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] === 'general')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$file = $_GET['file'] ?? null;
if (!$file || !preg_match('/^access_\d{4}-\d{2}-\d{2}\.log$/', $file)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file']);
    exit;
}

$log_file = __DIR__ . '/../logs/' . $file;
if (file_exists($log_file)) {
    file_put_contents($log_file, '');
    echo json_encode(['success' => true, 'message' => 'Log cleared']);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'File not found']);
}

