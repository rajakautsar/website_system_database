<?php
// =============================================
//  send_email.php – FINAL VERSION
// =============================================

// Simple email logger
function emailNotificationLog(string $role, array $data) {
    $logDir  = __DIR__ . '/../logs';
    $logFile = $logDir . '/email_notifications.log';

    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    $entry = [
        'ts'          => date('c'),
        'role'        => $role,
        'data'        => $data,
        'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null,
    ];

    @file_put_contents($logFile, json_encode($entry, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
}

function sendEmailNotification($recipient_role, $form, $subject_prefix = '') {
    global $pdo;

    if (!isset($pdo)) {
        $pdo = require __DIR__ . '/db.php';
    }

    // Mapping table berdasarkan role
    $tableMap = [
        'admin'   => 'admin_users',
        'pic'     => 'pic_users',
        'gm'      => 'gm_users',
        'general' => 'general_users',
    ];

    if (!isset($tableMap[$recipient_role])) {
        error_log("Unknown role in sendEmailNotification: " . $recipient_role);
        return false;
    }

    $table = $tableMap[$recipient_role];

    // Ambil daftar penerima dari DB
    try {
        $recipients = $pdo->query("SELECT username, email, full_name FROM {$table}")->fetchAll();
    } catch (Exception $e) {
        error_log("Email fetch error: " . $e->getMessage());
        return false;
    }

    if (!$recipients) {
        emailNotificationLog($recipient_role, ['event' => 'no_recipients']);
        return false;
    }

    // SUBJECT
    $subject = $subject_prefix ?: ("Notifikasi Form RAB - " . ($form['nama_project'] ?? ''));

    // HTML MESSAGE
    $message = "
    <html>
    <head><title>Notifikasi Form RAB</title></head>
    <body>
        <h2>Notifikasi Form RAB</h2>
        <p>Ada form yang memerlukan perhatian Anda:</p>
        <table border='1' cellpadding='5' cellspacing='0'>
            <tr><th>No Project</th><td>".htmlspecialchars($form['no_project'] ?? '')."</td></tr>
            <tr><th>Nama Project</th><td>".htmlspecialchars($form['nama_project'] ?? '')."</td></tr>
            <tr><th>Client</th><td>".htmlspecialchars($form['client'] ?? '')."</td></tr>
            <tr><th>Venue</th><td>".htmlspecialchars($form['venue'] ?? '')."</td></tr>
            <tr><th>Event Date</th><td>".
                htmlspecialchars($form['event_date_start'] ?? '') .
                " - " .
                htmlspecialchars($form['event_date_end'] ?? '') .
            "</td></tr>
            <tr><th>Status</th><td>".htmlspecialchars($form['status'] ?? '')."</td></tr>
        </table>
        <p>Silakan login ke sistem untuk melihat detail dan melakukan approval / reject ke link berikut:<br>
        <a href='https://prosys.dyandraeventsolutions.com/login.html' target='_blank'>Login Sistem</a></p>
    </body>
    </html>";

    // Load config
    $emailConfig = file_exists(__DIR__ . '/email_config.php')
        ? require __DIR__ . '/email_config.php'
        : [];

    // Autoload PHPMailer (Composer)
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
    }

    // Apply override per role
    $roleConfig = $emailConfig['roles'][$recipient_role] ?? [];

    // Forced recipients per role
    if (!empty($roleConfig['force_to'])) {
        $recipients = array_map(fn($e) => [
            'email' => $e,
            'username' => strstr($e, '@', true),
            'full_name' => null
        ], $roleConfig['force_to']);
    }

    // LOG recipients
    emailNotificationLog($recipient_role, [
        'event'      => 'prepared',
        'subject'    => $subject,
        'recipients' => array_map(fn($u) => ['email'=>$u['email']??null], $recipients)
    ]);

    // ============================================
    //  PHPMailer Mode
    // ============================================
    if (class_exists('\\PHPMailer\\PHPMailer\\PHPMailer')) {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = $roleConfig['smtp_host']   ?? $emailConfig['smtp_host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $roleConfig['smtp_user']   ?? $emailConfig['smtp_user'];
            $mail->Password   = $roleConfig['smtp_pass']   ?? $emailConfig['smtp_pass'];
            $mail->SMTPSecure = $roleConfig['smtp_secure'] ?? $emailConfig['smtp_secure'];
            $mail->Port       = $roleConfig['smtp_port']   ?? $emailConfig['smtp_port'];

            $fromEmail = $roleConfig['from_email'] ?? $emailConfig['from_email'];
            $fromName  = $roleConfig['from_name']  ?? $emailConfig['from_name'];

            $mail->setFrom($fromEmail, $fromName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->CharSet = 'UTF-8';

            $toList = [];
            foreach ($recipients as $u) {
                $to = $u['email'] ?? null;
                if ($to) {
                    $mail->addAddress($to, $u['full_name'] ?? $u['username']);
                    $toList[] = $to;
                }
            }

            emailNotificationLog($recipient_role, [
                'event' => 'sending',
                'from'  => $fromEmail,
                'to'    => $toList
            ]);

            $mail->send();

            emailNotificationLog($recipient_role, [
                'event' => 'sent',
                'count' => count($toList)
            ]);

            return true;

        } catch (Throwable $e) {
            emailNotificationLog($recipient_role, [
                'event' => 'failed',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    // ============================================
    //  Fallback Logging Mode (tanpa PHPMailer)
    // ============================================
    emailNotificationLog($recipient_role, [
        'event' => 'fallback',
        'info'  => 'PHPMailer not installed'
    ]);

    return true;
}
