<?php
require_once __DIR__ . '/config.php';
$page_title = 'Aspirasi';

$sukses = isset($_GET['sukses']) ? true : false;
$error = '';
$keep = [];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!csrf_verify()) {
        die('<p style="font-family:sans-serif;text-align:center;margin:60px 20px;color:#a3272a;font-size:.95rem;">Sesi formulir kedaluwarsa. Silakan kembali, muat ulang halaman, lalu coba lagi.</p>');
    }
    $nama   = trim($_POST['nama'] ?? '');
    $nim    = trim($_POST['nim'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $jenis  = $_POST['jenis'] ?? 'saran';
    $pesan  = trim($_POST['pesan'] ?? '');

    if (empty($nama) || empty($email) || empty($pesan)) {
        $error = 'Nama, email, dan pesan wajib diisi.';
        $keep = ['nama' => $nama, 'nim' => $nim, 'email' => $email, 'jenis' => $jenis, 'pesan' => $pesan];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
        $keep = ['nama' => $nama, 'nim' => $nim, 'email' => $email, 'jenis' => $jenis, 'pesan' => $pesan];
    } elseif (!in_array($jenis, ['kritik', 'saran', 'pertanyaan'])) {
        $error = 'Jenis aspirasi tidak valid.';
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO aspirasi (nama, nim, email, jenis, pesan) VALUES (?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'sssss', $nama, $nim, $email, $jenis, $pesan);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: aspirasi.php?sukses=1');
            exit;
        } else {
            $error = 'Terjadi kesalahan saat mengirim aspirasi. Silakan coba lagi.';
            $keep = ['nama' => $nama, 'nim' => $nim, 'email' => $email, 'jenis' => $jenis, 'pesan' => $pesan];
        }
    }
}

include 'header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-nav"><a href="index.php">Home</a><span>/</span>Aspirasi</div>
        <span class="eyebrow"><i class="bi bi-chat-dots"></i> Suara Mahasiswa</span>
        <h1>Sampaikan Aspirasi</h1>
        <p class="lead">Kritik, saran, dan pertanyaanmu adalah bahan evaluasi penting bagi kemajuan BEM dan kampus Institut Teknologi Mojosari.</p>
    </div>
</section>

<section>
    <div class="container" style="max-width:720px;">
        <div class="card reveal" style="padding:38px 34px;">
            <?php if ($sukses): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>Terima kasih! Aspirasimu telah berhasil terkirim dan akan segera kami tindaklanjuti.</div>
                </div>
                <p style="text-align:center;margin:0;"><a href="aspirasi.php" class="btn btn-outline"><i class="bi bi-plus-lg"></i> Kirim Aspirasi Lainnya</a></p>
            <?php else: ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div><?php echo esc($error); ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="aspirasi.php">
                <?php echo csrf_field(); ?>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="nama" class="form-control" required value="<?php echo esc($keep['nama'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>NIM (opsional)</label>
                        <input type="text" name="nim" class="form-control" value="<?php echo esc($keep['nim'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" required value="<?php echo esc($keep['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Jenis Aspirasi *</label>
                    <select name="jenis" class="form-control" required>
                        <option value="saran" <?php echo (($keep['jenis'] ?? 'saran')=='saran')?'selected':''; ?>>Saran</option>
                        <option value="kritik" <?php echo (($keep['jenis'] ?? '')=='kritik')?'selected':''; ?>>Kritik</option>
                        <option value="pertanyaan" <?php echo (($keep['jenis'] ?? '')=='pertanyaan')?'selected':''; ?>>Pertanyaan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Pesan *</label>
                    <textarea name="pesan" rows="6" class="form-control" required placeholder="Tuliskan kritik, saran, atau pertanyaanmu di sini..."><?php echo esc($keep['pesan'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Kirim Aspirasi <i class="bi bi-send"></i></button>
            </form>
            <?php endif; ?>
        </div>

        <div style="display:flex;gap:16px;margin-top:26px;flex-wrap:wrap;">
            <div class="card" style="flex:1;min-width:200px;padding:22px;text-align:center;">
                <i class="bi bi-shield-check" style="font-size:1.5rem;color:var(--maroon);"></i>
                <p style="font-size:.82rem;color:var(--ink-soft);margin:10px 0 0;">Data pribadimu dijaga kerahasiaannya</p>
            </div>
            <div class="card" style="flex:1;min-width:200px;padding:22px;text-align:center;">
                <i class="bi bi-clock-history" style="font-size:1.5rem;color:var(--maroon);"></i>
                <p style="font-size:.82rem;color:var(--ink-soft);margin:10px 0 0;">Ditindaklanjuti dalam 3x24 jam kerja</p>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
