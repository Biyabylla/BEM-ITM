<?php
require_once __DIR__ . '/includes/auth.php';
$admin_title = 'Berita';
$msg = '';
$err = '';

if (isset($_POST['simpan'])) {
    $id = (int)($_POST['id'] ?? 0);
    $judul = sanitize($_POST['judul'], 200);
    $konten = trim($_POST['konten']);
    $gambar = sanitize($_POST['gambar'], 500);
    $penulis = sanitize($_POST['penulis'], 100) ?: 'Redaksi BEM ITM';
    $tanggal = preg_replace('/[^0-9\-]/', '', $_POST['tanggal_publish']);
    $slug = buat_slug($judul) . '-' . substr(md5($judul . microtime()), 0, 5);

    // Upload gambar berita (opsional)
    if (!empty($_FILES['gambar_file']['name'])) {
        $up = upload_img('gambar_file', 'berita');
        if ($up['ok']) {
            $gambar = $up['path'];
        } else {
            $err = $up['error'];
        }
    }

    if (!$err) {
        if ($id > 0) {
            // Pertahankan slug lama saat update
            $lama = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT slug FROM berita WHERE id=$id"));
            $slug = $lama['slug'] ?? $slug;
            $stmt = mysqli_prepare($koneksi, "UPDATE berita SET judul=?, slug=?, konten=?, gambar=?, penulis=?, tanggal_publish=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'ssssssi', $judul, $slug, $konten, $gambar, $penulis, $tanggal, $id);
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO berita (judul, slug, konten, gambar, penulis, tanggal_publish) VALUES (?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, 'ssssss', $judul, $slug, $konten, $gambar, $penulis, $tanggal);
        }
        mysqli_stmt_execute($stmt);
        $msg = 'Berita berhasil disimpan.';
    }
}
if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM berita WHERE id=" . (int)$_GET['hapus']);
    header('Location: berita.php'); exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM berita WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $_GET['edit']);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

$list = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_publish DESC");
include 'includes/admin-header.php';
?>

<div class="page-head">
    <div>
        <h3><i class="bi bi-newspaper"></i> Berita</h3>
        <p>Kelola berita & informasi kegiatan yang tampil di publik.</p>
    </div>
</div>

<div style="margin-bottom:24px;">
    <input type="text" id="adminBeritaSearch" class="form-control" placeholder="Cari judul berita..." style="width:300px;padding:.5rem .8rem;font-size:1rem;border:1px solid var(--line);border-radius:8px;background:var(--field);color:var(--ink);">
</div>

<div style="display:grid;grid-template-columns:.9fr 1.4fr;gap:24px;align-items:start;">
    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);"><?php echo $edit ? 'Edit' : 'Tambah'; ?> Berita</h4>
        <form method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo $edit['id'] ?? ''; ?>">
            <div class="form-group">
                <label>Judul Berita</label>
                <input type="text" name="judul" class="form-control" required value="<?php echo esc($edit['judul'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Konten</label>
                <textarea name="konten" rows="6" class="form-control" required><?php echo esc($edit['konten'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>URL Gambar (opsional)</label>
                <input type="text" name="gambar" class="form-control" placeholder="https://..." value="<?php echo esc($edit['gambar'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Upload Gambar (opsional, maksimal 1 MB)</label>
                <input type="file" name="gambar_file" class="form-control" accept="image/*">
                <?php if (!empty($edit['gambar']) && !preg_match('~^https?://~', $edit['gambar'])): ?><img src="<?php echo esc(img_url($edit['gambar'])); ?>" style="width:96px;height:56px;object-fit:cover;border-radius:8px;margin-top:8px;border:1px solid var(--line);"><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Penulis</label>
                <input type="text" name="penulis" class="form-control" value="<?php echo esc($edit['penulis'] ?? 'Redaksi BEM ITM'); ?>">
            </div>
            <div class="form-group">
                <label>Tanggal Publish</label>
                <input type="date" name="tanggal_publish" class="form-control" required value="<?php echo esc($edit['tanggal_publish'] ?? date('Y-m-d')); ?>">
            </div>
            <button type="submit" name="simpan" class="btn btn-primary" style="width:100%;"><i class="bi bi-check-lg"></i> Simpan</button>
            <?php if ($edit): ?><a href="berita.php" class="btn btn-outline" style="width:100%;margin-top:8px;justify-content:center;">Batal Edit</a><?php endif; ?>
        </form>
    </div>

    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);">Daftar Berita</h4>
        <div class="table-responsive">
        <table>
            <thead><tr><th>Gambar</th><th>Judul</th><th>Penulis</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($list)): ?>
            <tr>
                <td>
                    <?php if (!empty($row['gambar'])): ?>
                    <img src="<?php echo esc(img_url($row['gambar'])); ?>" alt="Gambar" style="width:72px;height:44px;object-fit:cover;border-radius:8px;border:1px solid var(--line);">
                    <?php else: ?>
                    <span style="color:var(--ink-soft);font-size:.75rem;">-</span>
                    <?php endif; ?>
                </td>
                <td><?php echo esc($row['judul']); ?></td>
                <td><?php echo esc($row['penulis']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['tanggal_publish'])); ?></td>
                <td style="white-space:nowrap;">
                    <a href="../berita-detail.php?slug=<?php echo esc($row['slug']); ?>" target="_blank" class="btn btn-outline btn-sm"><i class="bi bi-eye"></i></a>
                    <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
                    <a href="?hapus=<?php echo $row['id']; ?>" onclick="return confirmAction('Hapus berita ini?', this.href)" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
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
document.getElementById('adminBeritaSearch')?.addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    document.querySelectorAll('.table-responsive table tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});
</script>
