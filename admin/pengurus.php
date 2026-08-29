<?php
require_once __DIR__ . '/includes/auth.php';
$admin_title = 'Struktur / Pengurus';
$msg = '';
$err = '';

if (isset($_POST['simpan'])) {
    $id = (int)($_POST['id'] ?? 0);
    $nama = sanitize($_POST['nama'], 150);
    $jabatan = sanitize($_POST['jabatan'], 100);
    $kategori_valid = ['pembina','pimpinan','bph','departemen'];
    $kategori = in_array($_POST['kategori'] ?? '', $kategori_valid) ? $_POST['kategori'] : 'departemen';
    $departemen_id = ($kategori === 'departemen' && !empty($_POST['departemen_id'])) ? (int)$_POST['departemen_id'] : null;
    $kabinet_id = !empty($_POST['kabinet_id']) ? (int)$_POST['kabinet_id'] : null;
    $prodi = sanitize($_POST['program_studi'], 100);
    $foto = sanitize($_POST['foto'], 500);
    $ig = sanitize($_POST['instagram'], 200);
    $gm = sanitize($_POST['gmail'], 200);
    $urutan = (int)($_POST['urutan'] ?? 1);

    // Upload foto pengurus (opsional)
    if (!empty($_FILES['foto_file']['name'])) {
        $up = upload_img('foto_file', 'pengurus');
        if ($up['ok']) {
            $foto = $up['path'];
        } else {
            $err = $up['error'];
        }
    }

    if (!$err) {
        if ($id > 0) {
            $stmt = mysqli_prepare($koneksi, "UPDATE pengurus SET nama=?, jabatan=?, kategori=?, departemen_id=?, kabinet_id=?, program_studi=?, foto=?, instagram=?, gmail=?, urutan=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'sssiissssii', $nama, $jabatan, $kategori, $departemen_id, $kabinet_id, $prodi, $foto, $ig, $gm, $urutan, $id);
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO pengurus (nama, jabatan, kategori, departemen_id, kabinet_id, program_studi, foto, instagram, gmail, urutan) VALUES (?,?,?,?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, 'sssiissssi', $nama, $jabatan, $kategori, $departemen_id, $kabinet_id, $prodi, $foto, $ig, $gm, $urutan);
        }
    mysqli_stmt_execute($stmt);
        $msg = 'Data pengurus berhasil disimpan.';
    }
}
if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM pengurus WHERE id=" . (int)$_GET['hapus']);
    header('Location: pengurus.php'); exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM pengurus WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $_GET['edit']);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

$departemen_opsi = mysqli_query($koneksi, "SELECT * FROM departemen ORDER BY nama_departemen ASC");
$kabinet_opsi = mysqli_query($koneksi, "SELECT * FROM kabinet ORDER BY is_aktif DESC, periode DESC");
$kab_aktif_id = (int)(kabinet_aktif()['id'] ?? 0);
$list = mysqli_query($koneksi, "SELECT p.*, d.nama_departemen, k.nama_kabinet, k.periode AS kab_periode FROM pengurus p LEFT JOIN departemen d ON p.departemen_id=d.id LEFT JOIN kabinet k ON p.kabinet_id=k.id ORDER BY p.kabinet_id ASC, d.id ASC, p.urutan ASC");
include 'includes/admin-header.php';
?>
<div class="page-head">
    <div>
        <h3><i class="bi bi-people"></i> Struktur / Pengurus</h3>
        <p>Kelola data pengurus BEM beserta foto, jabatan, &amp; kontak.</p>
    </div>
</div>

<div style="margin-bottom:24px;">
    <div class="search-wrap">
        <input type="text" id="adminPengurusSearch" class="form-control" placeholder="Cari nama, jabatan, atau departemen...">
    </div>
</div>

