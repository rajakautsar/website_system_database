<?php
// api/login.php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/log_helper.php';
$pdo = require_once __DIR__ . '/db.php';

$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;

if (!$username || !$password) {
    logLogin($username ?? 'empty', false, null);
    echo json_encode(['success' => false, 'message' => 'Username & password dibutuhkan']);
    exit;
}

$tables = [
    ['table' => 'admin_users', 'role' => 'admin'],
    ['table' => 'pic_users', 'role' => 'pic'],
    ['table' => 'gm_users', 'role' => 'gm'],
    ['table' => 'general_users', 'role' => 'general'],
];

$foundUser = null;
$role = null;

foreach ($tables as $info) {
    $stmt = $pdo->prepare("SELECT id, username, password, full_name, email FROM {$info['table']} WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    if ($row) {
        $foundUser = $row;
        $role = $info['role'];
        break;
    }
}

// NOTE: Passwords compared as plain text by request (no hashing).
// WARNING: This is insecure for production — passwords stored in DB are plain text.
if ($foundUser && $foundUser['password'] === $password) {
    $_SESSION['user_id'] = $foundUser['id'];
    $_SESSION['username'] = $foundUser['username'];
    $_SESSION['role'] = $role;
    $_SESSION['full_name'] = $foundUser['full_name'] ?: $foundUser['username'];
    $_SESSION['email'] = $foundUser['email'] ?? null;

    $redirect = 'form_rab.php'; // default for admin/user
    if ($role === 'pic') $redirect = 'dashboard/dashboard_pic.php';
    if ($role === 'gm') $redirect = 'dashboard/dashboard_gm.php';
    if ($role === 'general') $redirect = 'general_approved_forms.php';

    logLogin($username, true, $role);
    logRedirect('login.html', $redirect, "Login success - role: $role");
    
    echo json_encode(['success' => true, 'redirect' => $redirect]);
    exit;
}

logLogin($username, false, null);
echo json_encode(['success' => false, 'message' => 'Login gagal: username/password tidak cocok']);
