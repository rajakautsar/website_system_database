<?php
// Redirect to login if not logged in, otherwise redirect based on role
session_start();
require_once __DIR__ . '/api/log_helper.php';
logPageAccess('index.php');

if (!isset($_SESSION['user_id'])) {
    logRedirect('index.php', 'login.html', 'No session');
    header('Location: login.html');
    exit;
}

$role = $_SESSION['role'] ?? 'user';
if ($role === 'pic') {
    logRedirect('index.php', 'dashboard/dashboard_pic.php', "Role: $role");
    header('Location: dashboard/dashboard_pic.php');
} elseif ($role === 'gm') {
    logRedirect('index.php', 'dashboard/dashboard_gm.php', "Role: $role");
    header('Location: dashboard/dashboard_gm.php');
} elseif ($role === 'general') {
    logRedirect('index.php', 'general_approved_forms.php', "Role: $role");
    header('Location: general_approved_forms.php');
} else {
    logRedirect('index.php', 'general_approved_forms.php', "Role: $role");
    header('Location: general_approved_forms.php');
}
exit;

