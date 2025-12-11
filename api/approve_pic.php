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

// Ambil form untuk dapat email PIC yang dipilih
$formStmt = $pdo->prepare('SELECT * FROM rab_forms WHERE id = ?');
$formStmt->execute([$id]);
$form = $formStmt->fetch();

if (!$form) { echo json_encode(['success'=>false,'message'=>'Form tidak ditemukan']); exit; }

// Gunakan email yang dipilih di form, bukan email session
$pic_email = $form['pic_email'] ?? null;
if (!$pic_email) { echo json_encode(['success'=>false,'message'=>'Email PIC tidak ditemukan di form']); exit; }

// Cek PIN master dulu, jika tidak maka cek PIN email spesifik
$stmt = $pdo->prepare('SELECT pin_hash FROM email_pins WHERE email = ? AND role = ?');
$stmt->execute(['master', 'pic']);
$masterPin = $stmt->fetch();

if ($masterPin && password_verify($pin, $masterPin['pin_hash'])) {
    // PIN master valid, lanjut
} else {
    // Cek PIN email spesifik (gunakan email dari form, bukan session)
    $stmt = $pdo->prepare('SELECT pin_hash FROM email_pins WHERE email = ? AND role = ?');
    $stmt->execute([$pic_email, 'pic']);
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
    // Kirim notifikasi ke Talia sebagai penanda ada form yang di-reject oleh PIC
    require_once __DIR__ . '/send_email.php';
    if ($form) {
        // subject prefix menjelaskan alasan/reject
        $sub = 'Form ditolak oleh PIC - ' . ($form['nama_project'] ?? '');
        sendEmailNotification('pic', $form, $sub, ['mariatalia@dyandraeventsolutions.com']);
    }

    echo json_encode(['success'=>true,'message'=>'Form ditolak dan dikembalikan ke user']); exit;
}
