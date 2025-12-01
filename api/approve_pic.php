<?php
// api/approve_pic.php
header('Content-Type: application/json; charset=utf-8');
session_start();
$pdo = require_once __DIR__ . '/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pic') {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit;
}

$id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;
$reason = $_POST['reason'] ?? null;
$comment = $_POST['comment'] ?? null;
$pin = $_POST['pin'] ?? null;

if (!$id || !$action) { echo json_encode(['success'=>false,'message'=>'Invalid']); exit; }
if (!$pin) { echo json_encode(['success'=>false,'message'=>'PIN dibutuhkan']); exit; }

// Validasi PIN
$email = $_SESSION['email'] ?? null;
if (!$email) { echo json_encode(['success'=>false,'message'=>'Email tidak ditemukan di session']); exit; }

// Cek PIN master dulu, jika tidak maka cek PIN email spesifik
$stmt = $pdo->prepare('SELECT pin_hash FROM email_pins WHERE email = ? AND role = ?');
$stmt->execute(['master', 'pic']);
$masterPin = $stmt->fetch();

if ($masterPin && password_verify($pin, $masterPin['pin_hash'])) {
    // PIN master valid, lanjut
} else {
    // Cek PIN email spesifik
    $stmt = $pdo->prepare('SELECT pin_hash FROM email_pins WHERE email = ? AND role = ?');
    $stmt->execute([$email, 'pic']);
    $pinRow = $stmt->fetch();
    
    if (!$pinRow || !password_verify($pin, $pinRow['pin_hash'])) {
        echo json_encode(['success'=>false,'message'=>'PIN salah']); exit;
    }
}

// PIN valid, lanjutkan proses
if ($action === 'approve') {
    $stmt = $pdo->prepare('UPDATE rab_forms SET status = ?, pic_comment = ?, pic_approved_by = ?, pic_approved_name = ?, pic_approved_email = ?, pic_decision_at = NOW(), updated_by = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([
        'pending_gm',
        $comment,
        $_SESSION['user_id'],
        $_SESSION['full_name'] ?? $_SESSION['username'],
        $_SESSION['email'] ?? null,
        $_SESSION['user_id'],
        $id
    ]);
    
    // Send email notification to GM
    require_once __DIR__ . '/send_email.php';
    $formStmt = $pdo->prepare('SELECT * FROM rab_forms WHERE id = ?');
    $formStmt->execute([$id]);
    $form = $formStmt->fetch();
    if ($form) {
        sendEmailNotification('gm', $form, 'Form baru menunggu approval GM');
    }
    
    echo json_encode(['success'=>true,'message'=>'Form dikirim ke GM']); exit;
} else {
    if (!$reason) { echo json_encode(['success'=>false,'message'=>'Alasan reject dibutuhkan']); exit; }
    $stmt = $pdo->prepare('UPDATE rab_forms SET status = ?, reject_reason = ?, pic_approved_by = ?, pic_approved_name = ?, pic_approved_email = ?, pic_decision_at = NOW(), updated_by = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([
        'rejected_pic',
        $reason,
        $_SESSION['user_id'],
        $_SESSION['full_name'] ?? $_SESSION['username'],
        $_SESSION['email'] ?? null,
        $_SESSION['user_id'],
        $id
    ]);
    echo json_encode(['success'=>true,'message'=>'Form ditolak dan dikembalikan ke user']); exit;
}
