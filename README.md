# Website BEM Institut Teknologi Mojosari

Website resmi Badan Eksekutif Mahasiswa (BEM) Institut Teknologi Mojosari, dibangun dengan **PHP native** + **MySQL/MariaDB**. Tema warna utama **Maroon** dengan warna sekunder **Gold/Emas**.

## 🚀 Cara Instalasi (XAMPP / Laragon)

1. Salin folder `bem-itm` ke direktori server lokal Anda:
   - XAMPP: `C:/xampp/htdocs/bem-itm`
   - Laragon: `C:/laragon/www/bem-itm`
2. Jalankan Apache & MySQL melalui XAMPP/Laragon Control Panel.
3. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`), buat database baru atau langsung import:
   - Klik menu **Import** → pilih file `database.sql` → klik **Go**.
   - File ini akan otomatis membuat database `bem_itm` beserta seluruh tabel dan data contoh.
4. Buka `config.php`, sesuaikan jika perlu:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'bem_itm');
   ```
5. Akses website di browser: `http://localhost/bem-itm/index.php`

## 🔑 Login Admin

- URL: `http://localhost/bem-itm/admin/login.php`
- Username: `u`
- Password: ``

**⚠️ Segera ganti password default setelah login pertama kali** (update langsung di tabel `admin_users`, kolom `password`, gunakan `password_hash()` PHP untuk hash baru).

## 📁 Struktur Folder

```
bem-itm/
├── config.php              # Koneksi database & helper function
├── header.php               # Navbar + seluruh CSS (tema maroon terpadu)
├── footer.php                # Footer + seluruh JavaScript interaktif
├── index.php                 # 1. Home
├── tentang.php                # 2. Tentang Kami (Visi, Misi, Tujuan)
├── struktur.php                # 3. Struktur Organisasi (dengan filter departemen)
├── program-kerja.php            # 4. Program Kerja (dengan filter departemen)
├── berita.php + berita-detail.php # 5. Berita
├── aspirasi.php                   # 6. Aspirasi (Kritik & Saran)
├── database.sql                    # Struktur + data awal database
└── admin/                           # Panel admin (CRUD seluruh konten)
    ├── login.php / logout.php
    ├── index.php (dashboard)
    ├── profil.php      (edit deskripsi, visi, misi, tujuan)
    ├── sambutan.php    (edit sambutan Wakil Rektor III / Pembina / Presma)
    ├── departemen.php  (kelola departemen)
    ├── pengurus.php    (kelola struktur organisasi)
    ├── program-kerja.php
    ├── berita.php
    └── aspirasi.php    (lihat & kelola aspirasi masuk)
```

## 🎨 Catatan Desain

- Seluruh CSS & JavaScript digabung langsung dalam file PHP (`header.php` untuk CSS, `footer.php` untuk JS) — tidak ada file `.css`/`.js` terpisah.
- Desain **responsif & interaktif**: menu mobile slide-in, filter tab tanpa reload (struktur & program kerja), efek scroll reveal, navbar dinamis saat discroll.
- Warna utama **Maroon** (`#7a1f2b`) dipadukan dengan warna sekunder **Gold** (`#c89b3c`) sebagai aksen — kombinasi klasik yang elegan dan kontras baik di layar mobile maupun desktop.

## 📝 Catatan Data

Data kepengurusan (nama & jabatan) diambil dari SK No. 01.003/ITM/II/2026. Kolom **foto**, **program studi**, dan **instagram** pada data contoh adalah **placeholder** karena tidak tercantum pada dokumen SK — silakan perbarui melalui **Panel Admin → Struktur/Pengurus** agar sesuai data asli.
