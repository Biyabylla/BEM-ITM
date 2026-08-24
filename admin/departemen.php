<?php
require_once __DIR__ . '/includes/auth.php';
$admin_title = 'Departemen';
$msg = '';

if (isset($_POST['simpan'])) {
    $id = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama_departemen']);
    $deskripsi = trim($_POST['deskripsi']);
    $icon = trim($_POST['icon']) ?: 'bi-people';

    if ($id > 0) {
        $stmt = mysqli_prepare($koneksi, "UPDATE departemen SET nama_departemen=?, deskripsi=?, icon=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'sssi', $nama, $deskripsi, $icon, $id);
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO departemen (nama_departemen, deskripsi, icon) VALUES (?,?,?)");
        mysqli_stmt_bind_param($stmt, 'sss', $nama, $deskripsi, $icon);
    }
    mysqli_stmt_execute($stmt);
    $msg = 'Data departemen berhasil disimpan.';
}
if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM departemen WHERE id=" . (int)$_GET['hapus']);
    header('Location: departemen.php'); exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM departemen WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $_GET['edit']);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

$list = mysqli_query($koneksi, "SELECT d.*, (SELECT COUNT(*) FROM pengurus p WHERE p.departemen_id=d.id) AS jml_anggota FROM departemen d ORDER BY id ASC");
include 'includes/admin-header.php';
?>

<div class="page-head">
    <div>
        <h3><i class="bi bi-diagram-2"></i> Departemen</h3>
        <p>Kelola departemen BEM beserta ikon & deskripsi.</p>
    </div>
</div>

<div style="margin-bottom:24px;">
    <input type="text" id="adminDepartemenSearch" class="form-control" placeholder="Cari nama departemen..." style="width:300px;padding:.5rem .8rem;font-size:1rem;border:1px solid var(--line);border-radius:8px;background:var(--field);color:var(--ink);">
</div>

<div style="display:grid;grid-template-columns:.9fr 1.4fr;gap:24px;align-items:start;">
    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);"><?php echo $edit ? 'Edit' : 'Tambah'; ?> Departemen</h4>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo $edit['id'] ?? ''; ?>">
            <div class="form-group">
                <label>Nama Departemen</label>
                <input type="text" name="nama_departemen" class="form-control" required value="<?php echo esc($edit['nama_departemen'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Deskripsi Singkat</label>
                <textarea name="deskripsi" rows="3" class="form-control"><?php echo esc($edit['deskripsi'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Icon (kelas Bootstrap Icons, cth: bi-people)</label>
                <input type="text" name="icon" class="form-control" value="<?php echo esc($edit['icon'] ?? 'bi-people'); ?>">
            </div>
            <button type="submit" name="simpan" class="btn btn-primary" style="width:100%;"><i class="bi bi-check-lg"></i> Simpan</button>
            <?php if ($edit): ?><a href="departemen.php" class="btn btn-outline" style="width:100%;margin-top:8px;justify-content:center;">Batal Edit</a><?php endif; ?>
        </form>
    </div>

    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);">Daftar Departemen</h4>
        <div class="table-responsive">
        <table>
            <thead><tr><th>Nama Departemen</th><th>Jml Anggota</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($list)): ?>
            <tr>
                <td><i class="bi <?php echo esc($row['icon']); ?>"></i> <?php echo esc($row['nama_departemen']); ?></td>
                <td><span class="badge badge-maroon"><?php echo $row['jml_anggota']; ?> orang</span></td>
                <td style="white-space:nowrap;">
                    <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
                    <a href="?hapus=<?php echo $row['id']; ?>" onclick="return confirmAction('Hapus departemen ini? Program kerja terkait akan ikut terhapus, dan pengurusnya kehilangan departemen.', this.href)" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
<script>
document.getElementById('adminDepartemenSearch')?.addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    document.querySelectorAll('.table-responsive table tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});
</script>
