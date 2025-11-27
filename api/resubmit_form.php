<?php
// api/resubmit_form.php - handle resubmission (update) of rejected forms
header('Content-Type: application/json; charset=utf-8');
session_start();
$pdo = require_once __DIR__ . '/db.php';

// helper upload function (same rules as submit_form.php)
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

$id = $_POST['id'] ?? null;
if (!$id) { echo json_encode(['success'=>false,'message'=>'Form ID dibutuhkan']); exit; }

// data
$category = $_POST['category'] ?? 'kp';
$no_project = $_POST['no_project'] ?? null;
$nama_project = $_POST['nama_project'] ?? null;
$client = $_POST['client'] ?? null;
$venue = $_POST['venue'] ?? null;
$event_date_start = $_POST['event_date_start'] ?? null;
$event_date_end = $_POST['event_date_end'] ?? null;
$contract_type = $_POST['contract_type'] ?? null;
$remarks = $_POST['remarks'] ?? null;
$rab_submit = isset($_POST['rab_submit']) ? 1 : 0;
$rab_internal = isset($_POST['rab_internal']) ? 1 : 0;
$profit_margin = $_POST['profit_margin'] ?? 0;
$profit_percentage = $_POST['profit_percentage'] ?? 0;
$spk_link = $_POST['spk_link'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

$errors = [];
if (!$no_project) $errors[] = 'No Project harus diisi';
if (!$nama_project) $errors[] = 'Nama Project harus diisi';
if (!$client) $errors[] = 'Client harus diisi';
if (!$venue) $errors[] = 'Venue harus diisi';
if (!$event_date_start) $errors[] = 'Event Date Start harus diisi';
if (!$event_date_end) $errors[] = 'Event Date End harus diisi';
if (!$contract_type) $errors[] = 'Contract Type harus dipilih';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

$upload_dir = __DIR__ . '/../uploads';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

$file_spph = uploadFile($_FILES['file_spph'] ?? null, $upload_dir);
$file_spk = uploadFile($_FILES['file_spk'] ?? null, $upload_dir);

try {
    // get existing form to keep filenames if not replaced
    $stmt = $pdo->prepare('SELECT * FROM rab_forms WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if (!$existing) { echo json_encode(['success'=>false,'message'=>'Form tidak ditemukan']); exit; }

    $file_spph_db = $file_spph ?: $existing['file_spph'];
    $file_spk_db = $file_spk ?: $existing['file_spk'];

    $update = $pdo->prepare('UPDATE rab_forms SET category = ?, no_project = ?, nama_project = ?, client = ?, venue = ?, event_date_start = ?, event_date_end = ?, contract_type = ?, remarks = ?, rab_submit = ?, rab_internal = ?, profit_margin = ?, profit_percentage = ?, file_spph = ?, file_spk = ?, spk_link = ?, status = ?, reject_reason = NULL, updated_by = ?, updated_at = NOW() WHERE id = ?');
    $ok = $update->execute([
        $category, $no_project, $nama_project, $client, $venue, $event_date_start, $event_date_end,
        $contract_type, $remarks, $rab_submit, $rab_internal, $profit_margin, $profit_percentage,
        $file_spph_db, $file_spk_db, $spk_link, 'pending_pic', $user_id, $id
    ]);

    if ($ok) {
        // fetch updated form
        $fstmt = $pdo->prepare('SELECT * FROM rab_forms WHERE id = ?');
        $fstmt->execute([$id]);
        $form = $fstmt->fetch();

        // send notification to PIC
        require_once __DIR__ . '/send_email.php';
        if ($form) {
            if (function_exists('emailNotificationLog')) {
                emailNotificationLog('pic', ['event' => 'called_from_resubmit', 'form_id' => $id, 'by_user' => $user_id]);
            }
            $emailResult = sendEmailNotification('pic', $form, 'Form disubmit ulang menunggu approval PIC');
            if (function_exists('emailNotificationLog')) {
                emailNotificationLog('pic', ['event' => 'call_result_resubmit', 'form_id' => $id, 'by_user' => $user_id, 'result' => $emailResult ? 'ok' : 'failed']);
            }
        }

        echo json_encode(['success'=>true,'message'=>'Form berhasil di-submit ulang ke PIC']);
        exit;
    }
    echo json_encode(['success'=>false,'message'=>'Gagal menyimpan perubahan']);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Error: '.$e->getMessage()]);
}