<div style="display:grid;grid-template-columns:.9fr 1.4fr;gap:24px;align-items:start;">
    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);"><?php echo $edit ? 'Edit' : 'Tambah'; ?> Pengurus</h4>
        <form method="POST" enctype="multipart/form-data" onsubmit="return cekFotoPengurus();">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo $edit['id'] ?? ''; ?>">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required value="<?php echo esc($edit['nama'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Jabatan</label>
                <input type="text" name="jabatan" class="form-control" required placeholder="cth: Presiden Mahasiswa / Ketua Departemen / Anggota" value="<?php echo esc($edit['jabatan'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" id="kategoriSelect" class="form-control" onchange="document.getElementById('deptWrap').style.display=this.value==='departemen'?'block':'none';">
                    <option value="pembina" <?php echo (($edit['kategori'] ?? '')=='pembina')?'selected':''; ?>>Pembina</option>
                    <option value="pimpinan" <?php echo (($edit['kategori'] ?? '')=='pimpinan')?'selected':''; ?>>Pimpinan BPH (Presiden/Wakil)</option>
                    <option value="bph" <?php echo (($edit['kategori'] ?? '')=='bph')?'selected':''; ?>>Anggota BPH (Sekretaris/Bendahara)</option>
                    <option value="departemen" <?php echo (($edit['kategori'] ?? 'departemen')=='departemen')?'selected':''; ?>>Anggota Departemen</option>
                </select>
            </div>
            <div class="form-group" id="deptWrap" style="<?php echo (($edit['kategori'] ?? '') == 'departemen')?'':'display:none;'; ?>">
                <label>Departemen</label>
                <select name="departemen_id" class="form-control">
                    <option value="">-- Pilih Departemen --</option>
                    <?php mysqli_data_seek($departemen_opsi, 0); while ($d = mysqli_fetch_assoc($departemen_opsi)): ?>
                    <option value="<?php echo $d['id']; ?>" <?php echo (($edit['departemen_id'] ?? '')==$d['id'])?'selected':''; ?>><?php echo esc($d['nama_departemen']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Kabinet</label>
                <select name="kabinet_id" class="form-control">
                    <option value="">-- Tidak ada --</option>
                    <?php mysqli_data_seek($kabinet_opsi, 0); while ($kb = mysqli_fetch_assoc($kabinet_opsi)): ?>
                    <option value="<?php echo $kb['id']; ?>" <?php echo ((($edit['kabinet_id'] ?? $kab_aktif_id)==$kb['id']))?'selected':''; ?>><?php echo esc($kb['nama_kabinet']); ?> (<?php echo esc($kb['periode']); ?>)<?php echo $kb['is_aktif'] ? ' *' : ''; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Program Studi</label>
                <input type="text" name="program_studi" class="form-control" value="<?php echo esc($edit['program_studi'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>URL Foto (opsional, kosongkan untuk avatar otomatis)</label>
                <input type="text" name="foto" class="form-control" placeholder="https://..." value="<?php echo esc($edit['foto'] ?? ''); ?>">
                <?php if (!empty($edit['foto'])): ?><img src="<?php echo esc(img_url($edit['foto'])); ?>" style="width:54px;height:54px;border-radius:50%;object-fit:cover;margin-top:8px;"><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Upload Foto (opsional, maksimal 1 MB)</label>
                <input type="file" name="foto_file" id="foto_file" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label>Link Instagram</label>
                <input type="text" name="instagram" class="form-control" placeholder="https://instagram.com/username" value="<?php echo esc($edit['instagram'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Email (Gmail)</label>
                <input type="email" name="gmail" class="form-control" placeholder="nama@email.com" value="<?php echo esc($edit['gmail'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Urutan Tampil</label>
                <input type="number" name="urutan" class="form-control" value="<?php echo esc($edit['urutan'] ?? 1); ?>">
            </div>
            <button type="submit" name="simpan" class="btn btn-primary" style="width:100%;"><i class="bi bi-check-lg"></i> Simpan</button>
            <?php if ($edit): ?><a href="pengurus.php" class="btn btn-outline" style="width:100%;margin-top:8px;justify-content:center;">Batal Edit</a><?php endif; ?>
        </form>
    </div>

    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);">Daftar Pengurus</h4>
        <div class="table-responsive">
        <table>
            <thead><tr><th>Nama</th><th>Jabatan</th><th>Departemen</th><th>Kabinet</th><th>Prodi</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($list)): ?>
            <tr>
                <td data-label="Nama"><?php echo esc($row['nama']); ?></td>
                <td data-label="Jabatan"><?php echo esc($row['jabatan']); ?></td>
                <td data-label="Departemen"><?php
                    $label_map = ['pembina'=>'Pembina','pimpinan'=>'Pimpinan BPH','bph'=>'Anggota BPH'];
                    echo $row['kategori']=='departemen' ? esc($row['nama_departemen'] ?? '-') : '<span class="badge badge-gold">'.esc($label_map[$row['kategori']] ?? ucfirst($row['kategori'])).'</span>';
                    ?></td>
                <td data-label="Kabinet"><?php echo $row['kabinet_id'] ? esc($row['nama_kabinet']) . ' <small style="color:var(--ink-soft);">(' . esc($row['kab_periode']) . ')</small>' : '<small style="color:var(--ink-soft);">-</small>'; ?></td>
                <td data-label="Prodi"><?php echo esc($row['program_studi'] ?: '-'); ?></td>
                <td data-label="Aksi" class="action-cell">
                    <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
                    <a href="?hapus=<?php echo $row['id']; ?>" onclick="return confirmAction('Hapus pengurus ini?', this.href)" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
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
document.getElementById('adminPengurusSearch')?.addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    document.querySelectorAll('.table-responsive table tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});

function cekFotoPengurus() {
    var f = document.getElementById('foto_file');
    if (f && f.files && f.files.length > 0 && f.files[0].size > 1024 * 1024) {
        showToast('Ukuran foto melebihi 1 MB. Silakan pilih gambar yang lebih kecil.', 'error');
        return false;
    }
    return true;
}
</script>
