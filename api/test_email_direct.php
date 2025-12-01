<?php
// Direct SMTP test - check if emails are being sent and what errors occur

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/db.php';

$emailConfig = require __DIR__ . '/email_config.php';

echo "=== EMAIL DELIVERY TEST ===\n";
echo "From: " . $emailConfig['from_email'] . "\n";
echo "To: rajakautsar09@gmail.com\n";
echo "SMTP Host: " . $emailConfig['smtp_host'] . "\n";
echo "SMTP Port: " . $emailConfig['smtp_port'] . "\n";
echo "SMTP User: " . $emailConfig['smtp_user'] . "\n";
echo "\n";

try {
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    // Enable debug output
    $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
    $mail->Debugoutput = function($str, $level) {
        echo "[DEBUG] $str\n";
    };
    
    $mail->isSMTP();
    $mail->Host       = $emailConfig['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $emailConfig['smtp_user'];
    $mail->Password   = $emailConfig['smtp_pass'];
    $mail->SMTPSecure = $emailConfig['smtp_secure'];
    $mail->Port       = $emailConfig['smtp_port'];
    
    $mail->setFrom($emailConfig['from_email'], 'RAB System Test');
    $mail->addAddress('rajakautsar09@gmail.com', 'Test Recipient');
    
    $mail->isHTML(true);
    $mail->Subject = 'Test Email - ' . date('Y-m-d H:i:s');
    $mail->Body = '<h2>This is a test email</h2><p>If you see this, SMTP is working correctly.</p>';
    $mail->CharSet = 'UTF-8';
    
    if ($mail->send()) {
        echo "\n✅ SUCCESS: Email sent successfully!\n";
        echo "Message ID: " . $mail->getLastMessageID() . "\n";
    } else {
        echo "\n❌ FAILED: " . $mail->ErrorInfo . "\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ EXCEPTION: " . $e->getMessage() . "\n";
}
?>
