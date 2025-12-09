<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gm') {
    header('Location: ../login.php'); exit;
}
$pdo = require_once __DIR__ . '/../api/db.php';
$stmt = $pdo->prepare('SELECT * FROM rab_forms WHERE status = ? ORDER BY created_at DESC, id DESC');
$stmt->execute(['pending_gm']);
$forms = $stmt->fetchAll();
?>
<?php $pageTitle = 'Dashboard GM'; include __DIR__ . '/../includes/head.php'; ?>
<div class="page-wrapper">
  <div class="container py-4">
    <header class="d-flex justify-content-between align-items-center">
      <div>
        <h2>Dashboard GM</h2>
        <p class="text-muted">Pending Forms untuk Approval</p>
      </div>
      <div class="d-flex gap-2">
        <a href="../approved_gm_forms.php?from=dashboard" class="btn btn-success">Form Disetujui</a>
        <a href="../logout.php" class="btn btn-outline-secondary">Logout</a>
      </div>
    </header>

    <div class="table-responsive mt-4">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>No Project</th>
            <th>Nama</th>
            <th>Client</th>
            <th>Tanggal Masuk</th>
            <th>Files</th>
            <th>Komentar PIC</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($forms as $f): ?>
          <tr>
            <td><?=htmlspecialchars($f['id'])?></td>
            <td><?=htmlspecialchars($f['no_project'])?></td>
            <td><?=htmlspecialchars($f['nama_project'])?></td>
            <td><?=htmlspecialchars($f['client'])?></td>
            <td><?=date('d/m/Y', strtotime($f['created_at']))?></td>
            <td>
              <?php if ($f['file_spph']): ?><a href="../uploads/<?=htmlspecialchars($f['file_spph'])?>" class="badge bg-primary" download>SPPH</a><?php endif; ?>
              <?php if ($f['file_spk']): ?> <a href="../uploads/<?=htmlspecialchars($f['file_spk'])?>" class="badge bg-success" download>SPK</a><?php endif; ?>
              <?php if ($f['spk_link']): ?> <a href="<?=htmlspecialchars($f['spk_link'])?>" target="_blank" class="badge bg-info">Link</a><?php endif; ?>
            </td>
            <td><small><?=!empty($f['pic_comment']) ? nl2br(htmlspecialchars(substr($f['pic_comment'], 0, 60))) . (strlen($f['pic_comment']) > 60 ? '...' : '') : '-'?></small></td>
            <td>
              <div class="btn-group btn-group-sm">
                <button class="btn btn-success do-action" data-id="<?=$f['id']?>" data-action="approve">Approve</button>
                <button class="btn btn-danger do-action" data-id="<?=$f['id']?>" data-action="reject">Reject</button>
                <a href="view_form.php?id=<?=$f['id']?>" class="btn btn-outline-primary">View</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/scripts.php'; ?>

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
    const res = await fetch('../api/approve_gm.php', { method: 'POST', body: fd });
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
<?php include __DIR__ . '/../includes/scripts.php'; ?>
