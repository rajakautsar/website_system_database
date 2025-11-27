<?php
// api/test_send_email.php - simple test endpoint for sendEmailNotification
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/send_email.php';

echo "Testing sendEmailNotification()\n";

// report PHPMailer availability
if (class_exists('\\PHPMailer\\PHPMailer\\PHPMailer')) {
    echo "PHPMailer detected. Will attempt SMTP send if configured.\n";
} else {
    echo "PHPMailer NOT detected. send_email will fallback to logging.\n";
}

// try to fetch a form from DB
try {
    $stmt = $pdo->query('SELECT * FROM rab_forms LIMIT 1');
    $form = $stmt->fetch();
} catch (Exception $e) {
    $form = false;
}

if (!$form) {
    echo "No rab_forms found in DB — using mock form data.\n";
    $form = [
        'id' => 0,
        'no_project' => 'TEST-001',
        'nama_project' => 'Test Project',
        'client' => 'Test Client',
        'venue' => 'Test Venue',
        'event_date_start' => date('Y-m-d'),
        'event_date_end' => date('Y-m-d', strtotime('+1 day')),
        'status' => 'test'
    ];
}

// call the notification to PIC role
$result = sendEmailNotification('pic', $form, 'Tes Notifikasi Sistem');

echo $result ? "sendEmailNotification returned TRUE\n" : "sendEmailNotification returned FALSE\n";

echo "Done. Check PHP error log or application log for email delivery details.\n";
