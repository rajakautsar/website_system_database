<?php
// api/log_helper.php - Helper untuk logging
function writeLog($message, $data = []) {
    $log_dir = __DIR__ . '/../logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/access_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $session_info = '';
    if (session_status() === PHP_SESSION_ACTIVE) {
        $session_info = ' | Session: ' . (isset($_SESSION['user_id']) ? 'user_id=' . $_SESSION['user_id'] : 'no_session');
        $session_info .= ' | Role: ' . ($_SESSION['role'] ?? 'no_role');
        $session_info .= ' | Username: ' . ($_SESSION['username'] ?? 'no_username');
    }
    
    $data_str = !empty($data) ? ' | Data: ' . json_encode($data) : '';
    $log_entry = "[$timestamp] [$ip] $message$session_info$data_str | UA: $user_agent\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

function logRedirect($from, $to, $reason = '') {
    writeLog("REDIRECT: $from -> $to" . ($reason ? " ($reason)" : ""));
}

function logLogin($username, $success, $role = null) {
    $status = $success ? 'SUCCESS' : 'FAILED';
    writeLog("LOGIN $status: username=$username" . ($role ? " | role=$role" : ""));
}

function logPageAccess($page, $action = '') {
    $action_str = $action ? " | Action: $action" : '';
    writeLog("PAGE ACCESS: $page$action_str");
}

