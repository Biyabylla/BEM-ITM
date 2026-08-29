
<?php
/**
 * config.example.php
 * Salin file ini ke config.php, lalu isi credential database sesuai server Anda.
 * Website BEM Institut Teknologi Mojosari
 */

// ==== KONFIGURASI DATABASE ====
define('DB_HOST', 'localhost');
define('DB_USER', 'YOUR_DB_USER');       // username MySQL
define('DB_PASS', 'YOUR_DB_PASS');       // password MySQL
define('DB_NAME', 'bem_itm');

// ==== KONFIGURASI SITUS ====
define('SITE_NAME_DEFAULT', 'BEM Institut Teknologi Mojosari');
define('SITE_SHORT_DEFAULT', 'BEM ITM');
define('SITE_PERIODE_DEFAULT', '2025-2026');

// ==== KONFIGURASI ROLE ADMIN ====
define('ROLES', [
    'super_admin' => [
        'label' => 'Super Admin',
        'akses' => ['*','kelola-admin'],
        'deskripsi' => 'Akses penuh ke seluruh fitur admin, termasuk mengelola akun admin lain.',
    ],
    'admin_pengurus' => [
        'label' => 'Admin Pengurus',
        'akses' => ['index','pengurus','departemen','kabinet','profil','pendaftaran','aspirasi','profil-admin'],
        'deskripsi' => 'Mengelola data pengurus, departemen, kabinet, profil visi-misi, & pendaftaran rekrutmen.',
    ],
    'admin_publikasi' => [
        'label' => 'Admin Publikasi',
        'akses' => ['index','berita','program-kerja','sambutan','aspirasi','profil-admin'],
        'deskripsi' => 'Mengelola konten publikasi: berita, program kerja, & sambutan.',
    ],
]);

function cek_akses($halaman) {
    $role = $_SESSION['admin_role'] ?? '';
    if (!isset(ROLES[$role])) return false;
    $akses = ROLES[$role]['akses'];
    if (in_array('*', $akses)) return true;
    return in_array($halaman, $akses);
}

// ==== SISA FILE SAMA PERSIS DENGAN config.php ====
// Copy semua kode dari config.php mulai dari "// Koneksi database" ke bawah.
// Atau lebih praktis: salin config.php → config.example.php, lalu ganti 4 baris credential di atas.

$koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$koneksi) {
    die('
    <div style="font-family: sans-serif; max-width:600px; margin:60px auto; padding:24px 32px; border:1px solid #e2b8be; background:#fdf1f2; border-radius:12px; color:#5c1620;">
        <h2 style="margin-top:0;">Koneksi Database Gagal</h2>
        <p>Website tidak dapat terhubung ke database MySQL/MariaDB.</p>
        <p><b>Pesan error:</b> ' . htmlspecialchars(mysqli_connect_error()) . '</p>
        <p>Pastikan:</p>
        <ol>
            <li>Service MySQL/MariaDB sudah berjalan (misal via XAMPP/Laragon).</li>
            <li>Database <code>bem_itm</code> sudah dibuat dengan mengimpor file <code>database.sql</code>.</li>
            <li>Pengaturan <code>DB_HOST</code>, <code>DB_USER</code>, <code>DB_PASS</code> pada <code>config.php</code> sudah sesuai.</li>
        </ol>
    </div>');
}
mysqli_set_charset($koneksi, 'utf8mb4');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function esc($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function sanitize($string, $max_len = 255) {
    $string = trim($string ?? '');
    $string = stripslashes($string);
    $string = strip_tags($string);
    $string = mb_substr($string, 0, $max_len, 'UTF-8');
    return $string;
}

function valid_int($val) {
    return (int)$val;
}

function valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . esc(csrf_token()) . '">';
}
function csrf_verify($token = null) {
    if ($token === null) $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function tanggal_indo($tanggal, $format_lengkap = true) {
    if (empty($tanggal) || $tanggal == '0000-00-00' || $tanggal == '0000-00-00 00:00:00') return '-';
    $ts = strtotime($tanggal);
    if ($ts === false) return '-';
    $bulan = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
        7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
    ];
    $hari = date('d', $ts);
    $bln  = $bulan[(int)date('n', $ts)];
    $thn  = date('Y', $ts);
    return $format_lengkap ? "$hari $bln $thn" : "$bln $thn";
}

function buat_slug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    return trim($string, '-');
}

