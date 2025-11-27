<?php
// api/approve_gm.php
header('Content-Type: application/json; charset=utf-8');
session_start();
$pdo = require_once __DIR__ . '/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gm') {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit;
}

$id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;
$reason = $_POST['reason'] ?? null;
$comment = $_POST['comment'] ?? null;

if (!$id || !$action) { echo json_encode(['success'=>false,'message'=>'Invalid']); exit; }

if ($action === 'approve') {
    $stmt = $pdo->prepare('UPDATE rab_forms SET status = ?, gm_comment = ?, gm_approved_by = ?, gm_approved_name = ?, gm_approved_email = ?, gm_decision_at = NOW(), updated_by = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([
        'approved_final',
        $comment,
        $_SESSION['user_id'],
        $_SESSION['full_name'] ?? $_SESSION['username'],
        $_SESSION['email'] ?? null,
        $_SESSION['user_id'],
        $id
    ]);
    echo json_encode(['success'=>true,'message'=>'Form di-approve dan masuk ke hasil final']); exit;
} else {
    if (!$reason) { echo json_encode(['success'=>false,'message'=>'Alasan reject dibutuhkan']); exit; }
    $stmt = $pdo->prepare('UPDATE rab_forms SET status = ?, reject_reason = ?, gm_approved_by = ?, gm_approved_name = ?, gm_approved_email = ?, gm_decision_at = NOW(), updated_by = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([
        'rejected_gm',
        $reason,
        $_SESSION['user_id'],
        $_SESSION['full_name'] ?? $_SESSION['username'],
        $_SESSION['email'] ?? null,
        $_SESSION['user_id'],
        $id
    ]);
    echo json_encode(['success'=>true,'message'=>'Form ditolak oleh GM dan dikembalikan ke user']); exit;
}
