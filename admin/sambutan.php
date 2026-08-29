<?php
require_once __DIR__ . '/includes/auth.php';
$admin_title = 'Sambutan';
$msg = '';
$err = '';

if (isset($_POST['simpan'])) {
    $id = (int)($_POST['id'] ?? 0);
    $jabatan = trim($_POST['jabatan']);
    $nama = trim($_POST['nama']);
    $foto = trim($_POST['foto']);
    $isi = trim($_POST['isi_sambutan']);
    $urutan = (int)$_POST['urutan'];

    // Upload foto sambutan (opsional)
    if (!empty($_FILES['foto_file']['name'])) {
        $up = upload_img('foto_file', 'sambutan');
        if ($up['ok']) {
            $foto = $up['path'];
        } else {
            $err = $up['error'];
        }
    }

    if (!$err) {
        if ($id > 0) {
            $stmt = mysqli_prepare($koneksi, "UPDATE sambutan SET jabatan=?, nama=?, foto=?, isi_sambutan=?, urutan=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'ssssii', $jabatan, $nama, $foto, $isi, $urutan, $id);
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO sambutan (jabatan, nama, foto, isi_sambutan, urutan) VALUES (?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, 'ssssi', $jabatan, $nama, $foto, $isi, $urutan);
        }
        mysqli_stmt_execute($stmt);
        $msg = 'Data sambutan berhasil disimpan.';
    }
}
if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM sambutan WHERE id=" . (int)$_GET['hapus']);
    header('Location: sambutan.php'); exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM sambutan WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $_GET['edit']);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

$list = mysqli_query($koneksi, "SELECT * FROM sambutan ORDER BY urutan ASC");
include 'includes/admin-header.php';
?>

<div class="page-head">
    <div>
        <h3><i class="bi bi-chat-quote"></i> Sambutan</h3>
        <p>Kelola kata sambutan yang tampil di halaman utama.</p>
    </div>
</div>

<div style="margin-bottom:24px;">
    <div class="search-wrap">
        <input type="text" id="adminSambutanSearch" class="form-control" placeholder="Cari nama/jabatan...">
    </div>
</div>

<div style="display:grid;grid-template-columns:.9fr 1.4fr;gap:24px;align-items:start;">
    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);"><?php echo $edit ? 'Edit' : 'Tambah'; ?> Sambutan</h4>
        <form method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo $edit['id'] ?? ''; ?>">
            <div class="form-group">
                <label>Jabatan</label>
                <input type="text" name="jabatan" class="form-control" required placeholder="cth: Wakil Rektor III" value="<?php echo esc($edit['jabatan'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required value="<?php echo esc($edit['nama'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>URL Foto (opsional)</label>
                <input type="text" name="foto" class="form-control" placeholder="https://..." value="<?php echo esc($edit['foto'] ?? ''); ?>">
                <?php if (!empty($edit['foto'])): ?><img src="<?php echo esc(img_url($edit['foto'])); ?>" style="width:54px;height:54px;border-radius:50%;object-fit:cover;margin-top:8px;"><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Upload Foto (opsional, maksimal 1 MB)</label>
                <input type="file" name="foto_file" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label>Isi Sambutan</label>
                <textarea name="isi_sambutan" rows="4" class="form-control" required><?php echo esc($edit['isi_sambutan'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Urutan Tampil</label>
                <input type="number" name="urutan" class="form-control" value="<?php echo esc($edit['urutan'] ?? 1); ?>">
            </div>
            <button type="submit" name="simpan" class="btn btn-primary" style="width:100%;"><i class="bi bi-check-lg"></i> Simpan</button>
            <?php if ($edit): ?><a href="sambutan.php" class="btn btn-outline" style="width:100%;margin-top:8px;justify-content:center;">Batal Edit</a><?php endif; ?>
        </form>
    </div>

    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);">Daftar Sambutan</h4>
        <div class="table-responsive">
        <table>
            <thead><tr><th>Urutan</th><th>Jabatan</th><th>Nama</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($list)): ?>
            <tr>
                <td data-label="Urutan"><?php echo $row['urutan']; ?></td>
                <td data-label="Jabatan"><?php echo esc($row['jabatan']); ?></td>
                <td data-label="Nama"><?php echo esc($row['nama']); ?></td>
                <td data-label="Aksi" class="action-cell">
                    <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
                    <a href="?hapus=<?php echo $row['id']; ?>" onclick="return confirmAction('Hapus sambutan ini?', this.href)" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
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
document.getElementById('adminSambutanSearch')?.addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    document.querySelectorAll('.table-responsive table tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});
</script>
