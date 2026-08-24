<?php
require_once __DIR__ . '/includes/auth.php';
$admin_title = 'Dashboard';
$role = $_SESSION['admin_role'] ?? '';

function hitung_satu($sql) {
    global $koneksi;
    $r = mysqli_fetch_row(mysqli_query($koneksi, $sql));
    return (int)($r[0] ?? 0);
}

// Statistik berdasarkan role
$jml_aspirasi = hitung_satu("SELECT COUNT(*) FROM aspirasi");
$jml_aspirasi_baru = hitung_satu("SELECT COUNT(*) FROM aspirasi WHERE status='baru'");

if (cek_akses('pengurus')) {
    $jml_pengurus = hitung_satu("SELECT COUNT(*) FROM pengurus");
    $jml_departemen = hitung_satu("SELECT COUNT(*) FROM departemen");
    $jml_pendaftar = hitung_satu("SELECT (SELECT COUNT(*) FROM pendaftar_presma) + (SELECT COUNT(*) FROM pendaftar_pengurus) AS t");
}
if (cek_akses('program-kerja')) {
    $jml_proker = hitung_satu("SELECT COUNT(*) FROM program_kerja");
}
if (cek_akses('berita')) {
    $jml_berita = hitung_satu("SELECT COUNT(*) FROM berita");
}

$aspirasi_terbaru = [];
$q = mysqli_query($koneksi, "SELECT * FROM aspirasi ORDER BY created_at DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($q)) $aspirasi_terbaru[] = $row;

include 'includes/admin-header.php';
?>

<div class="page-head">
    <div>
        <h3>Selamat datang, <?php echo esc($_SESSION['admin_nama']); ?> 👋</h3>
        <p>Ringkasan aktivitas website BEM ITM.</p>
    </div>
    <?php $kab = kabinet_aktif(); if ($kab): ?>
    <div style="display:flex;align-items:center;gap:12px;background:var(--cream-soft);border:1px solid var(--line);padding:10px 18px;border-radius:12px;">
        <?php if (!empty($kab['logo'])): ?><img src="<?php echo esc(img_url($kab['logo'])); ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover;"><?php endif; ?>
        <div>
            <div style="font-weight:700;color:var(--maroon-900);font-size:.9rem;"><?php echo esc($kab['nama_kabinet']); ?></div>
            <div style="font-size:.74rem;color:var(--ink-soft);">Kabinet aktif periode <?php echo esc($kab['periode']); ?></div>
        </div>
        <a href="kabinet.php" class="btn btn-outline btn-sm" style="margin-left:6px;"><i class="bi bi-gear"></i></a>
    </div>
    <?php endif; ?>
</div>

<div class="stat-grid">
    <?php if (cek_akses('pengurus')): ?>
    <div class="stat-card" style="background:linear-gradient(135deg,#44121b,#6d2130);">
        <i class="bi bi-people fs-4"></i>
        <h2 style="color:#fff;margin:10px 0 2px;font-size:1.7rem;"><?php echo $jml_pengurus; ?></h2>
        <span style="font-size:.78rem;opacity:.9;">Pengurus</span>
    </div>
    <div class="stat-card" style="background:linear-gradient(135deg,#7b2c3b,#8d3a49);">
        <i class="bi bi-diagram-2 fs-4"></i>
        <h2 style="color:#fff;margin:10px 0 2px;font-size:1.7rem;"><?php echo $jml_departemen; ?></h2>
        <span style="font-size:.78rem;opacity:.9;">Departemen</span>
    </div>
    <?php endif; ?>
    <?php if (cek_akses('program-kerja')): ?>
    <div class="stat-card" style="background:linear-gradient(135deg,#8b682e,#a97f3d);">
        <i class="bi bi-clipboard2-check fs-4"></i>
        <h2 style="color:#fff;margin:10px 0 2px;font-size:1.7rem;"><?php echo $jml_proker; ?></h2>
        <span style="font-size:.78rem;opacity:.9;">Program Kerja</span>
    </div>
    <?php endif; ?>
    <?php if (cek_akses('berita')): ?>
    <div class="stat-card" style="background:linear-gradient(135deg,#501824,#6d2130);">
        <i class="bi bi-newspaper fs-4"></i>
        <h2 style="color:#fff;margin:10px 0 2px;font-size:1.7rem;"><?php echo $jml_berita; ?></h2>
        <span style="font-size:.78rem;opacity:.9;">Berita</span>
    </div>
    <?php endif; ?>
    <div class="stat-card" style="background:linear-gradient(135deg,#2f0a12,#44121b);">
        <i class="bi bi-chat-dots fs-4"></i>
        <h2 style="color:#fff;margin:10px 0 2px;font-size:1.7rem;"><?php echo $jml_aspirasi; ?></h2>
        <span style="font-size:.78rem;opacity:.9;"><?php echo $jml_aspirasi_baru; ?> aspirasi baru</span>
    </div>
    <?php if (cek_akses('pendaftaran')): ?>
    <div class="stat-card" style="background:linear-gradient(135deg,#501824,#7b2c3b);">
        <i class="bi bi-person-check fs-4"></i>
        <h2 style="color:#fff;margin:10px 0 2px;font-size:1.7rem;"><?php echo $jml_pendaftar; ?></h2>
        <span style="font-size:.78rem;opacity:.9;">Pendaftar rekrutmen</span>
    </div>
    <?php endif; ?>
</div>

<div class="card" style="padding:24px;">
    <div class="page-head" style="margin-bottom:16px;">
        <h4 style="margin:0;color:var(--maroon-900);font-size:1rem;">Aspirasi Terbaru</h4>
        <a href="aspirasi.php" class="btn btn-outline btn-sm">Lihat Semua</a>
    </div>
    <table>
        <thead><tr><th>Nama</th><th>Jenis</th><th>Pesan</th><th>Tanggal</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($aspirasi_terbaru as $a): ?>
            <tr>
                <td><?php echo esc($a['nama']); ?></td>
                <td><span class="badge badge-maroon"><?php echo esc(ucfirst($a['jenis'])); ?></span></td>
                <td><?php echo esc(mb_strimwidth($a['pesan'],0,50,'...')); ?></td>
                <td><?php echo date('d/m/Y', strtotime($a['created_at'])); ?></td>
                <td><span class="badge badge-gold"><?php echo esc(ucfirst($a['status'])); ?></span></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($aspirasi_terbaru)): ?>
            <tr><td colspan="5" style="text-align:center;color:var(--ink-soft);">Belum ada aspirasi masuk.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/admin-footer.php'; ?>
