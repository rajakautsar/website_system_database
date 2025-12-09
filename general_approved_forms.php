<?php
session_start();
require_once __DIR__ . '/api/log_helper.php';
logPageAccess('general_approved_forms.php');

// Hanya general user yang bisa akses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'general') {
    logRedirect('general_approved_forms.php', 'login.php', 'Unauthorized access');
    header('Location: login.php');
    exit;
}

$pdo = require_once __DIR__ . '/api/db.php';
$stmt = $pdo->prepare('SELECT * FROM rab_forms WHERE status = ? ORDER BY updated_at DESC');
$stmt->execute(['approved_final']);
$forms = $stmt->fetchAll();
?>
<?php $pageTitle = 'Form Disetujui GM - General User'; include __DIR__ . '/includes/head.php'; ?>
<div class="page-wrapper">
  <div class="container py-4">
    <header class="d-flex justify-content-between align-items-center">
    <div>
      <h3>Form Disetujui GM</h3>
      <p class="text-muted mb-0">Daftar project yang sudah disetujui oleh General Manager.</p>
    </div>
    <div>
      <a href="logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
    </div>
  </div>

  <?php if (empty($forms)): ?>
    <div class="alert alert-info">Belum ada form yang disetujui GM.</div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>No Project</th>
          <th>Nama Project</th>
          <th>Client</th>
          <th>Venue</th>
          <th>Event Date</th>
          <th>GM Checker</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($forms as $f): ?>
        <tr>
          <td><?=htmlspecialchars($f['id'])?></td>
          <td><?=htmlspecialchars($f['no_project'])?></td>
          <td><?=htmlspecialchars($f['nama_project'])?></td>
          <td><?=htmlspecialchars($f['client'])?></td>
          <td><?=htmlspecialchars($f['venue'])?></td>
          <td><?=date('d/m/Y', strtotime($f['event_date_start']))?> S.d <?=date('d/m/Y', strtotime($f['event_date_end']))?></td>
          <td>
            <?php if ($f['gm_approved_name']): ?>
              <div><strong><?=htmlspecialchars($f['gm_approved_name'])?></strong></div>
              <div class="small text-muted">Email GM: <span class="fw-bold text-primary"><?=htmlspecialchars($f['gm_email'] ?? '-')?></span></div>
              <div class="small text-muted">Waktu: <?=!empty($f['gm_decision_at']) ? date('d/m/Y H:i', strtotime($f['gm_decision_at'])) : '-'?></div>
              <?php if (!empty($f['gm_comment'])): ?>
                <div><?=nl2br(htmlspecialchars($f['gm_comment']))?></div>
              <?php endif; ?>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </td>
          <td><a class="btn btn-sm btn-outline-primary" href="dashboard/view_form_general.php?id=<?=urlencode($f['id'])?>">Detail</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
  </div>
</div>
<?php include __DIR__ . '/includes/scripts.php'; ?>

