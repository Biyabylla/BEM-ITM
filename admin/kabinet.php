<?php
require_once __DIR__ . '/includes/auth.php';
$admin_title = 'Kabinet Kepengurusan';
$msg = '';
$err = '';

if (isset($_POST['simpan'])) {
    $id = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama_kabinet']);
    $periode = trim($_POST['periode']);
    $logo = trim($_POST['logo']);

    // Upload logo kabinet
    if (!empty($_FILES['logo_file']['name'])) {
        $up = upload_img('logo_file', 'kabinet', ['png','jpg','jpeg','gif','webp','svg']);
        if ($up['ok']) {
            $logo = $up['path'];
        } else {
            $err = $up['error'];
        }
    }

    if (!$err) {
        if ($id > 0) {
            $stmt = mysqli_prepare($koneksi, "UPDATE kabinet SET nama_kabinet=?, periode=?, logo=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'sssi', $nama, $periode, $logo, $id);
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO kabinet (nama_kabinet, periode, logo, is_aktif) VALUES (?,?,?,0)");
            mysqli_stmt_bind_param($stmt, 'sss', $nama, $periode, $logo);
        }
        mysqli_stmt_execute($stmt);
        $msg = 'Data kabinet berhasil disimpan.';
    }
}
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = mysqli_prepare($koneksi, "DELETE FROM kabinet WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    header('Location: kabinet.php'); exit;
}
if (isset($_GET['aktif'])) {
    $id = (int)$_GET['aktif'];
    mysqli_query($koneksi, "UPDATE kabinet SET is_aktif=0");
    $stmt = mysqli_prepare($koneksi, "UPDATE kabinet SET is_aktif=1 WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    header('Location: kabinet.php'); exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM kabinet WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $_GET['edit']);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

$list = mysqli_query($koneksi, "SELECT *, (SELECT COUNT(*) FROM pengurus WHERE kabinet_id = kabinet.id) AS jml_pengurus FROM kabinet ORDER BY is_aktif DESC, periode DESC");
include 'includes/admin-header.php';
?>

<div class="page-head">
    <div>
        <h3><i class="bi bi-flag"></i> Kabinet Kepengurusan</h3>
        <p>Kelola kabinet per periode beserta logo & status aktif.</p>
    </div>
</div>

<div style="margin-bottom:24px;">
    <input type="text" id="adminKabinetSearch" class="form-control" placeholder="Cari nama kabinet..." style="width:300px;padding:.5rem .8rem;font-size:1rem;border:1px solid var(--line);border-radius:8px;background:var(--field);color:var(--ink);">
</div>

<div style="display:grid;grid-template-columns:.9fr 1.4fr;gap:24px;align-items:start;">
    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);"><?php echo $edit ? 'Edit' : 'Tambah'; ?> Kabinet</h4>
        <form method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo $edit['id'] ?? ''; ?>">
            <div class="form-group">
                <label>Nama Kabinet</label>
                <input type="text" name="nama_kabinet" class="form-control" required placeholder="cth: Kabinet Satya Mandala" value="<?php echo esc($edit['nama_kabinet'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Periode Kepengurusan</label>
                <input type="text" name="periode" class="form-control" required placeholder="cth: 2025-2026" value="<?php echo esc($edit['periode'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>URL Logo Kabinet (opsional)</label>
                <input type="text" name="logo" class="form-control" placeholder="https://..." value="<?php echo esc($edit['logo'] ?? ''); ?>">
                <small style="color:var(--ink-soft);font-size:.75rem;">Atau unggah file di bawah ini.</small>
            </div>
            <div class="form-group">
                <label>Unggah Logo Kabinet (opsional, maksimal 1 MB)</label>
                <input type="file" name="logo_file" class="form-control" accept="image/*">
                <?php if (!empty($edit['logo'])): ?>
                <img src="<?php echo esc(img_url($edit['logo'])); ?>" alt="Logo" style="width:60px;height:60px;border-radius:50%;object-fit:cover;margin-top:10px;">
                <?php endif; ?>
            </div>
            <button type="submit" name="simpan" class="btn btn-primary" style="width:100%;"><i class="bi bi-check-lg"></i> Simpan</button>
            <?php if ($edit): ?><a href="kabinet.php" class="btn btn-outline" style="width:100%;margin-top:8px;justify-content:center;">Batal Edit</a><?php endif; ?>
        </form>
    </div>

    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);">Daftar Kabinet</h4>
        <p style="color:var(--ink-soft);font-size:.82rem;margin:0 0 14px;">Kabinet aktif ditandai bintang. Pengurus &amp; struktur mengikuti kabinet yang dipilih.</p>
        <div class="table-responsive">
        <table>
            <thead><tr><th>Kabinet</th><th>Periode</th><th>Pengurus</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($list)): ?>
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <?php if (!empty($row['logo'])): ?><img src="<?php echo esc(img_url($row['logo'])); ?>" style="width:34px;height:34px;border-radius:50%;object-fit:cover;"><?php endif; ?>
                        <b><?php echo esc($row['nama_kabinet']); ?></b>
                    </div>
                </td>
                <td><?php echo esc($row['periode']); ?></td>
                <td><?php echo (int)$row['jml_pengurus']; ?></td>
                <td><?php echo $row['is_aktif'] ? '<span class="badge badge-gold"><i class="bi bi-star-fill"></i> Aktif</span>' : '<span class="badge badge-maroon">Arsip</span>'; ?></td>
                <td style="white-space:nowrap;">
                    <?php if (!$row['is_aktif']): ?>
                    <a href="?aktif=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm" title="Jadikan aktif"><i class="bi bi-star"></i></a>
                    <?php endif; ?>
                    <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
                    <a href="?hapus=<?php echo $row['id']; ?>" onclick="return confirmAction('Hapus kabinet ini? Pengurus di dalamnya tidak ikut terhapus.', this.href)" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($list) === 0): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--ink-soft);">Belum ada kabinet. Tambahkan yang pertama.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
<script>
document.getElementById('adminKabinetSearch')?.addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    document.querySelectorAll('.table-responsive table tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});
</script>