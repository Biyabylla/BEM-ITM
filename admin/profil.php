<?php
require_once __DIR__ . '/includes/auth.php';
$admin_title = 'Profil & Visi Misi';
$msg = '';

// Update profil utama (deskripsi & visi)
if (isset($_POST['simpan_profil'])) {
    $deskripsi = trim($_POST['deskripsi']);
    $visi = trim($_POST['visi']);
    $ada = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id FROM profil_bem ORDER BY id DESC LIMIT 1"));
    if ($ada) {
        $stmt = mysqli_prepare($koneksi, "UPDATE profil_bem SET deskripsi=?, visi=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'ssi', $deskripsi, $visi, $ada['id']);
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO profil_bem (deskripsi, visi) VALUES (?,?)");
        mysqli_stmt_bind_param($stmt, 'ss', $deskripsi, $visi);
    }
    mysqli_stmt_execute($stmt);
    $msg = 'Profil & Visi berhasil diperbarui.';
}

// Tambah misi
if (isset($_POST['tambah_misi'])) {
    $isi = trim($_POST['isi_misi']);
    $urutan = (int)(mysqli_fetch_row(mysqli_query($koneksi,"SELECT COALESCE(MAX(urutan),0)+1 FROM misi"))[0] ?? 0);
    $stmt = mysqli_prepare($koneksi, "INSERT INTO misi (isi_misi, urutan) VALUES (?,?)");
    mysqli_stmt_bind_param($stmt, 'si', $isi, $urutan);
    mysqli_stmt_execute($stmt);
    $msg = 'Poin misi ditambahkan.';
}
if (isset($_GET['hapus_misi'])) {
    $id = (int)$_GET['hapus_misi'];
    $stmt = mysqli_prepare($koneksi, "DELETE FROM misi WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    header('Location: profil.php'); exit;
}

// Tambah tujuan
if (isset($_POST['tambah_tujuan'])) {
    $isi = trim($_POST['isi_tujuan']);
    $urutan = (int)(mysqli_fetch_row(mysqli_query($koneksi,"SELECT COALESCE(MAX(urutan),0)+1 FROM tujuan"))[0] ?? 0);
    $stmt = mysqli_prepare($koneksi, "INSERT INTO tujuan (isi_tujuan, urutan) VALUES (?,?)");
    mysqli_stmt_bind_param($stmt, 'si', $isi, $urutan);
    mysqli_stmt_execute($stmt);
    $msg = 'Poin tujuan ditambahkan.';
}
if (isset($_GET['hapus_tujuan'])) {
    $id = (int)$_GET['hapus_tujuan'];
    $stmt = mysqli_prepare($koneksi, "DELETE FROM tujuan WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    header('Location: profil.php'); exit;
}

$profil = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM profil_bem ORDER BY id DESC LIMIT 1"));
$misi_list = mysqli_query($koneksi, "SELECT * FROM misi ORDER BY urutan ASC");
$tujuan_list = mysqli_query($koneksi, "SELECT * FROM tujuan ORDER BY urutan ASC");

include 'includes/admin-header.php';
?>

<div class="page-head">
    <div>
        <h3><i class="bi bi-info-circle"></i> Profil &amp; Visi Misi</h3>
        <p>Kelola deskripsi, visi, misi &amp; tujuan organisasi.</p>
    </div>
</div>

<div class="card" style="padding:24px;margin-bottom:24px;">
    <h4 style="margin:0 0 18px;color:var(--maroon-900);">Deskripsi & Visi</h4>
    <form method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label>Deskripsi Singkat BEM (ditampilkan di Home & Tentang Kami)</label>
            <textarea name="deskripsi" rows="4" class="form-control"><?php echo esc($profil['deskripsi'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>Visi</label>
            <textarea name="visi" rows="3" class="form-control"><?php echo esc($profil['visi'] ?? ''); ?></textarea>
        </div>
        <button type="submit" name="simpan_profil" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Perubahan</button>
    </form>
</div>

<div style="margin-bottom:24px;">
    <input type="text" id="profilSearch" class="form-control" placeholder="Cari poin misi/tujuan..." style="width:300px;padding:.5rem .8rem;font-size:1rem;border:1px solid var(--line);border-radius:8px;background:var(--field);color:var(--ink);">
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);">Poin Misi</h4>
        <form method="POST" style="display:flex;gap:10px;margin-bottom:18px;">
            <?php echo csrf_field(); ?>
            <input type="text" name="isi_misi" class="form-control" placeholder="Tulis poin misi baru..." required>
            <button type="submit" name="tambah_misi" class="btn btn-primary btn-sm">Tambah</button>
        </form>
        <?php while ($m = mysqli_fetch_assoc($misi_list)): ?>
        <div style="display:flex;justify-content:space-between;gap:10px;padding:10px 0;border-bottom:1px solid var(--line);font-size:.85rem;">
            <span><?php echo esc($m['isi_misi']); ?></span>
            <a href="?hapus_misi=<?php echo $m['id']; ?>" onclick="return confirmAction('Hapus poin misi ini?', this.href)" style="color:#a3272a;"><i class="bi bi-trash"></i></a>
        </div>
        <?php endwhile; ?>
    </div>
    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);">Poin Tujuan</h4>
        <form method="POST" style="display:flex;gap:10px;margin-bottom:18px;">
            <?php echo csrf_field(); ?>
            <input type="text" name="isi_tujuan" class="form-control" placeholder="Tulis poin tujuan baru..." required>
            <button type="submit" name="tambah_tujuan" class="btn btn-primary btn-sm">Tambah</button>
        </form>
        <?php while ($t = mysqli_fetch_assoc($tujuan_list)): ?>
        <div style="display:flex;justify-content:space-between;gap:10px;padding:10px 0;border-bottom:1px solid var(--line);font-size:.85rem;">
            <span><?php echo esc($t['isi_tujuan']); ?></span>
            <a href="?hapus_tujuan=<?php echo $t['id']; ?>" onclick="return confirmAction('Hapus poin tujuan ini?', this.href)" style="color:#a3272a;"><i class="bi bi-trash"></i></a>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
document.getElementById('profilSearch')?.addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    document.querySelectorAll('.card > div > div').forEach(item => {
        item.style.display = item.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});
</script>

<?php include 'includes/admin-footer.php'; ?>
