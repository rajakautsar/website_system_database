<?php
// api/submit_form.php
header('Content-Type: application/json; charset=utf-8');
session_start();
$pdo = require_once __DIR__ . '/db.php';

// data
$category = $_POST['category'] ?? 'kp';
$no_project = $_POST['no_project'] ?? null;
$nama_project = $_POST['nama_project'] ?? null;
$client = $_POST['client'] ?? null;
$venue = $_POST['venue'] ?? null;
$event_date_start = $_POST['event_date_start'] ?? null;
$event_date_end = $_POST['event_date_end'] ?? null;
$remarks = $_POST['remarks'] ?? null;
$rab_submit = $_POST['rab_submit'] ?? 0;
$rab_internal = $_POST['rab_internal'] ?? 0;
$profit_margin = $_POST['profit_margin'] ?? 0;
$profit_percentage = $_POST['profit_percentage'] ?? 0;
$spk_link = $_POST['spk_link'] ?? null;
$pic_email = $_POST['pic_email'] ?? null;
$gm_email = $_POST['gm_email'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

// validation
$errors = [];
if (!$no_project) $errors[] = 'No Project harus diisi';
if (!$nama_project) $errors[] = 'Nama Project harus diisi';
if (!$client) $errors[] = 'Client harus diisi';
if (!$venue) $errors[] = 'Venue harus diisi';
if (!$event_date_start) $errors[] = 'Event Date Start harus diisi';
if (!$event_date_end) $errors[] = 'Event Date End harus diisi';
if (!isset($_FILES['file_spph']) || $_FILES['file_spph']['error'] !== UPLOAD_ERR_OK) $errors[] = 'File SPPH wajib diupload';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// upload folder
$upload_dir = __DIR__ . '/../uploads';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

function uploadFile($file, $upload_dir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $allowed = ['pdf','docx'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return null;
    if ($file['size'] > 10*1024*1024) return null;
    $name = time() . '_' . uniqid() . '.' . $ext;
    $path = $upload_dir . '/' . $name;
    if (move_uploaded_file($file['tmp_name'], $path)) return $name;
    return null;
}

$file_spph = uploadFile($_FILES['file_spph'], $upload_dir);
$file_spk = isset($_FILES['file_spk']) ? uploadFile($_FILES['file_spk'], $upload_dir) : null;

// insert
try {
    $stmt = $pdo->prepare('INSERT INTO rab_forms 
    (category,no_project,nama_project,client,venue,event_date_start,event_date_end,remarks,rab_submit,rab_internal,profit_margin,profit_percentage,file_spph,file_spk,spk_link,pic_email,gm_email,status,created_by,created_at)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');

    $ok = $stmt->execute([
        $category, $no_project, $nama_project, $client, $venue, $event_date_start, $event_date_end,
        $remarks, $rab_submit, $rab_internal, $profit_margin, $profit_percentage,
        $file_spph, $file_spk, $spk_link, $pic_email, $gm_email, 'pending_pic', $user_id, date('Y-m-d H:i:s')
    ]);

    if ($ok) {
        $form_id = $pdo->lastInsertId();
        
        // Send email notification to PIC Sales
        require_once __DIR__ . '/send_email.php';
        $formStmt = $pdo->prepare('SELECT * FROM rab_forms WHERE id = ?');
        $formStmt->execute([$form_id]);
        $form = $formStmt->fetch();
        if ($form) {
            // log that submit flow is calling the notification
            if (function_exists('emailNotificationLog')) {
                emailNotificationLog('pic', ['event' => 'called_from_submit', 'form_id' => $form_id, 'by_user' => $user_id]);
            }
            $emailResult = sendEmailNotification('pic', $form, 'Form baru menunggu approval PIC Sales');
            if (function_exists('emailNotificationLog')) {
                emailNotificationLog('pic', ['event' => 'call_result_submit', 'form_id' => $form_id, 'by_user' => $user_id, 'result' => $emailResult ? 'ok' : 'failed']);
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Form berhasil dikirim ke PIC Sales untuk dicek.']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
