<?php
require_once __DIR__ . '/includes/auth.php';
$admin_title = 'Profil Saya';
$msg = '';
$err = '';

$admin_id = (int)$_SESSION['admin_id'];
$admin = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM admin_users WHERE id=$admin_id"));

// Update nama & foto
if (isset($_POST['simpan_profil'])) {
    $nama = sanitize($_POST['nama_lengkap'], 100);
    $foto = $admin['foto'] ?? '';

    if (!empty($_FILES['foto_file']['name'])) {
        $up = upload_img('foto_file', 'admin', ['png','jpg','jpeg','webp']);
        if ($up['ok']) {
            // Hapus foto lama jika ada
            if ($foto && file_exists('../' . $foto) && $foto !== 'assets/img/logobem.png') {
                @unlink('../' . $foto);
            }
            $foto = $up['path'];
        } else {
            $err = $up['error'];
        }
    }

    if (!$err) {
        $stmt = mysqli_prepare($koneksi, "UPDATE admin_users SET nama_lengkap=?, foto=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'ssi', $nama, $foto, $admin_id);
        mysqli_stmt_execute($stmt);
        $_SESSION['admin_nama'] = $nama;
        $admin['nama_lengkap'] = $nama;
        $admin['foto'] = $foto;
        $msg = 'Profil berhasil diperbarui.';
    }
}

// Refresh data
$admin = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM admin_users WHERE id=$admin_id"));

include 'includes/admin-header.php';
?>

<div class="page-head">
    <div>
        <h3><i class="bi bi-person-circle"></i> Profil Saya</h3>
        <p>Kelola informasi akun admin Anda.</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1.2fr;gap:24px;align-items:start;">

    <div class="card" style="padding:28px;text-align:center;">
        <div style="position:relative;width:120px;height:120px;margin:0 auto 18px;">
            <?php if (!empty($admin['foto'])): ?>
            <img src="<?php echo esc(img_url($admin['foto'])); ?>" alt="Foto Profil"
                 style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid var(--gold);box-shadow:0 8px 24px rgba(0,0,0,.15);">
            <?php else: ?>
            <div style="width:120px;height:120px;border-radius:50%;background:linear-gradient(135deg,var(--maroon),var(--maroon-800));color:#fff;display:flex;align-items:center;justify-content:center;font-size:2.8rem;font-weight:700;font-family:'Poppins',sans-serif;border:4px solid var(--gold);box-shadow:0 8px 24px rgba(0,0,0,.15);">
                <?php echo strtoupper(substr($admin['nama_lengkap'],0,1)); ?>
            </div>
            <?php endif; ?>
        </div>
        <h4 style="margin:0 0 4px;color:var(--maroon-900);font-size:1.1rem;"><?php echo esc($admin['nama_lengkap']); ?></h4>
        <div style="font-size:.82rem;color:var(--ink-soft);margin-bottom:6px;">@<?php echo esc($admin['username']); ?></div>
        <span class="badge badge-gold">Admin</span>
        <div style="margin-top:16px;font-size:.78rem;color:var(--ink-soft);">
            <i class="bi bi-calendar3"></i> Bergabung <?php echo date('d M Y', strtotime($admin['created_at'])); ?>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:24px;">

        <div class="card" style="padding:24px;">
            <h4 style="margin:0 0 18px;color:var(--maroon-900);"><i class="bi bi-person-lines-fill"></i> Edit Profil</h4>
            <form method="POST" enctype="multipart/form-data" onsubmit="return cekFotoAdmin();">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" required value="<?php echo esc($admin['nama_lengkap']); ?>">
                </div>
                <div class="form-group">
                    <label>Foto Profil (opsional, maks 1 MB)</label>
                    <input type="file" name="foto_file" id="foto_file_admin" class="form-control" accept="image/png,image/jpeg,image/webp">
                    <small style="color:var(--ink-soft);">Format: PNG, JPG, JPEG, WebP. Maksimal 1 MB.</small>
                </div>
                <button type="submit" name="simpan_profil" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
            </form>
        </div>

    </div>
</div>

<script>
function cekFotoAdmin() {
    var f = document.getElementById('foto_file_admin');
    if (f && f.files && f.files.length > 0 && f.files[0].size > 1024 * 1024) {
        showToast('Ukuran foto melebihi 1 MB.', 'error');
        return false;
    }
    return true;
}
</script>

<?php include 'includes/admin-footer.php'; ?>
