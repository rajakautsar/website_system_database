<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pic') {
    header('Location: ../login.html'); exit;
}
$pdo = require_once __DIR__ . '/../api/db.php';
$stmt = $pdo->prepare('SELECT * FROM rab_forms WHERE status = ? ORDER BY created_at DESC, id DESC');
$stmt->execute(['pending_pic']);
$forms = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>Dashboard PIC</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="d-flex justify-content-between mb-3">
    <h3>Dashboard PIC - Pending Forms</h3>
    <div>
      <a href="../logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
    </div>
  </div>

  <table class="table table-striped">
    <thead><tr><th>ID</th><th>No Project</th><th>Nama</th><th>Client</th><th>Tanggal Masuk</th><th>Files</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach ($forms as $f): ?>
      <tr>
        <td><?=htmlspecialchars($f['id'])?></td>
        <td><?=htmlspecialchars($f['no_project'])?></td>
        <td><?=htmlspecialchars($f['nama_project'])?></td>
        <td><?=htmlspecialchars($f['client'])?></td>
        <td><?=date('d/m/Y H:i', strtotime($f['created_at']))?></td>
        <td>
          <?php if ($f['file_spph']): ?><a href="../uploads/<?=htmlspecialchars($f['file_spph'])?>" download>SPPH</a><?php endif; ?>
          <?php if ($f['file_spk']): ?> | <a href="../uploads/<?=htmlspecialchars($f['file_spk'])?>" download>SPK</a><?php endif; ?>
          <?php if ($f['spk_link']): ?> | <a href="<?=htmlspecialchars($f['spk_link'])?>" target="_blank">SPK Link</a><?php endif; ?>
        </td>
        <td>
          <button class="btn btn-sm btn-success do-action" data-id="<?=$f['id']?>" data-action="approve">Approve</button>
          <button class="btn btn-sm btn-danger do-action" data-id="<?=$f['id']?>" data-action="reject">Reject</button>
          <a href="view_form.php?id=<?=$f['id']?>" class="btn btn-sm btn-outline-primary">View</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <!-- PIN Modal -->
  <div class="modal fade" id="pinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Verifikasi PIN</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p id="pinAction"></p>
          <input type="password" id="pinInput" class="form-control" placeholder="Masukkan PIN Anda" maxlength="4">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" id="pinSubmitBtn">Verifikasi</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let pendingAction = { id: null, action: null, reason: null, comment: null };
const pinModal = new bootstrap.Modal(document.getElementById('pinModal'));

document.querySelectorAll('.do-action').forEach(btn => btn.addEventListener('click', e => {
  const id = e.target.dataset.id;
  const action = e.target.dataset.action;
  
  if (action === 'reject') {
    const reason = prompt('Masukkan alasan reject (wajib):');
    if (!reason) { alert('Alasan reject dibutuhkan'); return; }
    pendingAction = { id, action, reason, comment: null };
    document.getElementById('pinAction').textContent = 'Masukkan PIN untuk REJECT form ini.';
    document.getElementById('pinInput').value = '';
    pinModal.show();
  } else {
    const comment = prompt('Masukkan komentar approval (opsional):');
    pendingAction = { id, action, comment };
    document.getElementById('pinAction').textContent = 'Masukkan PIN untuk APPROVE form ini.';
    document.getElementById('pinInput').value = '';
    pinModal.show();
  }
}));

document.getElementById('pinSubmitBtn').addEventListener('click', async () => {
  const pin = document.getElementById('pinInput').value;
  if (!pin) { alert('PIN harus diisi'); return; }
  
  const fd = new FormData();
  fd.append('id', pendingAction.id);
  fd.append('action', pendingAction.action);
  fd.append('pin', pin);
  if (pendingAction.reason) fd.append('reason', pendingAction.reason);
  if (pendingAction.comment) fd.append('comment', pendingAction.comment);
  
  try {
    const res = await fetch('../api/approve_pic.php', { method: 'POST', body: fd });
    const j = await res.json();
    alert(j.message);
    if (j.success) location.reload();
    else pinModal.show();
  } catch (err) {
    alert('Error: ' + err.message);
    pinModal.show();
  }
});
</script>
</body>
</html>
