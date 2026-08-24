<?php
require_once __DIR__ . '/includes/auth.php';
$admin_title = 'Program Kerja';
$msg = '';
$err = '';

if (isset($_POST['simpan'])) {
    $id = (int)($_POST['id'] ?? 0);
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $tanggal = $_POST['tanggal_kegiatan'];
    $departemen_id = (int)$_POST['departemen_id'];
    $kabinet_id = !empty($_POST['kabinet_id']) ? (int)$_POST['kabinet_id'] : (int)(kabinet_aktif()['id'] ?? 0);
    $status = $_POST['status'];
    $gambar = trim($_POST['gambar']);

    // Upload gambar program kerja (opsional)
    if (!empty($_FILES['gambar_file']['name'])) {
        $up = upload_img('gambar_file', 'programkerja');
        if ($up['ok']) {
            $gambar = $up['path'];
        } else {
            $err = $up['error'];
        }
    }

    if (!$err) {
        if ($id > 0) {
            $stmt = mysqli_prepare($koneksi, "UPDATE program_kerja SET judul=?, deskripsi=?, tanggal_kegiatan=?, departemen_id=?, kabinet_id=?, status=?, gambar=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'sssiissi', $judul, $deskripsi, $tanggal, $departemen_id, $kabinet_id, $status, $gambar, $id);
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO program_kerja (judul, deskripsi, tanggal_kegiatan, departemen_id, kabinet_id, status, gambar) VALUES (?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, 'sssiiss', $judul, $deskripsi, $tanggal, $departemen_id, $kabinet_id, $status, $gambar);
        }
        mysqli_stmt_execute($stmt);
        $msg = 'Program kerja berhasil disimpan.';
    }
}
if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM program_kerja WHERE id=" . (int)$_GET['hapus']);
    header('Location: program-kerja.php'); exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM program_kerja WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $_GET['edit']);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

$departemen_opsi = mysqli_query($koneksi, "SELECT * FROM departemen ORDER BY nama_departemen ASC");
$kabinet_opsi = mysqli_query($koneksi, "SELECT * FROM kabinet ORDER BY is_aktif DESC, periode DESC");
$list = mysqli_query($koneksi, "SELECT pk.*, d.nama_departemen, k.nama_kabinet FROM program_kerja pk LEFT JOIN departemen d ON pk.departemen_id=d.id LEFT JOIN kabinet k ON pk.kabinet_id=k.id ORDER BY pk.tanggal_kegiatan DESC");
include 'includes/admin-header.php';
?>

<div class="page-head">
    <div>
        <h3><i class="bi bi-clipboard2-check"></i> Program Kerja</h3>
        <p>Kelola program kerja per departemen & kabinet.</p>
    </div>
</div>

<div style="margin-bottom:24px;">
    <input type="text" id="adminProkerSearch" class="form-control" placeholder="Cari judul program..." style="width:300px;padding:.5rem .8rem;font-size:1rem;border:1px solid var(--line);border-radius:8px;background:var(--field);color:var(--ink);">
</div>

<div style="display:grid;grid-template-columns:.9fr 1.4fr;gap:24px;align-items:start;">
    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);"><?php echo $edit ? 'Edit' : 'Tambah'; ?> Program Kerja</h4>
        <form method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo $edit['id'] ?? ''; ?>">
            <div class="form-group">
                <label>Judul Program Kerja</label>
                <input type="text" name="judul" class="form-control" required value="<?php echo esc($edit['judul'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="form-control" required><?php echo esc($edit['deskripsi'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Tanggal Kegiatan</label>
                <input type="date" name="tanggal_kegiatan" class="form-control" required value="<?php echo esc($edit['tanggal_kegiatan'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Departemen Penanggung Jawab</label>
                <select name="departemen_id" class="form-control" required>
                    <option value="">-- Pilih Departemen --</option>
                    <?php mysqli_data_seek($departemen_opsi, 0); while ($d = mysqli_fetch_assoc($departemen_opsi)): ?>
                    <option value="<?php echo $d['id']; ?>" <?php echo (($edit['departemen_id'] ?? '')==$d['id'])?'selected':''; ?>><?php echo esc($d['nama_departemen']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Kabinet</label>
                <select name="kabinet_id" class="form-control">
                    <?php mysqli_data_seek($kabinet_opsi, 0); while ($kb = mysqli_fetch_assoc($kabinet_opsi)): ?>
                    <option value="<?php echo $kb['id']; ?>" <?php echo ((($edit['kabinet_id'] ?? (kabinet_aktif()['id'] ?? 0))==$kb['id']))?'selected':''; ?>><?php echo esc($kb['nama_kabinet']); ?> (<?php echo esc($kb['periode']); ?>)<?php echo $kb['is_aktif'] ? ' *' : ''; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="akan datang" <?php echo (($edit['status'] ?? '')=='akan datang')?'selected':''; ?>>Akan Datang</option>
                    <option value="berlangsung" <?php echo (($edit['status'] ?? '')=='berlangsung')?'selected':''; ?>>Berlangsung</option>
                    <option value="selesai" <?php echo (($edit['status'] ?? '')=='selesai')?'selected':''; ?>>Selesai</option>
                </select>
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
            <button type="submit" name="simpan" class="btn btn-primary" style="width:100%;"><i class="bi bi-check-lg"></i> Simpan</button>
            <?php if ($edit): ?><a href="program-kerja.php" class="btn btn-outline" style="width:100%;margin-top:8px;justify-content:center;">Batal Edit</a><?php endif; ?>
        </form>
    </div>

    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);">Daftar Program Kerja</h4>
        <div class="table-responsive">
        <table>
            <thead><tr><th>Judul</th><th>Departemen</th><th>Kabinet</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($list)): ?>
            <tr>
                <td><?php echo esc($row['judul']); ?></td>
                <td><?php echo esc($row['nama_departemen'] ?? '-'); ?></td>
                <td><?php echo esc($row['nama_kabinet'] ?? '-'); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['tanggal_kegiatan'])); ?></td>
                <td><span class="badge badge-maroon"><?php echo esc(ucfirst($row['status'])); ?></span></td>
                <td style="white-space:nowrap;">
                    <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
                    <a href="?hapus=<?php echo $row['id']; ?>" onclick="return confirmAction('Hapus program kerja ini?', this.href)" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
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
document.getElementById('adminProkerSearch')?.addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    document.querySelectorAll('.table-responsive table tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});
</script>
