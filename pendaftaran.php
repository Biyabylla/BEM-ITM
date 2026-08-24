<?php
require_once __DIR__ . '/config.php';
$page_title = 'Pendaftaran';

// Proteksi CSRF untuk form pendaftaran
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && !csrf_verify()) {
    die('<p style="font-family:sans-serif;text-align:center;margin:60px 20px;color:#a3272a;font-size:.95rem;">Sesi formulir kedaluwarsa. Silakan kembali, muat ulang halaman, lalu coba lagi.</p>');
}

$info_presma   = pendaftaran_info('presma');
$info_pengurus = pendaftaran_info('pengurus');

$nupt = ''; // pesan notifikasi per jenis
$success_presma = isset($_GET['sukses_presma']) ? true : false;
$success_pengurus = isset($_GET['sukses_pengurus']) ? true : false;
$keep_presma = [];
$keep_pengurus = [];

// -------------------------------------------------------------
// PROSES PENDAFTARAN CAPRES / WAPRES
// -------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['daftar_presma'])) {
    $nama_presma  = trim($_POST['nama_presma']  ?? '');
    $nama_wapresma= trim($_POST['nama_wapresma'] ?? '');
    $nim    = trim($_POST['nim']    ?? '');
    $prodi  = trim($_POST['prodi']  ?? '');
    $email  = trim($_POST['email']  ?? '');
    $no_hp  = trim($_POST['no_hp']  ?? '');
    $visi   = trim($_POST['visi_misi'] ?? '');

    $keep_presma = array_merge($_POST, [
        'nama_presma' => $nama_presma, 'nama_wapresma' => $nama_wapresma,
        'nim' => $nim, 'prodi' => $prodi, 'email' => $email,
        'no_hp' => $no_hp, 'visi_misi' => $visi,
    ]);

    if (!$info_presma['buka']) {
        $nupt = 'Pendaftaran Capres & Wapres sedang ditutup.';
    } elseif (empty($nama_presma) || empty($nama_wapresma) || empty($email)) {
        $nupt = 'Nama calon Presiden, calon Wakil Presiden, dan email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $nupt = 'Format email tidak valid.';
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO pendaftar_presma (nama_presma, nama_wapresma, nim, prodi, email, no_hp, visi_misi) VALUES (?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'sssssss', $nama_presma, $nama_wapresma, $nim, $prodi, $email, $no_hp, $visi);
        if (mysqli_stmt_execute($stmt)) {
            $keep_presma = [];
            header('Location: pendaftaran.php?sukses_presma=1');
            exit;
        }
        $nupt = 'Terjadi kesalahan saat menyimpan. Silakan coba lagi.';
    }
}

// -------------------------------------------------------------
// PROSES PENDAFTARAN PENGURUS BEM
// -------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['daftar_pengurus'])) {
    $nama    = trim($_POST['nama']    ?? '');
    $nim     = trim($_POST['nim']     ?? '');
    $prodi   = trim($_POST['prodi']   ?? '');
    $email   = trim($_POST['email']   ?? '');
    $no_hp   = trim($_POST['no_hp']   ?? '');
    $dep     = !empty($_POST['departemen_id']) ? (int)$_POST['departemen_id'] : null;
    $jabatan = trim($_POST['pilihan_jabatan'] ?? '');
    $alasan  = trim($_POST['alasan'] ?? '');
    $riwayat = trim($_POST['riwayat'] ?? '');

    $keep_pengurus = array_merge($_POST, [
        'nama' => $nama, 'nim' => $nim, 'prodi' => $prodi, 'email' => $email,
        'no_hp' => $no_hp, 'departemen_id' => $dep, 'pilihan_jabatan' => $jabatan,
        'alasan' => $alasan, 'riwayat' => $riwayat,
    ]);

    if (!$info_pengurus['buka']) {
        $nupt = 'Pendaftaran Pengurus BEM sedang ditutup.';
    } elseif (empty($nama) || empty($email)) {
        $nupt = 'Nama lengkap dan email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $nupt = 'Format email tidak valid.';
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO pendaftar_pengurus (nama, nim, prodi, email, no_hp, departemen_id, pilihan_jabatan, alasan, riwayat) VALUES (?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'sssssisss', $nama, $nim, $prodi, $email, $no_hp, $dep, $jabatan, $alasan, $riwayat);
        if (mysqli_stmt_execute($stmt)) {
            $keep_pengurus = [];
            header('Location: pendaftaran.php?sukses_pengurus=1');
            exit;
        }
        $nupt = 'Terjadi kesalahan saat menyimpan. Silakan coba lagi.';
    }
}

