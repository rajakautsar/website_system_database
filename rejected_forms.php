<?php
$pdo = require_once __DIR__ . '/api/db.php';
$stmt = $pdo->query("SELECT * FROM rab_forms WHERE status IN ('rejected_pic','rejected_gm') ORDER BY updated_at DESC");
$forms = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Rejected Forms</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-4">
  <h3>Riwayat Form Ditolak</h3>
  <div class="mb-3">
    <a href="form_rab.php" class="btn btn-sm btn-secondary">&larr; Kembali ke Form Pendaftaran RAB</a>
  </div>
  <table class="table table-striped">
    <thead><tr><th>ID</th><th>No Project</th><th>Status</th><th>Ditolak Oleh</th><th>Alasan</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach($forms as $f): ?>
      <tr id="row-<?=htmlspecialchars($f['id'])?>">
        <td><?=htmlspecialchars($f['id'])?></td>
        <td><?=htmlspecialchars($f['no_project'])?></td>
        <td><?=htmlspecialchars($f['status'])?></td>
        <td>
          <?php if ($f['status'] === 'rejected_pic'): ?>
            <div><strong><?=htmlspecialchars($f['pic_approved_name'] ?? '-')?></strong></div>
            <div class="small text-muted"><?=htmlspecialchars($f['pic_approved_email'] ?? '-')?></div>
          <?php else: ?>
            <div><strong><?=htmlspecialchars($f['gm_approved_name'] ?? '-')?></strong></div>
            <div class="small text-muted"><?=htmlspecialchars($f['gm_approved_email'] ?? '-')?></div>
          <?php endif; ?>
        </td>
        <td><?=nl2br(htmlspecialchars($f['reject_reason']))?></td>
        <td>
          <button class="btn btn-sm btn-primary" onclick='openEditModal(<?=json_encode($f)?>)'>Edit & Submit Ulang</button>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <!-- Bootstrap JS (bundle includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  function openEditModal(data) {
    // create modal if not exists
    const modal = document.getElementById('editModal');
    if (!modal) return alert('Modal editor tidak ditemukan');

    // helper to get element by id or fallback to form element
    function getEl(id, name) {
      return document.getElementById(id) || (document.forms['resubmitForm'] ? document.forms['resubmitForm'].elements[name] : null);
    }

    // populate fields with safe checks
    const el_id = getEl('edit_id', 'id'); if (!el_id) return alert('Editor input tidak ditemukan'); el_id.value = data.id;
    const el_category = getEl('edit_category', 'category'); if (el_category) el_category.value = data.category || 'kp';
    const el_no = getEl('edit_no_project', 'no_project'); if (el_no) el_no.value = data.no_project || '';
    const el_nama = getEl('edit_nama_project', 'nama_project'); if (el_nama) el_nama.value = data.nama_project || '';
    const el_client = getEl('edit_client', 'client'); if (el_client) el_client.value = data.client || '';
    const el_venue = getEl('edit_venue', 'venue'); if (el_venue) el_venue.value = data.venue || '';
    const el_start = getEl('edit_event_date_start', 'event_date_start'); if (el_start) el_start.value = data.event_date_start || '';
    const el_end = getEl('edit_event_date_end', 'event_date_end'); if (el_end) el_end.value = data.event_date_end || '';
    const el_contract = getEl('edit_contract_type', 'contract_type'); if (el_contract) el_contract.value = data.contract_type || '';
    const el_remarks = getEl('edit_remarks', 'remarks'); if (el_remarks) el_remarks.value = data.remarks || '';
    const el_rab_submit = getEl('edit_rab_submit', 'rab_submit'); if (el_rab_submit) el_rab_submit.checked = !!Number(data.rab_submit);
    const el_rab_internal = getEl('edit_rab_internal', 'rab_internal'); if (el_rab_internal) el_rab_internal.checked = !!Number(data.rab_internal);
    const el_profit_margin = getEl('edit_profit_margin', 'profit_margin'); if (el_profit_margin) el_profit_margin.value = data.profit_margin || 0;
    const el_profit_percentage = getEl('edit_profit_percentage', 'profit_percentage'); if (el_profit_percentage) el_profit_percentage.value = data.profit_percentage || 0;
    const el_spk_link = getEl('edit_spk_link', 'spk_link'); if (el_spk_link) el_spk_link.value = data.spk_link || '';
      const el_pic_email = getEl('edit_pic_email', 'pic_email'); if (el_pic_email) el_pic_email.value = data.pic_email || '';
      const el_gm_email = getEl('edit_gm_email', 'gm_email'); if (el_gm_email) el_gm_email.value = data.gm_email || '';
      const hiddenPic = document.getElementById('hidden_pic_email'); if (hiddenPic) hiddenPic.value = data.pic_email || '';
      const hiddenGm = document.getElementById('hidden_gm_email'); if (hiddenGm) hiddenGm.value = data.gm_email || '';

    // show existing filenames
    const cur_spph = document.getElementById('current_spph'); if (cur_spph) cur_spph.textContent = data.file_spph || '-';
    const cur_spk = document.getElementById('current_spk'); if (cur_spk) cur_spk.textContent = data.file_spk || '-';

    // show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
  }

  async function submitResubmitForm(e) {
    e.preventDefault();
    const form = document.getElementById('resubmitForm');
    const fd = new FormData(form);
    const submitBtn = document.getElementById('resubmitSubmit');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Menyimpan...';

    try {
      const res = await fetch('api/resubmit_form.php', { method: 'POST', body: fd });
      const json = await res.json();
      alert(json.message || (json.success ? 'Berhasil' : 'Gagal'));
      if (json.success) {
        // reload page to refresh list
        location.reload();
      }
    } catch (err) {
      alert('Error: ' + err.message);
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Submit Ulang';
    }
  }
  </script>

  <!-- Edit / Resubmit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="resubmitForm" onsubmit="submitResubmitForm(event)" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit & Submit Ulang Form Ditolak</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit_id">
          <div class="row">
            <div class="col-md-6 mb-2"><label>No Project</label><input class="form-control" name="no_project" id="edit_no_project" required></div>
            <div class="col-md-6 mb-2"><label>Nama Project</label><input class="form-control" name="nama_project" id="edit_nama_project" required></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-2"><label>Client</label><input class="form-control" name="client" id="edit_client" required></div>
            <div class="col-md-6 mb-2"><label>Venue</label><input class="form-control" name="venue" id="edit_venue" required></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-2"><label>Start Date</label><input type="date" class="form-control" name="event_date_start" id="edit_event_date_start" required></div>
            <div class="col-md-6 mb-2"><label>End Date</label><input type="date" class="form-control" name="event_date_end" id="edit_event_date_end" required></div>
          </div>
          <div class="mb-2"><label>Contract Type</label><input class="form-control" name="contract_type" id="edit_contract_type" required></div>
          <div class="mb-2"><label>Remarks</label><textarea class="form-control" name="remarks" id="edit_remarks"></textarea></div>
          <div class="row">
            <div class="col-md-3 mb-2"><label>RAB Submit</label><div class="form-check"><input class="form-check-input" type="checkbox" id="edit_rab_submit" name="rab_submit" value="1"></div></div>
            <div class="col-md-3 mb-2"><label>RAB Internal</label><div class="form-check"><input class="form-check-input" type="checkbox" id="edit_rab_internal" name="rab_internal" value="1"></div></div>
            <div class="col-md-3 mb-2"><label>Profit Margin</label><input class="form-control" name="profit_margin" id="edit_profit_margin" type="number" step="0.01"></div>
            <div class="col-md-3 mb-2"><label>Profit %</label><input class="form-control" name="profit_percentage" id="edit_profit_percentage" type="number" step="0.01"></div>
          </div>
          <div class="mb-2"><label>SPK Link</label><input class="form-control" name="spk_link" id="edit_spk_link" type="url"></div>
          <div class="mb-2">
            <label>File SPPH (current: <span id="current_spph">-</span>)</label>
            <input type="file" name="file_spph" class="form-control">
          </div>
          <div class="mb-2">
            <label>File SPK (current: <span id="current_spk">-</span>)</label>
            <input type="file" name="file_spk" class="form-control">
          </div>
            <div class="row">
              <div class="col-md-6 mb-2"><label>PIC Email (terpilih)</label><input readonly class="form-control-plaintext" id="edit_pic_email" name="pic_email_display"></div>
              <div class="col-md-6 mb-2"><label>GM Email (terpilih)</label><input readonly class="form-control-plaintext" id="edit_gm_email" name="gm_email_display"></div>
            </div>
            <!-- keep hidden values to post if needed, but readonly display prevents changing -->
            <input type="hidden" name="pic_email" id="hidden_pic_email">
            <input type="hidden" name="gm_email" id="hidden_gm_email">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button id="resubmitSubmit" type="submit" class="btn btn-primary">Submit Ulang</button>
        </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
