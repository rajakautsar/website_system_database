<?php
/**
 * Clean Database Script - Hapus semua data form RAB
 * Gunakan untuk membersihkan data dummy
 */

require_once __DIR__ . '/db.php';

try {
    echo "<h2>🗑️ Database Cleanup</h2>\n";
    
    // Count existing records sebelum delete
    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM rab_forms");
    $count = $countStmt->fetch()['total'];
    
    echo "<p>Total form yang akan dihapus: <strong>$count</strong></p>\n";
    
    if ($count == 0) {
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px;'>";
        echo "<h3>✅ Database sudah bersih!</h3>";
        echo "<p>Tidak ada form yang perlu dihapus.</p>";
        echo "</div>";
        exit;
    }
    
    // Confirm sebelum delete (safety check)
    if (!isset($_POST['confirm']) || $_POST['confirm'] !== 'yes') {
        echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>⚠️ PERINGATAN!</h3>";
        echo "<p>Anda akan menghapus <strong>$count</strong> form dari database.</p>";
        echo "<p><strong>Tindakan ini TIDAK BISA DIBATALKAN!</strong></p>";
        echo "<p>Pastikan Anda sudah backup database terlebih dahulu.</p>";
        
        echo "<form method='POST' style='margin-top: 20px;'>";
        echo "<button type='submit' name='confirm' value='yes' class='btn btn-danger' style='padding: 10px 20px; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;'>";
        echo "Ya, Hapus Semua Data";
        echo "</button>";
        echo "</form>";
        echo "</div>";
        exit;
    }
    
    // Delete all records
    echo "<p>Menghapus data...</p>\n";
    
    $deleteStmt = $pdo->query("DELETE FROM rab_forms");
    
    // Reset auto increment
    $pdo->query("ALTER TABLE rab_forms AUTO_INCREMENT = 1");
    
    // Verify delete
    $verifyStmt = $pdo->query("SELECT COUNT(*) as total FROM rab_forms");
    $newCount = $verifyStmt->fetch()['total'];
    
    echo "\n<div style='background-color: #d4edda; padding: 15px; border-radius: 5px;'>";
    echo "<h3>✅ Cleanup Complete!</h3>";
    echo "<p>Total form yang dihapus: <strong>$count</strong></p>";
    echo "<p>Sisa form di database: <strong>$newCount</strong></p>";
    echo "<p>Database siap digunakan dengan data bersih. 🎉</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h3>❌ Error: " . $e->getMessage() . "</h3>";
    echo "</div>";
}
?>
