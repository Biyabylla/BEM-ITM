<?php
require_once __DIR__ . '/includes/auth.php';
$admin_title = 'Identitas & Pengaturan Situs';
$msg = '';
$err = '';

if (isset($_POST['simpan'])) {
    $logo = trim($_POST['logo'] ?? '');

    // Upload logo BEM
    if (!empty($_FILES['logo_file']['name'])) {
        $up = upload_img('logo_file', 'logo_bem', ['png','jpg','jpeg','gif','webp','svg','ico']);
        if ($up['ok']) {
            $logo = $up['path'];
        } else {
            $err = $up['error'];
        }
    }

    if (!$err) {
        simpan_setting('logo_bem', $logo);

        $fields = [
            'site_name'       => $_POST['site_name']       ?? '',
            'site_short'      => $_POST['site_short']      ?? '',
            'site_periode'    => $_POST['site_periode']    ?? '',
            'kontak_alamat'   => $_POST['kontak_alamat']   ?? '',
            'kontak_email'    => $_POST['kontak_email']    ?? '',
            'kontak_instagram'=> $_POST['kontak_instagram'] ?? '',
            'kontak_youtube'  => $_POST['kontak_youtube']  ?? '',
        ];
        foreach ($fields as $k => $v) simpan_setting($k, $v);

        $msg = 'Pengaturan berhasil disimpan.';
    }
}

$logo_now = isset($_POST['simpan']) && !$err ? $logo : setting('logo_bem', 'assets/img/logobem.png');
include 'includes/admin-header.php';
?>

<div class="page-head">
    <div>
        <h3><i class="bi bi-gear-fill"></i> Identitas &amp; Pengaturan Situs</h3>
        <p>Kelola identitas organisasi, periode, kontak &amp; logo yang tampil di seluruh website.</p>
    </div>
</div>

<form method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">
        <div style="display:flex;flex-direction:column;gap:24px;">
            <div class="card" style="padding:24px;">
                <h4 style="margin:0 0 16px;color:var(--maroon-900);"><i class="bi bi-info-circle"></i> Identitas Organisasi</h4>
                <div class="form-group">
                    <label>Nama Organisasi (Nama Panjang)</label>
                    <input type="text" name="site_name" class="form-control" value="<?php echo esc(setting('site_name', SITE_NAME_DEFAULT)); ?>">
                </div>
                <div class="form-group">
                    <label>Nama Singkat</label>
                    <input type="text" name="site_short" class="form-control" value="<?php echo esc(setting('site_short', SITE_SHORT_DEFAULT)); ?>">
                </div>
                <div class="form-group">
                    <label>Periode Kepengurusan</label>
                    <input type="text" name="site_periode" class="form-control" placeholder="cth: 2025-2026" value="<?php echo esc(setting('site_periode', SITE_PERIODE_DEFAULT)); ?>">
                    <small style="color:var(--ink-soft);">Digunakan di judul halaman, hero struktur, index &amp; footer.</small>
                </div>
            </div>

            <div class="card" style="padding:24px;">
                <h4 style="margin:0 0 16px;color:var(--maroon-900);"><i class="bi bi-image"></i> Logo BEM / Ikon Situs</h4>
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:18px;padding:14px;background:var(--cream);border:1px dashed var(--line);border-radius:12px;">
                    <img src="<?php echo esc(img_url($logo_now)); ?>" alt="Logo saat ini" style="width:76px;height:76px;border-radius:50%;object-fit:cover;background:var(--field);border:2px solid var(--line);">
                    <div>
                        <div style="font-weight:700;color:var(--maroon-900);margin-bottom:4px;">Logo Saat Ini</div>
                        <div style="font-size:.78rem;color:var(--ink-soft);word-break:break-all;"><?php echo esc($logo_now); ?></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>URL Logo</label>
                    <input type="text" name="logo" class="form-control" placeholder="assets/img/logobem.png atau https://..." value="<?php echo esc($logo_now); ?>">
                </div>
                <div class="form-group">
                    <label>Unggah Logo Baru (opsional, maksimal 1 MB)</label>
                    <input type="file" name="logo_file" class="form-control" accept="image/*">
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:24px;">
            <div class="card" style="padding:24px;">
                <h4 style="margin:0 0 16px;color:var(--maroon-900);"><i class="bi bi-envelope"></i> Kontak &amp; Media Sosial</h4>
                <div class="form-group">
                    <label>Alamat (Footer)</label>
                    <textarea name="kontak_alamat" class="form-control" rows="2" style="resize:vertical;"><?php echo esc(setting('kontak_alamat', 'Mojosari, Ngepeh, Kec. Loceret, Kabupaten Nganjuk, Jawa Timur 64471')); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="kontak_email" class="form-control" value="<?php echo esc(setting('kontak_email', 'bem@itmojosari.ac.id')); ?>">
                </div>
                <div class="form-group">
                    <label>URL Instagram</label>
                    <input type="text" name="kontak_instagram" class="form-control" placeholder="https://instagram.com/..." value="<?php echo esc(setting('kontak_instagram', 'https://www.instagram.com/bem_itmnganjuk/')); ?>">
                </div>
                <div class="form-group">
                    <label>URL YouTube</label>
                    <input type="text" name="kontak_youtube" class="form-control" placeholder="https://youtube.com/..." value="<?php echo esc(setting('kontak_youtube', 'https://share.google/GqxxOel6affLvPU0t')); ?>">
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top:24px;display:flex;gap:10px;">
        <button type="submit" name="simpan" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Semua Pengaturan</button>
        <a href="index.php" class="btn btn-outline">Kembali</a>
    </div>
</form>

<?php include 'includes/admin-footer.php'; ?>