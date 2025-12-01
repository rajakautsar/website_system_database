<?php
/**
 * Database Restructuring Migration
 * - Buat tabel emails untuk menyimpan daftar email PIC/GM
 * - Buat tabel file_uploads untuk menyimpan file SPPH
 * - Update tabel rab_forms dengan referensi ke emails
 * - Isi tabel emails dengan data yang sudah ada
 */

require_once __DIR__ . '/db.php';

try {
    echo "<h2>Database Restructuring Migration</h2>\n";
    
    // ===== 1. CREATE TABEL EMAILS =====
    echo "<h3>1. Creating 'emails' table...</h3>\n";
    $sql_emails = "
    CREATE TABLE IF NOT EXISTS emails (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        role ENUM('pic', 'gm') NOT NULL,
        name VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    if ($pdo->exec($sql_emails)) {
        echo "✅ Tabel 'emails' berhasil dibuat\n";
    } else {
        echo "⚠️ Tabel 'emails' sudah ada atau ada error\n";
    }
    
    // ===== 2. CREATE TABEL FILE_UPLOADS =====
    echo "<h3>2. Creating 'file_uploads' table...</h3>\n";
    $sql_files = "
    CREATE TABLE IF NOT EXISTS file_uploads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        form_id INT NOT NULL,
        file_type ENUM('spph', 'spk', 'other') NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        original_name VARCHAR(255),
        file_path VARCHAR(500),
        file_size INT,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (form_id) REFERENCES rab_forms(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    if ($pdo->exec($sql_files)) {
        echo "✅ Tabel 'file_uploads' berhasil dibuat\n";
    } else {
        echo "⚠️ Tabel 'file_uploads' sudah ada atau ada error\n";
    }
    
    // ===== 3. ADD COLUMNS TO rab_forms IF NOT EXISTS =====
    echo "<h3>3. Updating 'rab_forms' table structure...</h3>\n";
    
    // Check if pic_email_id column exists
    $check_pic_col = "SHOW COLUMNS FROM rab_forms LIKE 'pic_email_id'";
    $result = $pdo->query($check_pic_col);
    
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE rab_forms ADD COLUMN pic_email_id INT");
        $pdo->exec("ALTER TABLE rab_forms ADD FOREIGN KEY (pic_email_id) REFERENCES emails(id)");
        echo "✅ Kolom 'pic_email_id' ditambahkan\n";
    } else {
        echo "✓ Kolom 'pic_email_id' sudah ada\n";
    }
    
    $check_gm_col = "SHOW COLUMNS FROM rab_forms LIKE 'gm_email_id'";
    $result = $pdo->query($check_gm_col);
    
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE rab_forms ADD COLUMN gm_email_id INT");
        $pdo->exec("ALTER TABLE rab_forms ADD FOREIGN KEY (gm_email_id) REFERENCES emails(id)");
        echo "✅ Kolom 'gm_email_id' ditambahkan\n";
    } else {
        echo "✓ Kolom 'gm_email_id' sudah ada\n";
    }
    
    // ===== 4. POPULATE EMAILS TABLE =====
    echo "<h3>4. Populating 'emails' table with existing data...</h3>\n";
    
    $pic_emails = [
        ['email' => 'arifa@dyandraeventsolutions.com', 'name' => 'Arifa'],
        ['email' => 'irfant.giant@dyandraeventsolutions.com', 'name' => 'Irfant'],
        ['email' => 'dellaazkia@dyandraeventsolutions.com', 'name' => 'Della'],
        ['email' => 'admin@dyandraeventsolutions.com', 'name' => 'Admin'],
        ['email' => 'rajakautsar09@gmail.com', 'name' => 'Test PIC'],
    ];
    
    $gm_emails = [
        ['email' => 'andysoekasah@dyandraeventsolutions.com', 'name' => 'Andy Soekasah'],
        ['email' => 'tessya@dyandraeventsolutions.com', 'name' => 'Tessya'],
        ['email' => 'bahri@dyandraeventsolutions.com', 'name' => 'Bahri'],
        ['email' => 'admin@dyandraeventsolutions.com', 'name' => 'Admin'],
        ['email' => 'rajakautsar20@gmail.com', 'name' => 'Test GM'],
    ];
    
    $inserted_count = 0;
    $skipped_count = 0;
    
    // Insert PIC emails
    foreach ($pic_emails as $email_data) {
        try {
            $check = $pdo->prepare("SELECT id FROM emails WHERE email = ? AND role = 'pic'");
            $check->execute([$email_data['email']]);
            
            if ($check->rowCount() == 0) {
                $insert = $pdo->prepare("INSERT INTO emails (email, role, name) VALUES (?, 'pic', ?)");
                $insert->execute([$email_data['email'], $email_data['name']]);
                echo "✅ PIC: {$email_data['email']}\n";
                $inserted_count++;
            } else {
                echo "⊘ PIC: {$email_data['email']} (sudah ada)\n";
                $skipped_count++;
            }
        } catch (Exception $e) {
            echo "⚠️ PIC: {$email_data['email']} - {$e->getMessage()}\n";
        }
    }
    
    // Insert GM emails
    foreach ($gm_emails as $email_data) {
        try {
            $check = $pdo->prepare("SELECT id FROM emails WHERE email = ? AND role = 'gm'");
            $check->execute([$email_data['email']]);
            
            if ($check->rowCount() == 0) {
                $insert = $pdo->prepare("INSERT INTO emails (email, role, name) VALUES (?, 'gm', ?)");
                $insert->execute([$email_data['email'], $email_data['name']]);
                echo "✅ GM: {$email_data['email']}\n";
                $inserted_count++;
            } else {
                echo "⊘ GM: {$email_data['email']} (sudah ada)\n";
                $skipped_count++;
            }
        } catch (Exception $e) {
            echo "⚠️ GM: {$email_data['email']} - {$e->getMessage()}\n";
        }
    }
    
    echo "\n<strong>Summary:</strong>\n";
    echo "- Inserted: $inserted_count\n";
    echo "- Skipped: $skipped_count\n";
    
    echo "\n<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h3>✅ Migration Complete!</h3>";
    echo "<p>Selanjutnya:</p>";
    echo "<ul>";
    echo "<li>Tabel <strong>emails</strong> sudah siap. Anda bisa tambah/edit email di phpMyAdmin</li>";
    echo "<li>Update <strong>form_rab.php</strong> untuk menggunakan tabel emails sebagai dropdown</li>";
    echo "<li>Update <strong>submit_form.php</strong> untuk menyimpan email_id bukan email string</li>";
    echo "<li>Update halaman lain untuk membaca dari tabel emails</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h3>❌ Error: " . $e->getMessage() . "</h3>";
    echo "</div>";
}
?>
