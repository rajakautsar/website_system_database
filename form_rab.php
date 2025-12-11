<?php
session_start();
require_once __DIR__ . '/api/log_helper.php';
logPageAccess('form_rab.php');

if (!isset($_SESSION['user_id'])) {
    logRedirect('form_rab.php', 'login.php', 'No session');
    header('Location: login.php');
    exit;
}
// Redirect general user langsung ke general_approved_forms.php
if (isset($_SESSION['role']) && $_SESSION['role'] === 'general') {
    logRedirect('form_rab.php', 'general_approved_forms.php', 'General user redirect');
    header('Location: general_approved_forms.php');
    exit;
}
$is_general_user = false; // Setelah redirect, ini tidak akan pernah true
// load email config for dynamic dropdowns
$emailConfig = file_exists(__DIR__ . '/api/email_config.php') ? require __DIR__ . '/api/email_config.php' : [];

// Helper: Extract clean email from "Name (email@domain.com)" format
function extractEmail($formatted) {
    if (strpos($formatted, '(') !== false && strpos($formatted, ')') !== false) {
        preg_match('/\(([^)]+)\)/', $formatted, $matches);
        return $matches[1] ?? $formatted;
    }
    return $formatted;
}

$picOptions = array_map('extractEmail', $emailConfig['roles']['pic']['force_to'] ?? []);
$gmOptions = array_map('extractEmail', $emailConfig['roles']['gm']['force_to'] ?? []);
?>
<?php $pageTitle = 'Form Pendaftaran RAB'; include __DIR__ . '/includes/head.php'; ?>
<div class="page-wrapper">
  <div class="container py-4">
    <header class="d-flex justify-content-between align-items-center">
      <div>
        <h1>Form Pendaftaran RAB</h1>
        <p class="text-muted">Sistem Database Project Management</p>
      </div>
      <div class="d-flex gap-2">
        <a href="rejected_forms.php" class="btn btn-outline-danger">Riwayat Reject</a>
        <a href="approved_gm_forms.php" class="btn btn-success">List Project</a>
        <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
      </div>
    </header>

    <div id="alerts"></div>

    <form id="rabForm" enctype="multipart/form-data">
    <div class="card mb-3">
      <div class="card-body">
        <h5>Informasi Project</h5>
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Category</label>
            <select id="category" name="category" class="form-select">
              <option value="kp">Kontrak Payung (KP)</option>
              <option value="sl">Non Kontrak Payung (SL)</option>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">No Project</label>
            <input id="noProject" name="no_project" class="form-control" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Nama Project</label>
            <input id="namaProject" name="nama_project" class="form-control" required>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Client</label>
            <input id="client" name="client" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Venue</label>
            <input id="venue" name="venue" class="form-control" required>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Pilih Email PIC Sales</label>
            <select id="picEmail" name="pic_email" class="form-select" required>
              <?php if (!empty($picOptions)): ?>
                <?php foreach ($picOptions as $p): ?>
                  <option value="<?=htmlspecialchars($p)?>"><?=htmlspecialchars($p)?></option>
                <?php endforeach; ?>
              <?php else: ?>
                <option value="arifa@dyandraeventsolutions.com">Arifa (arifa@dyandraeventsolutions.com)</option>
                <option value="irfant.giant@dyandraeventsolutions.com">Irfant (irfant.giant@dyandraeventsolutions.com)</option>
                <option value="dellaazkia@dyandraeventsolutions.com">Della (dellaazkia@dyandraeventsolutions.com)</option>
              <?php endif; ?>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Pilih Email GM Sales</label>
            <select id="gmEmail" name="gm_email" class="form-select" required>
              <?php if (!empty($gmOptions)): ?>
                <?php foreach ($gmOptions as $g): ?>
                  <option value="<?=htmlspecialchars($g)?>"><?=htmlspecialchars($g)?></option>
                <?php endforeach; ?>
              <?php else: ?>
                <option value="andysoekasah@dyandraeventsolutions.com">Andy Soekasah (andysoekasah@dyandraeventsolutions.com)</option>
                <option value="tessya@dyandraeventsolutions.com">Tessya (tessya@dyandraeventsolutions.com)</option>
                <option value="bahri@dyandraeventsolutions.com">Bahri (bahri@dyandraeventsolutions.com)</option>
                <option value="admin@dyandraeventsolutions.com">Tes (admin@dyandraeventsolutions.com)</option>
              <?php endif; ?>
            </select>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Event Date Start</label>
            <input id="eventDateStart" name="event_date_start" type="date" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Event Date End</label>
            <input id="eventDateEnd" name="event_date_end" type="date" class="form-control" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Remarks</label>
          <textarea id="remarks" name="remarks" class="form-control"></textarea>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <h5>Data Finansial</h5>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>RAB Submit (Rp)</label>
            <input id="rabSubmit" name="rab_submit" type="number" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>RAB Internal (Rp)</label>
            <input id="rabInternal" name="rab_internal" type="number" class="form-control" required>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Profit Margin (Rp)</label>
            <input id="profitMargin" name="profit_margin" type="number" readonly class="form-control">
          </div>
          <div class="col-md-6 mb-3">
            <label>Profit Percentage (%)</label>
            <input id="profitPercentage" name="profit_percentage" type="text" readonly class="form-control">
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <h5>Upload File</h5>
        <div class="mb-3">
          <label>Upload SPPH (wajib - pdf/docx)</label>
          <input id="uploadSPPH" name="file_spph" type="file" accept=".pdf,.docx" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Upload SPK (opsional - file)</label>
          <input id="uploadSPK" name="file_spk" type="file" accept=".pdf,.docx" class="form-control">
          <div class="form-text">Atau isi link SPK (jika SPK belum tersedia)</div>
          <input id="spkLink" name="spk_link" class="form-control mt-1" placeholder="Link SPK (opsional)">
        </div>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="reset" class="btn btn-accent-soft">🔄 Reset</button>
      <button type="button" id="previewBtn" class="btn btn-primary">👁️ Preview</button>
    </div>
  </form>
</div>

<!-- preview modal -->
<div id="previewModal" class="modal" tabindex="-1" style="display:none; background: rgba(0,0,0,0.5);">
  <div class="modal-dialog modal-lg">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5>Preview Data</h5>
        <button id="closeModal" class="btn-close"></button>
      </div>
      <div class="modal-body">
        <div id="previewContent"></div>
      </div>
      <div class="modal-footer">
        <button id="editBtn" class="btn btn-secondary">Edit</button>
        <button id="confirmSubmitBtn" class="btn btn-success">Confirm Submit</button>
      </div>
    </div>
  </div>
</div>
  </div>
</div>

<?php include __DIR__ . '/includes/scripts.php'; ?>