function foto_default($nama) {
    return 'https://ui-avatars.com/api/?name=' . urlencode($nama) . '&background=7A1F2B&color=fff&size=256&font-size=0.38&bold=true';
}

function username_ig($url) {
    $url = rtrim((string)$url, '/');
    $parts = array_values(array_filter(explode('/', $url), 'strlen'));
    $last = end($parts);
    if ($last === 'instagram.com' || $last === 'www.instagram.com' || !$last) return $url;
    return '@' . rtrim($last, '?.');
}

function img_url($path) {
    $path = (string)$path;
    if ($path === '') return $path;
    if (preg_match('~^(https?:)?//~i', $path) || strpos($path, 'data:') === 0 || $path[0] === '/') return $path;
    if (strpos($path, '../') === 0) return $path;
    $di_admin = isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false;
    return $di_admin ? '../' . ltrim($path, '/') : ltrim($path, '/');
}

function setting($kunci, $default = '') {
    global $koneksi;
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $r = mysqli_query($koneksi, "SELECT kunci, nilai FROM pengaturan");
        if ($r) while ($row = mysqli_fetch_assoc($r)) $cache[$row['kunci']] = $row['nilai'];
    }
    return $cache[$kunci] ?? $default;
}

function simpan_setting($kunci, $nilai) {
    global $koneksi;
    $kunci = trim($kunci);
    $nilai = trim($nilai);
    if ($kunci === '') return;
    $ada = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id FROM pengaturan WHERE kunci='" . mysqli_real_escape_string($koneksi, $kunci) . "'"));
    if ($ada) {
        $stmt = mysqli_prepare($koneksi, "UPDATE pengaturan SET nilai=? WHERE kunci=?");
        mysqli_stmt_bind_param($stmt, 'ss', $nilai, $kunci);
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO pengaturan (kunci, nilai) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'ss', $kunci, $nilai);
    }
    mysqli_stmt_execute($stmt);
}

define('SITE_NAME', setting('site_name', SITE_NAME_DEFAULT));
define('SITE_SHORT', setting('site_short', SITE_SHORT_DEFAULT));
define('SITE_PERIODE', setting('site_periode', SITE_PERIODE_DEFAULT));

function kabinet_aktif() {
    global $koneksi;
    static $cache = null;
    if ($cache !== null) return $cache;
    $r = mysqli_query($koneksi, "SELECT * FROM kabinet WHERE is_aktif=1 ORDER BY periode DESC LIMIT 1");
    $k = mysqli_fetch_assoc($r);
    if (!$k) $k = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kabinet ORDER BY periode DESC LIMIT 1"));
    $cache = $k ?: null;
    return $cache;
}

function daftar_kabinet() {
    global $koneksi;
    $out = [];
    $r = mysqli_query($koneksi, "SELECT * FROM kabinet ORDER BY periode DESC");
    if ($r) while ($row = mysqli_fetch_assoc($r)) $out[] = $row;
    return $out;
}

function pendaftaran_info($jenis) {
    $prefix = ($jenis === 'presma') ? 'pendaftaran_presma' : 'pendaftaran_pengurus';
    $status   = setting($prefix . '_status', 'tutup');
    $buka_tgl = setting($prefix . '_buka', '');
    $tutup_tgl = setting($prefix . '_tutup', '');
    $sekarang = date('Y-m-d');
    $dalam_jangka = true;
    if (!empty($buka_tgl) && $sekarang < $buka_tgl) $dalam_jangka = false;
    if (!empty($tutup_tgl) && $sekarang > $tutup_tgl) $dalam_jangka = false;
    $buka = ($status === 'buka' && $dalam_jangka);
    $pesan = '';
    if ($status !== 'buka') $pesan = 'Pendaftaran saat ini ditutup.';
    elseif (!empty($buka_tgl) && $sekarang < $buka_tgl) $pesan = 'Pendaftaran akan dibuka pada ' . tanggal_indo($buka_tgl) . '.';
    elseif (!empty($tutup_tgl) && $sekarang > $tutup_tgl) $pesan = 'Pendaftaran telah ditutup pada ' . tanggal_indo($tutup_tgl) . '.';
    return [
        'buka'     => $buka,
        'status'   => $status,
        'buka_tgl' => $buka_tgl,
        'tutup_tgl'=> $tutup_tgl,
        'pesan'    => $pesan,
    ];
}

