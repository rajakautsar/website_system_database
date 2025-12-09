<?php
$pdo = require_once __DIR__ . '/api/db.php';
$stmt = $pdo->prepare('SELECT * FROM rab_forms WHERE status = ? ORDER BY created_at DESC');
$stmt->execute(['approved_final']);
$forms = $stmt->fetchAll();
?>
<?php $pageTitle = 'Approved Forms'; include __DIR__ . '/includes/head.php'; ?>
<div class="p-4">
  <h3>Approved Forms (Final)</h3>
  <table class="table table-striped">
    <thead><tr><th>ID</th><th>No Project</th><th>Nama</th><th>Client</th><th>Event Date</th><th>Files</th></tr></thead>
    <tbody>
    <?php foreach($forms as $f): ?>
      <tr>
        <td><?=htmlspecialchars($f['id'])?></td>
        <td><?=htmlspecialchars($f['no_project'])?></td>
        <td><?=htmlspecialchars($f['nama_project'])?></td>
        <td><?=htmlspecialchars($f['client'])?></td>
        <td><?=htmlspecialchars($f['event_date_start'])?> - <?=htmlspecialchars($f['event_date_end'])?></td>
        <td>
          <?php if ($f['file_spph']): ?><a href="uploads/<?=htmlspecialchars($f['file_spph'])?>" download>SPPH</a><?php endif; ?>
          <?php if ($f['file_spk']): ?> | <a href="uploads/<?=htmlspecialchars($f['file_spk'])?>" download>SPK</a><?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php include __DIR__ . '/includes/scripts.php'; ?>
