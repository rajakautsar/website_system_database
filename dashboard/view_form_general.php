<?php
session_start();
require_once __DIR__ . '/../api/log_helper.php';
$id = $_GET['id'] ?? null;
logPageAccess('dashboard/view_form_general.php', "id=$id");

// Hanya general user yang bisa akses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'general') {
    logRedirect('dashboard/view_form_general.php', 'login.html', 'Unauthorized access');
    header('Location: ../login.html');
    exit;
}

$pdo = require_once __DIR__ . '/../api/db.php';
$id = $_GET['id'] ?? null;
if (!$id) { 
    header('Location: ../general_approved_forms.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM rab_forms WHERE id = ? AND status = ? LIMIT 1');
$stmt->execute([$id, 'approved_final']);
$f = $stmt->fetch();

if (!$f) { 
    header('Location: ../general_approved_forms.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Detail Form - General User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4>Detail Form #<?=htmlspecialchars($f['id'])?></h4>
      <div>
        <a href="../general_approved_forms.php" class="btn btn-secondary">Kembali</a>
        <a href="../logout.php" class="btn btn-outline-danger ms-2">Logout</a>
      </div>
    </div>
    
    <div class="card">
      <div class="card-body">
        <table class="table table-bordered">
          <tr><th>Category</th><td><?=htmlspecialchars($f['category'])?></td></tr>
          <tr><th>No Project</th><td><?=htmlspecialchars($f['no_project'])?></td></tr>
          <tr><th>Nama Project</th><td><?=htmlspecialchars($f['nama_project'])?></td></tr>
          <tr><th>Client</th><td><?=htmlspecialchars($f['client'])?></td></tr>
          <tr><th>Venue</th><td><?=htmlspecialchars($f['venue'])?></td></tr>
          <tr><th>Event Date Start</th><td><?=htmlspecialchars($f['event_date_start'])?></td></tr>
          <tr><th>Event Date End</th><td><?=htmlspecialchars($f['event_date_end'])?></td></tr>
          <tr><th>Remarks</th><td><?=nl2br(htmlspecialchars($f['remarks'] ?? '-'))?></td></tr>
          <tr><th>Status</th><td><span class="badge bg-success"><?=htmlspecialchars($f['status'])?></span></td></tr>
          <?php if (!empty($f['pic_comment'])): ?>
          <tr><th>Komentar PIC Sales</th><td><?=nl2br(htmlspecialchars($f['pic_comment']))?></td></tr>
          <?php endif; ?>
          <?php if (!empty($f['gm_comment'])): ?>
          <tr><th>Komentar GM</th><td><strong><?=nl2br(htmlspecialchars($f['gm_comment']))?></strong></td></tr>
          <?php endif; ?>
          <tr><th>Email PIC Terpilih</th>
            <td><?=htmlspecialchars($f['pic_email'] ?? '-')?></td>
          </tr>
          <tr><th>Email GM Terpilih</th>
            <td><?=htmlspecialchars($f['gm_email'] ?? '-')?></td>
          </tr>
          <?php if (!empty($f['gm_approved_name']) || !empty($f['gm_approved_email'])): ?>
          <tr><th>Approval GM</th>
            <td>
              <div><strong><?=htmlspecialchars($f['gm_approved_name'] ?? '-')?></strong></div>
              <div class="text-muted small">Waktu Approval: <?=!empty($f['gm_decision_at']) ? date('d/m/Y H:i', strtotime($f['gm_decision_at'])) : '-'?></div>
            </td>
          </tr>
          <?php endif; ?>
        </table>
        
        <div class="alert alert-info mt-3">
          <strong>Catatan:</strong> Data finansial dan file upload tidak ditampilkan untuk general user.
        </div>
      </div>
    </div>
  </div>
</body>
</html>