define('MAX_IMG_UPLOAD', 1024 * 1024);
define('IMG_DIR', __DIR__ . '/assets/img');

function upload_img($field, $prefix, $ekstensi_izinkan = ['png','jpg','jpeg','gif','webp','svg','ico']) {
    if (empty($_FILES[$field]['name'])) {
        return ['ok' => false, 'error' => 'Tidak ada file yang dipilih.'];
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        if ($_FILES[$field]['error'] === UPLOAD_ERR_INI_SIZE || $_FILES[$field]['error'] === UPLOAD_ERR_FORM_SIZE) {
            return ['ok' => false, 'error' => 'File terlalu besar. Maksimal 1 MB.'];
        }
        return ['ok' => false, 'error' => 'Gagal mengunggah file.'];
    }
    if ($_FILES[$field]['size'] > MAX_IMG_UPLOAD) {
        return ['ok' => false, 'error' => 'Ukuran file melebihi batas maksimal 1 MB.'];
    }
    $nama_asli = $_FILES[$field]['name'];
    $ekstensi = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
    if (!in_array($ekstensi, $ekstensi_izinkan)) {
        return ['ok' => false, 'error' => 'Format file tidak didukung (' . strtoupper(implode(' / ', $ekstensi_izinkan)) . ').'];
    }

    $tmp = $_FILES[$field]['tmp_name'];
    $mime_izinkan = [
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'ico'  => ['image/x-icon', 'image/vnd.microsoft.icon'],
        'svg'  => 'image/svg+xml',
    ];
    if ($ekstensi === 'svg') {
        $konten = (string)file_get_contents($tmp, false, null, 0, 1_000_000);
        $head = strtolower(substr($konten, 0, 512));
        if (strpos($head, '<svg') === false && strpos($head, '<?xml') === false) {
            return ['ok' => false, 'error' => 'File SVG tidak valid.'];
        }
        if (preg_match('/<\s*(script|foreignobject)\b|on\w+\s*=/i', $konten)) {
            return ['ok' => false, 'error' => 'File SVG tidak boleh mengandung skrip.'];
        }
    } else {
        if (function_exists('finfo_open')) {
            $ft = finfo_open(FILEINFO_MIME_TYPE);
            $deteksi = finfo_file($ft, $tmp);
            finfo_close($ft);
            $harapan = $mime_izinkan[$ekstensi] ?? null;
            $cocok = false;
            if (is_array($harapan)) { $cocok = in_array($deteksi, $harapan, true); }
            else { $cocok = ($deteksi === $harapan); }
            if (!$cocok) {
                return ['ok' => false, 'error' => 'Isi file tidak sesuai format gambar yang dipilih.'];
            }
        } elseif (function_exists('getimagesize')) {
            $info = @getimagesize($tmp);
            if ($info === false) {
                return ['ok' => false, 'error' => 'File bukan gambar yang valid.'];
            }
        }
    }

    if (!is_dir(IMG_DIR)) @mkdir(IMG_DIR, 0775, true);
    $nama_file = $prefix . '_' . time() . '_' . rand(100, 999) . '.' . $ekstensi;
    if (move_uploaded_file($tmp, IMG_DIR . '/' . $nama_file)) {
        return ['ok' => true, 'path' => 'assets/img/' . $nama_file];
    }
    return ['ok' => false, 'error' => 'Gagal menyimpan file. Periksa izin folder assets/img.'];
}
