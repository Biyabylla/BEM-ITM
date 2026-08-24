<?php
require_once __DIR__ . '/../../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . (strpos($_SERVER['PHP_SELF'], '/admin/includes/') !== false ? '../login.php' : 'login.php'));
    exit;
}

// Ambil role dari session, jika belum ada ambil dari database
if (!isset($_SESSION['admin_role'])) {
    $adm_id = (int)$_SESSION['admin_id'];
    $adm_q = mysqli_query($koneksi, "SELECT role FROM admin_users WHERE id=$adm_id");
    $adm_row = mysqli_fetch_assoc($adm_q);
    $_SESSION['admin_role'] = $adm_row['role'] ?? 'admin_publikasi';
}

// Tentukan halaman aktif dari nama file
$halaman_aktif = basename($_SERVER['PHP_SELF'], '.php');

// Cek akses berdasarkan role (kecuali profil-admin selalu bisa diakses)
if ($halaman_aktif !== 'profil-admin' && !cek_akses($halaman_aktif)) {
    http_response_code(403);
    die('<div style="font-family:sans-serif;max-width:500px;margin:80px auto;padding:30px;border:1px solid #e2b8be;background:#fdf1f2;border-radius:12px;text-align:center;">
        <h2 style="color:#a3272a;margin-top:0;">Akses Ditolak</h2>
        <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="index.php" style="display:inline-block;margin-top:16px;padding:10px 24px;background:#6d2130;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">Kembali ke Dashboard</a>
    </div>');
}

// Proteksi CSRF: semua request POST di area admin wajib membawa token valid
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && !csrf_verify($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    die('Permintaan ditolak: token keamanan (CSRF) tidak valid. Silakan muat ulang halaman lalu coba lagi.');
}