$departemen_opsi = mysqli_query($koneksi, "SELECT * FROM departemen ORDER BY nama_departemen ASC");

include 'header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-nav"><a href="index.php">Home</a><span>/</span>Pendaftaran</div>
        <span class="eyebrow"><i class="bi bi-clipboard-check"></i> Rekrutmen BEM ITM</span>
        <h1>Pendaftaran Pengurus BEM</h1>
        <p class="lead">Bergabung menjadi bagian dari pergerakan mahasiswa di Institut Teknologi Mojosari. Pilih jalur pendaftaran yang sesuai minatmu.</p>
    </div>
</section>

<section>
    <div class="container">
        <?php if ($nupt): ?>
        <div class="alert alert-danger" style="max-width:760px;margin:0 auto 30px;">
            <i class="bi bi-exclamation-triangle-fill"></i> <div><?php echo esc($nupt); ?></div>
        </div>
        <?php endif; ?>

        <div class="grid" style="grid-template-columns:1fr 1fr;gap:30px;align-items:start;">
            <!-- ===== CALON PRES & WAPRES ===== -->
            <div class="card reveal" style="padding:34px;border-top:4px solid var(--gold);">
                <?php if ($success_presma): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill"></i>
                        <div>Pendaftaran berhasil dikirim! Tim kami akan menghubungi kamu melalui email.</div>
                    </div>
                <?php else: ?>
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                    <span style="width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,var(--maroon),var(--maroon-600));color:var(--gold-light);display:flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="bi bi-person-badge"></i></span>
                    <div>
                        <h3 style="margin:0;color:var(--maroon-900);font-size:1.05rem;">Calon Presiden &amp; Wakil Presiden Mahasiswa</h3>
                        <span style="font-size:.78rem;color:var(--ink-soft);">Daftar sebagai pasangan kandidat</span>
                    </div>
                </div>

                <?php if (!$info_presma['buka']): ?>
                <div style="margin:18px 0 6px;padding:18px;background:var(--maroon-tint);border:1px solid var(--line);border-radius:12px;">
                    <b style="color:var(--maroon);"><i class="bi bi-clock"></i> <?php echo $info_presma['pesan'] ?: 'Pendaftaran ditutup'; ?></b>
                    <?php if ($info_presma['buka_tgl'] || $info_presma['tutup_tgl']): ?>
                    <p style="margin:8px 0 0;font-size:.82rem;color:var(--ink-soft);">
                        <?php if ($info_presma['buka_tgl']): ?>Buka: <b><?php echo esc(tanggal_indo($info_presma['buka_tgl'])); ?></b><?php endif; ?>
                        <?php if ($info_presma['tutup_tgl']): ?> &nbsp;·&nbsp; Tutup: <b><?php echo esc(tanggal_indo($info_presma['tutup_tgl'])); ?></b><?php endif; ?>
                    </p>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <form method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="daftar_presma" value="1">
                    <div class="form-group">
                        <label>Nama Calon Presiden Mahasiswa *</label>
                        <input type="text" name="nama_presma" class="form-control" required value="<?php echo esc($keep_presma['nama_presma'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Nama Calon Wakil Presiden Mahasiswa *</label>
                        <input type="text" name="nama_wapresma" class="form-control" required value="<?php echo esc($keep_presma['nama_wapresma'] ?? ''); ?>">
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label>NIM</label>
                            <input type="text" name="nim" class="form-control" value="<?php echo esc($keep_presma['nim'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Program Studi</label>
                            <input type="text" name="prodi" class="form-control" value="<?php echo esc($keep_presma['prodi'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control" required value="<?php echo esc($keep_presma['email'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control" value="<?php echo esc($keep_presma['no_hp'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Visi Misi Singkat Pasangan</label>
                        <textarea name="visi_misi" rows="3" class="form-control"><?php echo esc($keep_presma['visi_misi'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-gold" style="width:100%;">Daftar sebagai Kandidat <i class="bi bi-arrow-right"></i></button>
                </form>
                <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- ===== PENGURUS BEM ===== -->
            <div class="card reveal" style="padding:34px;border-top:4px solid var(--maroon);">
                <?php if ($success_pengurus): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill"></i>
                        <div>Pendaftaran berhasil dikirim! Cek emailmu untuk informasi selanjutnya.</div>
                    </div>
                <?php else: ?>
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                    <span style="width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,var(--maroon),var(--maroon-600));color:var(--gold-light);display:flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="bi bi-people"></i></span>
                    <div>
                        <h3 style="margin:0;color:var(--maroon-900);font-size:1.05rem;">Pengurus BEM</h3>
                        <span style="font-size:.78rem;color:var(--ink-soft);">Daftar sebagai anggota departemen</span>
                    </div>
                </div>

                <?php if (!$info_pengurus['buka']): ?>
                <div style="margin:18px 0 6px;padding:18px;background:var(--maroon-tint);border:1px solid var(--line);border-radius:12px;">
                    <b style="color:var(--maroon);"><i class="bi bi-clock"></i> <?php echo $info_pengurus['pesan'] ?: 'Pendaftaran ditutup'; ?></b>
                    <?php if ($info_pengurus['buka_tgl'] || $info_pengurus['tutup_tgl']): ?>
                    <p style="margin:8px 0 0;font-size:.82rem;color:var(--ink-soft);">
                        <?php if ($info_pengurus['buka_tgl']): ?>Buka: <b><?php echo esc(tanggal_indo($info_pengurus['buka_tgl'])); ?></b><?php endif; ?>
                        <?php if ($info_pengurus['tutup_tgl']): ?> &nbsp;·&nbsp; Tutup: <b><?php echo esc(tanggal_indo($info_pengurus['tutup_tgl'])); ?></b><?php endif; ?>
                    </p>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <form method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="daftar_pengurus" value="1">
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="nama" class="form-control" required value="<?php echo esc($keep_pengurus['nama'] ?? ''); ?>">
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label>NIM</label>
                            <input type="text" name="nim" class="form-control" value="<?php echo esc($keep_pengurus['nim'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Program Studi</label>
                            <input type="text" name="prodi" class="form-control" value="<?php echo esc($keep_pengurus['prodi'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control" required value="<?php echo esc($keep_pengurus['email'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control" value="<?php echo esc($keep_pengurus['no_hp'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Departemen yang Diminati</label>
                        <select name="departemen_id" class="form-control">
                            <option value="">-- Pilih Departemen (opsional) --</option>
                            <?php mysqli_data_seek($departemen_opsi, 0); while ($d = mysqli_fetch_assoc($departemen_opsi)): ?>
                            <option value="<?php echo $d['id']; ?>" <?php echo ((string)($keep_pengurus['departemen_id'] ?? '') == $d['id']) ? 'selected' : ''; ?>><?php echo esc($d['nama_departemen']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Posisi / Jabatan yang Diinginkan</label>
                        <input type="text" name="pilihan_jabatan" class="form-control" value="<?php echo esc($keep_pengurus['pilihan_jabatan'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Alasan Bergabung</label>
                        <textarea name="alasan" rows="2" class="form-control"><?php echo esc($keep_pengurus['alasan'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Pengalaman Organisasi</label>
                        <textarea name="riwayat" rows="2" class="form-control" placeholder="Opsional"><?php echo esc($keep_pengurus['riwayat'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Daftar sebagai Pengurus <i class="bi bi-arrow-right"></i></button>
                </form>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>