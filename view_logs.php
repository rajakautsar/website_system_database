<?php
session_start();
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] === 'general')) {
    header('Location: login.php');
    exit;
}

$log_dir = __DIR__ . '/logs';
$log_files = [];
if (is_dir($log_dir)) {
    $files = scandir($log_dir);
    foreach ($files as $file) {
        if (preg_match('/^access_\d{4}-\d{2}-\d{2}\.log$/', $file)) {
            $log_files[] = $file;
        }
    }
    rsort($log_files); 
}

$selected_file = $_GET['file'] ?? ($log_files[0] ?? null);
$log_content = '';
if ($selected_file && file_exists($log_dir . '/' . $selected_file)) {
    $log_content = file_get_contents($log_dir . '/' . $selected_file);
    $lines = explode("\n", $log_content);
    $lines = array_reverse($lines); 
    $log_content = implode("\n", $lines);
}
?>
<?php $pageTitle = 'Logs'; include __DIR__ . '/includes/head.php'; ?>

<style>
.log-content { background: #1e1e1e; color: #d4d4d4; font-family: 'Courier New', monospace; font-size:12px; padding:15px; border-radius:5px; max-height:600px; overflow-y:auto; white-space:pre-wrap; word-wrap:break-word }
.log-line{margin-bottom:2px}
.log-redirect{color:#4ec9b0}
.log-login-success{color:#4fc1ff}
.log-login-failed{color:#f48771}
.log-page-access{color:#ce9178}
</style>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>📋 Access Logs</h3>
    <div>
      <a href="form_rab.php" class="btn btn-outline-secondary">Kembali</a>
      <a href="logout.php" class="btn btn-outline-danger ms-2">Logout</a>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <h5>Pilih File Log</h5>
      <form method="GET" class="d-flex gap-2">
        <select name="file" class="form-select" onchange="this.form.submit()">
          <?php foreach ($log_files as $file): ?>
            <option value="<?=htmlspecialchars($file)?>" <?=$file === $selected_file ? 'selected' : ''?>>
              <?=htmlspecialchars($file)?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Load</button>
      </form>
    </div>
  </div>

  <?php if ($selected_file): ?>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Log: <?=htmlspecialchars($selected_file)?></h5>
      <div>
        <button onclick="clearLog()" class="btn btn-sm btn-danger">Clear Log</button>
        <button onclick="location.reload()" class="btn btn-sm btn-primary">Refresh</button>
      </div>
    </div>
    <div class="card-body">
      <div class="log-content" id="logContent">
        <?php
        if ($log_content) {
          $lines = explode("\n", $log_content);
          foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            $class = '';
            if (strpos($line, 'REDIRECT') !== false) $class = 'log-redirect';
            elseif (strpos($line, 'LOGIN SUCCESS') !== false) $class = 'log-login-success';
            elseif (strpos($line, 'LOGIN FAILED') !== false) $class = 'log-login-failed';
            elseif (strpos($line, 'PAGE ACCESS') !== false) $class = 'log-page-access';
            echo '<div class="log-line ' . $class . '">' . htmlspecialchars($line) . '</div>';
          }
        } else {
          echo '<div class="text-muted">Log file kosong atau tidak ditemukan.</div>';
        }
        ?>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="alert alert-info">Belum ada file log yang tersedia.</div>
  <?php endif; ?>
</div>

<script>
function clearLog() {
  if (confirm('Yakin ingin menghapus log ini? Tindakan ini tidak dapat dibatalkan.')) {
    const file = '<?=htmlspecialchars($selected_file ?? '')?>';
    if (file) {
      fetch('api/clear_log.php?file=' + encodeURIComponent(file), {method: 'POST'})
        .then(() => location.reload());
    }
  }
}
</script>
<?php include __DIR__ . '/includes/scripts.php'; ?>

