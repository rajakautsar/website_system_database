# Panduan Lengkap Database Restructuring

## 📋 Ringkasan
Anda ingin membuat sistem email yang lebih fleksibel:
- ✅ Tabel `emails` untuk menyimpan daftar email PIC/GM
- ✅ Mudah tambah/edit email tanpa edit kode (langsung di phpMyAdmin)
- ✅ Tabel `file_uploads` untuk tracking file SPPH

---

## 🚀 Langkah-Langkah

### Step 1: Jalankan Migration Script
1. Buka: **http://localhost/website_system_database/api/migration_restructure_database.php**
2. Tunggu sampai muncul "✅ Migration Complete!"
3. Cek di phpMyAdmin apakah tabel `emails` dan `file_uploads` sudah ada

**Screenshot setelah success:**
```
✅ Tabel 'emails' berhasil dibuat
✅ Tabel 'file_uploads' berhasil dibuat
✅ Kolom 'pic_email_id' ditambahkan
✅ Kolom 'gm_email_id' ditambahkan

✅ PIC: arifa@dyandraeventsolutions.com
✅ PIC: irfant.giant@dyandraeventsolutions.com
... (dan seterusnya)

✅ Migration Complete!
```

---

### Step 2: Verifikasi di phpMyAdmin

1. Login phpMyAdmin
2. Pilih database `rab_system`
3. Lihat tabel `emails`:
   - Berisi semua email PIC dan GM
   - Setiap email punya ID unik
   - Ada kolom: email, role, name

4. Lihat tabel `file_uploads`:
   - Kosong dulu (akan terisi saat upload file baru)
   - Ada kolom: form_id, file_type, file_name, file_path, dll

---

### Step 3: Edit Email (Langkah Paling Penting!)

**Untuk TAMBAH Email Baru:**
1. phpMyAdmin → tabel `emails` → Insert
2. Isi:
   - **email**: alamat@gmail.com
   - **role**: pic (atau gm)
   - **name**: Nama Lengkap
3. Klik Save
4. Email otomatis muncul di dropdown form_rab.php

**Untuk EDIT Email Existing:**
1. phpMyAdmin → tabel `emails` 
2. Klik pencil (edit)
3. Ubah email atau name
4. Klik Save

**Untuk HAPUS Email:**
1. phpMyAdmin → tabel `emails`
2. Klik X (delete)
3. Email hilang dari dropdown

---

### Step 4: Update Kode (OPSIONAL - untuk fitur lengkap)

Jika ingin update aplikasi agar fully menggunakan tabel emails:

**Update `form_rab.php`:**
- Ganti hardcoded dropdown dengan query dari tabel `emails`
- File contoh: `EXAMPLE_FORM_UPDATE.php`

**Update `api/submit_form.php`:**
- Simpan `pic_email_id` dan `gm_email_id` (ID bukan email string)
- Jika perlu email string, ambil dari tabel saat kirim email

**Update halaman tampilan:**
- `approved_gm_forms.php`
- `view_form.php`
- Join dengan tabel `emails` untuk ambil email dari ID

---

## 📊 Struktur Database Baru

```
rab_forms
├── id
├── nama_project
├── client
├── pic_email_id → FK ke emails.id ✨ NEW
├── gm_email_id → FK ke emails.id ✨ NEW
├── ... (kolom lain)
└── created_at

emails ✨ NEW
├── id
├── email (unique)
├── role (pic / gm)
├── name
├── created_at
└── updated_at

file_uploads ✨ NEW
├── id
├── form_id → FK ke rab_forms.id
├── file_type (spph / spk / other)
├── file_name
├── file_path
├── file_size
└── uploaded_at
```

---

## ⚠️ Catatan Penting

1. **Email yang sudah disimpan di tabel lama** (kolom `pic_email`, `gm_email`)
   - Akan tetap ada, tidak dihapus
   - Bisa digunakan untuk historical data

2. **Data migrasi otomatis**
   - Email dari `email_config.php` sudah dimasukkan ke tabel `emails`
   - Tidak perlu input manual

3. **Backup database** sebelum jalankan migration (jaga-jaga)
   - phpMyAdmin → Export database

---

## ✅ Checklist Setelah Migration

- [ ] Jalankan `migration_restructure_database.php`
- [ ] Lihat "✅ Migration Complete!" di browser
- [ ] Verifikasi tabel `emails` di phpMyAdmin
- [ ] Coba tambah 1 email baru via phpMyAdmin
- [ ] Cek apakah email baru muncul di form (jika sudah update form_rab.php)
- [ ] Backup database

---

## 🆘 Troubleshooting

**Q: Tabel sudah ada, migration error?**
A: Itu OK, script akan skip tabel yang sudah ada. Lanjut ke Step 2.

**Q: Email belum muncul di form setelah tambah?**
A: Kemungkinan form_rab.php belum di-update. Sementara, refresh browser dan cek email_config.php masih berfungsi.

**Q: Mau rollback?**
A: Tabel lama `pic_email`, `gm_email` masih ada di rab_forms. Bisa pakai yang lama sambil develop yang baru.

**Q: Data hilang?**
A: Tidak! Data original tetap ada. Tabel baru hanya tambahan untuk struktur yang lebih baik.

