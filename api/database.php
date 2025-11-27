<?php
// api/database.php - central DB connection and helper query functions for the app
$host = '127.0.0.1';
$db   = 'rab_system';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB connection failed: ' . $e->getMessage()]);
    exit;
}

// --- Database helper functions ---
// Semua fungsi menggunakan global $pdo sehingga file lain tetap bisa menggunakan
// $pdo = require_once __DIR__ . '/db.php'; atau require 'api/database.php' dan juga memanggil fungsi-fungsi ini.

function getFormsByStatus($status) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM rab_forms WHERE status = ? ORDER BY updated_at DESC');
    $stmt->execute([$status]);
    return $stmt->fetchAll();
}

function getRejectedForms() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM rab_forms WHERE status IN ('rejected_pic','rejected_gm') ORDER BY updated_at DESC");
    return $stmt->fetchAll();
}

function getFormById($id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM rab_forms WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getFormByIdAndStatus($id, $status) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM rab_forms WHERE id = ? AND status = ? LIMIT 1');
    $stmt->execute([$id, $status]);
    return $stmt->fetch();
}

function insertRabForm($data) {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO rab_forms 
    (category,no_project,nama_project,client,venue,event_date_start,event_date_end,contract_type,remarks,rab_submit,rab_internal,profit_margin,profit_percentage,file_spph,file_spk,spk_link,status,created_by,created_at)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $ok = $stmt->execute([
        $data['category'] ?? 'kp',
        $data['no_project'] ?? null,
        $data['nama_project'] ?? null,
        $data['client'] ?? null,
        $data['venue'] ?? null,
        $data['event_date_start'] ?? null,
        $data['event_date_end'] ?? null,
        $data['contract_type'] ?? null,
        $data['remarks'] ?? null,
        $data['rab_submit'] ?? 0,
        $data['rab_internal'] ?? 0,
        $data['profit_margin'] ?? 0,
        $data['profit_percentage'] ?? 0,
        $data['file_spph'] ?? null,
        $data['file_spk'] ?? null,
        $data['spk_link'] ?? null,
        $data['status'] ?? 'pending_pic',
        $data['created_by'] ?? null,
        $data['created_at'] ?? date('Y-m-d H:i:s')
    ]);
    if ($ok) return $pdo->lastInsertId();
    return false;
}

function updateFormStatusByPIC($id, $status, $comment, $approver_id, $approver_name, $approver_email, $updater_id) {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE rab_forms SET status = ?, pic_comment = ?, pic_approved_by = ?, pic_approved_name = ?, pic_approved_email = ?, pic_decision_at = NOW(), updated_by = ?, updated_at = NOW() WHERE id = ?');
    return $stmt->execute([$status, $comment, $approver_id, $approver_name, $approver_email, $updater_id, $id]);
}

function updateFormStatusByGM($id, $status, $comment, $approver_id, $approver_name, $approver_email, $updater_id) {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE rab_forms SET status = ?, gm_comment = ?, gm_approved_by = ?, gm_approved_name = ?, gm_approved_email = ?, gm_decision_at = NOW(), updated_by = ?, updated_at = NOW() WHERE id = ?');
    return $stmt->execute([$status, $comment, $approver_id, $approver_name, $approver_email, $updater_id, $id]);
}

function rejectFormByPIC($id, $reason, $approver_id, $approver_name, $approver_email, $updater_id) {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE rab_forms SET status = ?, reject_reason = ?, pic_approved_by = ?, pic_approved_name = ?, pic_approved_email = ?, pic_decision_at = NOW(), updated_by = ?, updated_at = NOW() WHERE id = ?');
    return $stmt->execute(['rejected_pic', $reason, $approver_id, $approver_name, $approver_email, $updater_id, $id]);
}

function rejectFormByGM($id, $reason, $approver_id, $approver_name, $approver_email, $updater_id) {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE rab_forms SET status = ?, reject_reason = ?, gm_approved_by = ?, gm_approved_name = ?, gm_approved_email = ?, gm_decision_at = NOW(), updated_by = ?, updated_at = NOW() WHERE id = ?');
    return $stmt->execute(['rejected_gm', $reason, $approver_id, $approver_name, $approver_email, $updater_id, $id]);
}

function getUsersFromTable($table) {
    global $pdo;
    // $table should be an internal table name (admin_users, pic_users, gm_users, general_users)
    $allowed = ['admin_users','pic_users','gm_users','general_users'];
    if (!in_array($table, $allowed)) return [];
    $stmt = $pdo->query("SELECT username, email, full_name FROM {$table}");
    return $stmt->fetchAll();
}

function findUserInTable($table, $username) {
    global $pdo;
    $allowed = ['admin_users','pic_users','gm_users','general_users'];
    if (!in_array($table, $allowed)) return false;
    $stmt = $pdo->prepare("SELECT id, username, password, full_name, email FROM {$table} WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    return $stmt->fetch();
}

// akhir helper

return $pdo;
