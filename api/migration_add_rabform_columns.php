<?php
/**
 * Migration: Add pic_email and gm_email to rab_forms
 * Run this script once (via browser or CLI) after backing up your DB
 */
session_start();
$pdo = require_once __DIR__ . '/db.php';
try {
    // Check and add pic_email if missing
    $hasPic = $pdo->query("SHOW COLUMNS FROM rab_forms LIKE 'pic_email'")->fetch();
    if (!$hasPic) {
        $pdo->exec("ALTER TABLE rab_forms ADD COLUMN pic_email VARCHAR(255) DEFAULT NULL AFTER spk_link");
        echo "✓ Added column pic_email<br>";
    } else {
        echo "- Column pic_email already exists<br>";
    }

    // Check and add gm_email if missing
    $hasGm = $pdo->query("SHOW COLUMNS FROM rab_forms LIKE 'gm_email'")->fetch();
    if (!$hasGm) {
        $pdo->exec("ALTER TABLE rab_forms ADD COLUMN gm_email VARCHAR(255) DEFAULT NULL AFTER pic_email");
        echo "✓ Added column gm_email<br>";
    } else {
        echo "- Column gm_email already exists<br>";
    }

    echo "<br><strong>Migration completed.</strong>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
