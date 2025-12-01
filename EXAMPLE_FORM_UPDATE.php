<?php
/**
 * Updated form_rab.php - menggunakan tabel emails dari database
 * Salin code ini dan replace bagian dropdown di form_rab.php
 */

// Setelah session_start() dan login check, tambahkan:
$pdo = require_once __DIR__ . '/api/db.php';

// Load email options dari database
$pic_emails_stmt = $pdo->query("SELECT id, email, name FROM emails WHERE role = 'pic' ORDER BY name ASC");
$pic_emails = $pic_emails_stmt->fetchAll(PDO::FETCH_ASSOC);

$gm_emails_stmt = $pdo->query("SELECT id, email, name FROM emails WHERE role = 'gm' ORDER BY name ASC");
$gm_emails = $gm_emails_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Kemudian di bagian form, gunakan dropdown dengan loop: -->

<div class="col-md-6 mb-3">
    <label class="form-label">Pilih Email PIC Sales</label>
    <select id="picEmail" name="pic_email_id" class="form-select" required>
        <option value="">-- Pilih PIC Sales --</option>
        <?php foreach ($pic_emails as $email_option): ?>
            <option value="<?=$email_option['id']?>"><?=htmlspecialchars($email_option['name'] . ' (' . $email_option['email'] . ')')?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Pilih Email GM Sales</label>
    <select id="gmEmail" name="gm_email_id" class="form-select" required>
        <option value="">-- Pilih GM Sales --</option>
        <?php foreach ($gm_emails as $email_option): ?>
            <option value="<?=$email_option['id']?>"><?=htmlspecialchars($email_option['name'] . ' (' . $email_option['email'] . ')')?></option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Update assets/app.js untuk menggunakan ID bukan email string -->
<script>
// Di function handleFormSubmit atau saat kirim form:
const picEmailId = document.getElementById('picEmail').value;
const gmEmailId = document.getElementById('gmEmail').value;

// Instead of email string, kirim ID:
fd.append('pic_email_id', picEmailId);
fd.append('gm_email_id', gmEmailId);

// Atau jika perlu email string, query dari database di backend
</script>
