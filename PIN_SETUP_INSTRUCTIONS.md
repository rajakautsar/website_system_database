# Sistem PIN untuk Approval/Reject

## Langkah Setup

### 1. Jalankan Migration
Buka browser dan akses:
```
http://localhost/website_system_database/api/migration_email_pins.php
```

Tunggu sampai muncul pesan "✓ Migration completed successfully!"

Ini akan:
- Membuat tabel `email_pins`
- Mengisi data PIN untuk semua email PIC, GM, dan Admin

### 2. Daftar PIN yang Sudah Disimpan

**PIC Sales:**
- arifa@dyandraeventsolutions.com: **1234**
- irfant.giant@dyandraeventsolutions.com: **5678**
- dellaazkia@dyandraeventsolutions.com: **9012**

**GM Sales:**
- andysoekasah@dyandraeventsolutions.com: **3456**
- tessya@dyandraeventsolutions.com: **7890**
- bahri@dyandraeventsolutions.com: **2345**
- admin@dyandraeventsolutions.com: **6789**

**Admin Project:**
- admin@dyandraeventsolutions.com: **9999**

### 3. Testing

**Test Dashboard PIC:**
1. Login dengan akun PIC (email = arifa@dyandraeventsolutions.com)
2. Klik tombol "Approve" atau "Reject" pada form
3. Masukkan PIN **1234** pada modal yang muncul
4. Verifikasi bahwa form berhasil di-approve/reject

**Test Dashboard GM:**
1. Login dengan akun GM (email = andysoekasah@dyandraeventsolutions.com)
2. Klik tombol "Approve" atau "Reject" pada form
3. Masukkan PIN **3456** pada modal yang muncul
4. Verifikasi bahwa form berhasil di-approve/reject

### 4. Jika Ingin Mengubah PIN

Edit file `api/migration_email_pins.php` di bagian array `$pins`:

```php
$pins = [
    ['email' => 'arifa@dyandraeventsolutions.com', 'pin' => '1234', 'role' => 'pic'],
    // ... ubah nilai 'pin' sesuai kebutuhan
];
```

Kemudian jalankan ulang halaman migration.

---

## Ringkasan Perubahan File

### Files yang Ditambah:
- `api/migration_email_pins.php` — script untuk create table dan seed data
- `PIN_SETUP_INSTRUCTIONS.md` — dokumentasi ini

### Files yang Diubah:
- `dashboard/dashboard_pic.php` — tambah modal PIN dan validasi sebelum approve/reject
- `dashboard/dashboard_gm.php` — tambah modal PIN dan validasi sebelum approve/reject
- `api/approve_pic.php` — tambah validasi PIN dari email_pins table
- `api/approve_gm.php` — tambah validasi PIN dari email_pins table

### Database Schema:
```sql
CREATE TABLE email_pins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    pin_hash VARCHAR(255) NOT NULL,
    role ENUM('pic', 'gm', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## Catatan Teknis

- PIN di-hash menggunakan `password_hash()` dengan algoritma default (bcrypt)
- Validasi PIN dilakukan dengan `password_verify()`
- Setiap email hanya bisa punya 1 PIN per role (constraint UNIQUE pada email)
- Jika PIN salah, akan menampilkan pesan "PIN salah" dan tidak ada yang diubah di database
