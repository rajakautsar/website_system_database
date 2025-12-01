# Database Restructuring Guide

## Tujuan
Membuat struktur database yang lebih terstruktur dengan:
1. **Tabel `emails`** - Menyimpan daftar email PIC/GM (mudah di-edit di phpMyAdmin)
2. **Tabel `file_uploads`** - Menyimpan informasi file SPPH
3. **Update `rab_forms`** - Gunakan foreign key ke tabel emails

---

## Langkah 1: Jalankan Migration Script

1. Buka browser: **http://localhost/website_system_database/api/migration_restructure_database.php**
2. Tunggu hingga selesai
3. Lihat hasil: tabel `emails` dan `file_uploads` akan dibuat
4. Email existing akan diisi otomatis ke tabel `emails`

---

## Langkah 2: Struktur Tabel Baru

### Tabel `emails`
```sql
CREATE TABLE emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    role ENUM('pic', 'gm') NOT NULL,
    name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

**Cara menambah email baru:**
- Login phpMyAdmin
- Buka tabel `emails`
- Klik "Insert" atau "+" 
- Isi: email, role (pic/gm), name
- Klik Save

### Tabel `file_uploads`
```sql
CREATE TABLE file_uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    file_type ENUM('spph', 'spk', 'other'),
    file_name VARCHAR(255),
    original_name VARCHAR(255),
    file_path VARCHAR(500),
    file_size INT,
    uploaded_at TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES rab_forms(id)
)
```

Menyimpan semua file yang di-upload (SPPH, SPK, dll)

---

## Langkah 3: Update Kode (TODO)

Setelah migration, perlu update file berikut:

### `form_rab.php`
- Ubah dropdown PIC/GM untuk membaca dari tabel `emails` bukan hardcoded
- Query: `SELECT id, email, name FROM emails WHERE role = 'pic'`

### `api/submit_form.php`
- Ubah untuk menyimpan `pic_email_id` dan `gm_email_id` (FK) bukan string email
- Insert file info ke tabel `file_uploads`

### Halaman tampilan (approved_gm_forms.php, view_form.php, dll)
- Join dengan tabel `emails` untuk menampilkan email dari ID

---

## Email Awal di Tabel

### PIC Emails:
- arifa@dyandraeventsolutions.com (Arifa)
- irfant.giant@dyandraeventsolutions.com (Irfant)
- dellaazkia@dyandraeventsolutions.com (Della)
- admin@dyandraeventsolutions.com (Admin)
- rajakautsar09@gmail.com (Test PIC)

### GM Emails:
- andysoekasah@dyandraeventsolutions.com (Andy Soekasah)
- tessya@dyandraeventsolutions.com (Tessya)
- bahri@dyandraeventsolutions.com (Bahri)
- admin@dyandraeventsolutions.com (Admin)
- rajakautsar20@gmail.com (Test GM)

---

## Keuntungan Struktur Baru

✅ **Mudah mengelola email** - Edit langsung di phpMyAdmin tanpa coding
✅ **Tidak ada duplikasi** - Email disimpan sekali di tabel `emails`
✅ **Tracking file** - Semua file terekam di tabel `file_uploads`
✅ **Scalable** - Mudah dikembangkan ke fitur baru

---

## Jika Ada Masalah

Jika tabel sudah ada atau ada error:
1. Buka phpMyAdmin
2. Lihat apakah tabel `emails` dan `file_uploads` sudah ada
3. Jika belum ada, buat manual dengan SQL di atas
4. Jika sudah ada, lanjut ke Langkah 2 dan 3

