<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gm') {
    header('Location: ../login.html'); exit;
}
$pdo = require_once __DIR__ . '/../api/db.php';
$stmt = $pdo->prepare('SELECT * FROM rab_forms WHERE status = ? ORDER BY created_at DESC');
$stmt->execute(['pending_gm']);
$forms = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>Dashboard GM</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="d-flex justify-content-between mb-3">
    <h3>Dashboard GM - Pending Forms</h3>
    <div><a href="../logout.php" class="btn btn-sm btn-outline-secondary">Logout</a></div>
  </div>

  <table class="table table-striped">
    <thead><tr><th>ID</th><th>No Project</th><th>Nama</th><th>Client</th><th>Files</th><th>Komentar PIC</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach ($forms as $f): ?>
      <tr>
        <td><?=htmlspecialchars($f['id'])?></td>
        <td><?=htmlspecialchars($f['no_project'])?></td>
        <td><?=htmlspecialchars($f['nama_project'])?></td>
        <td><?=htmlspecialchars($f['client'])?></td>
        <td>
          <?php if ($f['file_spph']): ?><a href="../uploads/<?=htmlspecialchars($f['file_spph'])?>" download>SPPH</a><?php endif; ?>
          <?php if ($f['file_spk']): ?> | <a href="../uploads/<?=htmlspecialchars($f['file_spk'])?>" download>SPK</a><?php endif; ?>
          <?php if ($f['spk_link']): ?> | <a href="<?=htmlspecialchars($f['spk_link'])?>" target="_blank">SPK Link</a><?php endif; ?>
        </td>
        <td><?=!empty($f['pic_comment']) ? nl2br(htmlspecialchars(substr($f['pic_comment'], 0, 100))) . (strlen($f['pic_comment']) > 100 ? '...' : '') : '-'?></td>
        <td>
          <button class="btn btn-sm btn-success do-action" data-id="<?=$f['id']?>" data-action="approve">Approve</button>
          <button class="btn btn-sm btn-danger do-action" data-id="<?=$f['id']?>" data-action="reject">Reject</button>
          <a href="view_form.php?id=<?=$f['id']?>" class="btn btn-sm btn-outline-primary">View</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

<script>
document.querySelectorAll('.do-action').forEach(btn => btn.addEventListener('click', async e => {
  const id = e.target.dataset.id;
  const action = e.target.dataset.action;
  if (action === 'reject') {
    const reason = prompt('Masukkan alasan reject (wajib):');
    if (!reason) { alert('Alasan reject dibutuhkan'); return; }
    const fd = new FormData(); fd.append('id', id); fd.append('action', 'reject'); fd.append('reason', reason);
    const res = await fetch('../api/approve_gm.php', {method:'POST', body:fd});
    const j = await res.json(); alert(j.message); location.reload();
  } else {
    const comment = prompt('Masukkan komentar approval (opsional):');
    const fd = new FormData(); fd.append('id', id); fd.append('action', 'approve');
    if (comment) fd.append('comment', comment);
    const res = await fetch('../api/approve_gm.php', {method:'POST', body:fd});
    const j = await res.json(); alert(j.message); location.reload();
  }
}));
</script>
</body>
</html>
