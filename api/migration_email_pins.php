<?php
/**
 * Migration: Create email_pins table
 * Run this script once to create the table
 */
session_start();
$pdo = require_once __DIR__ . '/db.php';

try {
    // Drop existing table jika ada (untuk reset)
    $pdo->exec("DROP TABLE IF EXISTS email_pins");
    
    // Create table with UNIQUE constraint on email+role combination
    $sql = "
    CREATE TABLE email_pins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        pin_hash VARCHAR(255) NOT NULL,
        role ENUM('pic', 'gm', 'admin') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_email_role (email, role)
    )
    ";
    $pdo->exec($sql);
    echo "✓ Table email_pins created successfully<br>";

    // Seed data with PINs
    $pins = [
        // Master PIN (works for all roles)
        ['email' => 'master', 'pin' => '0000', 'role' => 'pic'],
        ['email' => 'master', 'pin' => '0000', 'role' => 'gm'],
        ['email' => 'master', 'pin' => '0000', 'role' => 'admin'],
        
        // PIC Sales
        ['email' => 'arifa@dyandraeventsolutions.com', 'pin' => '1234', 'role' => 'pic'],
        ['email' => 'irfant.giant@dyandraeventsolutions.com', 'pin' => '5678', 'role' => 'pic'],
        ['email' => 'dellaazkia@dyandraeventsolutions.com', 'pin' => '9012', 'role' => 'pic'],
        
        // GM Sales
        ['email' => 'andysoekasah@dyandraeventsolutions.com', 'pin' => '3456', 'role' => 'gm'],
        ['email' => 'tessya@dyandraeventsolutions.com', 'pin' => '7890', 'role' => 'gm'],
        ['email' => 'bahri@dyandraeventsolutions.com', 'pin' => '2345', 'role' => 'gm'],
        ['email' => 'admin@dyandraeventsolutions.com', 'pin' => '6789', 'role' => 'gm'],
        
        // Admin Project
        ['email' => 'admin@dyandraeventsolutions.com', 'pin' => '9999', 'role' => 'admin'],
    ];

    $stmt = $pdo->prepare("
        INSERT INTO email_pins (email, pin_hash, role)
        VALUES (?, ?, ?)
    ");

    foreach ($pins as $p) {
        $pinHash = password_hash($p['pin'], PASSWORD_DEFAULT);
        $stmt->execute([$p['email'], $pinHash, $p['role']]);
        echo "✓ Inserted PIN for {$p['email']} ({$p['role']})<br>";
    }

    echo "<br><strong>Migration completed successfully!</strong>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

