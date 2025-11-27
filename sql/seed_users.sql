-- sql/seed_users.sql
-- Skrip ini membuat database (jika belum ada), tabel user dasar, dan mengisi contoh akun
-- Perhatian: password disimpan dalam plain text sesuai permintaan. Ini TIDAK aman untuk production.

CREATE DATABASE IF NOT EXISTS `rab_system` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `rab_system`;

-- Struktur tabel user (sama untuk semua role)
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pic_users` LIKE `admin_users`;
CREATE TABLE IF NOT EXISTS `gm_users` LIKE `admin_users`;
CREATE TABLE IF NOT EXISTS `general_users` LIKE `admin_users`;

-- Contoh akun (password plaintext)
-- Admin
INSERT INTO `admin_users` (`username`, `password`, `full_name`, `email`) VALUES
('admin', 'admin123', 'Administrator', 'admin@example.com')
ON DUPLICATE KEY UPDATE password=VALUES(password), full_name=VALUES(full_name), email=VALUES(email);

-- PIC Sales
INSERT INTO `pic_users` (`username`, `password`, `full_name`, `email`) VALUES
('pic', 'pic123', 'PIC Sales', 'pic@example.com')
ON DUPLICATE KEY UPDATE password=VALUES(password), full_name=VALUES(full_name), email=VALUES(email);

-- General Manager
INSERT INTO `gm_users` (`username`, `password`, `full_name`, `email`) VALUES
('gm', 'gm123', 'General Manager', 'gm@example.com')
ON DUPLICATE KEY UPDATE password=VALUES(password), full_name=VALUES(full_name), email=VALUES(email);

-- General user
INSERT INTO `general_users` (`username`, `password`, `full_name`, `email`) VALUES
('user', 'user123', 'General User', 'user@example.com')
ON DUPLICATE KEY UPDATE password=VALUES(password), full_name=VALUES(full_name), email=VALUES(email);

-- Optional: sample data untuk rab_forms (sederhana)
CREATE TABLE IF NOT EXISTS `rab_forms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category` VARCHAR(50),
  `no_project` VARCHAR(100),
  `nama_project` VARCHAR(255),
  `client` VARCHAR(255),
  `venue` VARCHAR(255),
  `event_date_start` DATE,
  `event_date_end` DATE,
  `contract_type` VARCHAR(100),
  `remarks` TEXT,
  `rab_submit` TINYINT DEFAULT 0,
  `rab_internal` TINYINT DEFAULT 0,
  `profit_margin` DECIMAL(12,2) DEFAULT 0,
  `profit_percentage` DECIMAL(5,2) DEFAULT 0,
  `file_spph` VARCHAR(255),
  `file_spk` VARCHAR(255),
  `spk_link` VARCHAR(1024),
  `status` VARCHAR(50) DEFAULT 'pending_pic',
  `created_by` INT,
  `created_at` DATETIME,
  `updated_by` INT,
  `updated_at` DATETIME,
  `pic_comment` TEXT,
  `pic_approved_by` INT,
  `pic_approved_name` VARCHAR(255),
  `pic_approved_email` VARCHAR(255),
  `pic_decision_at` DATETIME,
  `gm_comment` TEXT,
  `gm_approved_by` INT,
  `gm_approved_name` VARCHAR(255),
  `gm_approved_email` VARCHAR(255),
  `gm_decision_at` DATETIME,
  `reject_reason` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- contoh satu form
INSERT INTO `rab_forms` (`category`,`no_project`,`nama_project`,`client`,`venue`,`event_date_start`,`event_date_end`,`contract_type`,`remarks`,`status`,`created_by`,`created_at`)
VALUES ('kp','PRJ-001','Contoh Project','Contoh Client','Contoh Venue','2025-12-01','2025-12-02','Kontrak A','Catatan contoh','pending_pic',1,NOW())
ON DUPLICATE KEY UPDATE nama_project=VALUES(nama_project);
