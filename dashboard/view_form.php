<?php
session_start();
require_once __DIR__ . '/../api/log_helper.php';
$id = $_GET['id'] ?? null;
logPageAccess('dashboard/view_form.php', "id=$id");

// Redirect general user ke halaman khusus mereka
if (isset($_SESSION['role']) && $_SESSION['role'] === 'general') {
    if ($id) {
        logRedirect('dashboard/view_form.php', 'dashboard/view_form_general.php', "General user redirect - id=$id");
        header('Location: view_form_general.php?id=' . urlencode($id));
    } else {
        logRedirect('dashboard/view_form.php', 'general_approved_forms.php', 'General user redirect - no id');
        header('Location: ../general_approved_forms.php');
    }
    exit;
}

$pdo = require_once __DIR__ . '/../api/db.php';
$id = $_GET['id'] ?? null;
if (!$id) { echo "Form ID required"; exit; }
$stmt = $pdo->prepare('SELECT * FROM rab_forms WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$f = $stmt->fetch();
if (!$f) { echo "Form not found"; exit; }
$is_general_user = false; // Setelah redirect, ini tidak akan pernah true
?>
<?php $pageTitle = 'View Form'; include __DIR__ . '/../includes/head.php'; ?>
<div class="p-4">
  <h4>Detail Form #<?=htmlspecialchars($f['id'])?></h4>
  <table class="table table-bordered">
    <tr><th>Category</th><td><?=htmlspecialchars($f['category'])?></td></tr>
    <tr><th>No Project</th><td><?=htmlspecialchars($f['no_project'])?></td></tr>
    <tr><th>Nama Project</th><td><?=htmlspecialchars($f['nama_project'])?></td></tr>
    <tr><th>Client</th><td><?=htmlspecialchars($f['client'])?></td></tr>
    <tr><th>Venue</th><td><?=htmlspecialchars($f['venue'])?></td></tr>
    <tr><th>Event Date</th><td><?=htmlspecialchars($f['event_date_start'])?> - <?=htmlspecialchars($f['event_date_end'])?></td></tr>
    <tr><th>Remarks</th><td><?=nl2br(htmlspecialchars($f['remarks']))?></td></tr>
    <?php if (!$is_general_user): ?>
    <tr><th>RAB Submit</th><td><?=htmlspecialchars($f['rab_submit'])?></td></tr>
    <tr><th>RAB Internal</th><td><?=htmlspecialchars($f['rab_internal'])?></td></tr>
    <tr><th>Profit</th><td><?=htmlspecialchars($f['profit_margin'])?> (<?=htmlspecialchars($f['profit_percentage'])?>%)</td></tr>
    <tr><th>Files</th>
      <td>
        <?php if ($f['file_spph']): ?><a href="../uploads/<?=htmlspecialchars($f['file_spph'])?>" download>Download SPPH</a><br><?php endif; ?>
        <?php if ($f['file_spk']): ?><a href="../uploads/<?=htmlspecialchars($f['file_spk'])?>" download>Download SPK</a><br><?php endif; ?>
        <?php if ($f['spk_link']): ?><a href="<?=htmlspecialchars($f['spk_link'])?>" target="_blank">SPK Link</a><?php endif; ?>
      </td>
    </tr>
    <?php endif; ?>
    <tr><th>Status</th><td><?=htmlspecialchars($f['status'])?></td></tr>
    <tr><th>Reject Reason</th><td><?=nl2br(htmlspecialchars($f['reject_reason'] ?? '-'))?></td></tr>
    <?php if (!empty($f['pic_comment'])): ?>
    <tr><th>Komentar PIC Sales</th><td><?=nl2br(htmlspecialchars($f['pic_comment']))?></td></tr>
    <?php endif; ?>
    <?php if (!empty($f['gm_comment'])): ?>
    <tr><th>Komentar GM</th><td><?=nl2br(htmlspecialchars($f['gm_comment']))?></td></tr>
    <?php endif; ?>
    <tr><th>Email PIC Terpilih</th>
      <td><?=htmlspecialchars($f['pic_email'] ?? '-')?></td>
    </tr>
    <tr><th>Email GM Terpilih</th>
      <td><?=htmlspecialchars($f['gm_email'] ?? '-')?></td>
    </tr>
    <?php if (!empty($f['pic_approved_name']) || !empty($f['pic_approved_email'])): ?>
    <tr><th>Approval PIC</th>
      <td>
        <div><strong><?=htmlspecialchars($f['pic_approved_name'] ?? '-')?></strong></div>
        <div class="text-muted small">Waktu Approval: <?=!empty($f['pic_decision_at']) ? date('d/m/Y H:i', strtotime($f['pic_decision_at'])) : '-'?></div>
      </td>
    </tr>
    <?php endif; ?>
    <?php if (!empty($f['gm_approved_name']) || !empty($f['gm_approved_email'])): ?>
    <tr><th>Approval GM</th>
      <td>
        <div><strong><?=htmlspecialchars($f['gm_approved_name'] ?? '-')?></strong></div>
        <div class="text-muted small">Waktu Approval: <?=!empty($f['gm_decision_at']) ? date('d/m/Y H:i', strtotime($f['gm_decision_at'])) : '-'?></div>
      </td>
    </tr>
    <?php endif; ?>
  </table>
  <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
<?php include __DIR__ . '/../includes/scripts.php'; ?>
