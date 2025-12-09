<?php
session_start();
require_once __DIR__ . '/api/log_helper.php';
logPageAccess('approved_gm_forms.php');

// Redirect general user ke halaman khusus mereka
if (isset($_SESSION['role']) && $_SESSION['role'] === 'general') {
    logRedirect('approved_gm_forms.php', 'general_approved_forms.php', 'General user redirect');
    header('Location: general_approved_forms.php');
    exit;
}

$pdo = require_once __DIR__ . '/api/db.php';
$stmt = $pdo->prepare('SELECT * FROM rab_forms WHERE status = ? ORDER BY updated_at DESC');
$stmt->execute(['approved_final']);
$forms = $stmt->fetchAll();
$is_general_user = false; // Setelah redirect, ini tidak akan pernah true
$from_dashboard = ($_GET['from'] ?? null) === 'dashboard'; // Cek apakah datang dari dashboard
?>
<?php $pageTitle = 'Approved GM - RAB System'; include __DIR__ . '/includes/head.php'; ?>
<div class="page-wrapper">
  <div class="container py-4">
    <header class="d-flex justify-content-between align-items-center">
    <div>
      <h3>Form Disetujui GM</h3>
      <p class="text-muted mb-0">Daftar project yang sudah lolos pengecekan PIC dan disetujui GM.</p>
    </div>
    <div>
      <?php if (isset($_SESSION['role'])): ?>
        <a href="logout.php" class="btn btn-sm btn-outline-danger me-2">Logout</a>
      <?php endif; ?>
      <?php if (!$is_general_user && !$from_dashboard): ?>
        <a href="form_rab.php" class="btn btn-outline-secondary">⬅️ Kembali</a>
      <?php endif; ?>
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
          <th>Event Date</th>
          <?php if (!$is_general_user): ?>
          <th>RAB Submit</th>
          <th>Profit Margin</th>
          <th>File</th>
          <?php endif; ?>
          <th>PIC Checker</th>
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
          <td><?=date('d/m/Y', strtotime($f['event_date_start']))?> S.d <?=date('d/m/Y', strtotime($f['event_date_end']))?></td>
          <?php if (!$is_general_user): ?>
          <td>Rp <?=number_format($f['rab_submit'],0,',','.')?></td>
          <td>Rp <?=number_format($f['profit_margin'],0,',','.')?> (<?=htmlspecialchars($f['profit_percentage'])?>%)</td>
          <td>
            <?php if ($f['file_spph']): ?>
              <a href="uploads/<?=htmlspecialchars($f['file_spph'])?>" class="badge bg-primary text-decoration-none" download>SPPH</a>
            <?php endif; ?>
            <?php if ($f['file_spk']): ?>
              <a href="uploads/<?=htmlspecialchars($f['file_spk'])?>" class="badge bg-success text-decoration-none ms-1" download>SPK</a>
            <?php endif; ?>
            <?php if ($f['spk_link']): ?>
              <a href="<?=htmlspecialchars($f['spk_link'])?>" target="_blank" class="badge bg-info text-decoration-none ms-1">Link SPK</a>
            <?php endif; ?>
          </td>
          <?php endif; ?>
          <td>
            <?php if ($f['pic_approved_name']): ?>
              <div><strong><?=htmlspecialchars($f['pic_approved_name'])?></strong></div>
              <div class="small text-muted">Email PIC: <span class="fw-bold text-primary"><?=htmlspecialchars($f['pic_email'] ?? '-')?></span></div>
              <div class="small text-muted"><?=htmlspecialchars($f['pic_decision_at'])?></div>
              <?php if (!empty($f['pic_comment'])): ?>
                <div><?=nl2br(htmlspecialchars($f['pic_comment']))?></div>
              <?php endif; ?>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($f['gm_approved_name']): ?>
              <div><strong><?=htmlspecialchars($f['gm_approved_name'])?></strong></div>
              <div class="small text-muted">Email GM: <span class="fw-bold text-primary"><?=htmlspecialchars($f['gm_email'] ?? '-')?></span></div>
              <div class="small text-muted"><?=htmlspecialchars($f['gm_decision_at'])?></div>
              <?php if (!empty($f['gm_comment'])): ?>
                <div><?=nl2br(htmlspecialchars($f['gm_comment']))?></div>
              <?php endif; ?>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </td>
          <td><a class="btn btn-sm btn-outline-primary" href="dashboard/view_form.php?id=<?=urlencode($f['id'])?>">Detail</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
  </div>
</div>
<?php include __DIR__ . '/includes/scripts.php'; ?>


