<?php
require_once __DIR__ . '/includes/auth.php';
$admin_title = 'Pendaftaran Rekrutmen';
$msg = '';
$err = '';

// Setel status pendaftar
if (isset($_GET['status']) && isset($_GET['tb'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    $tb = $_GET['tb'];
    if (in_array($status, ['baru','diterima','ditolak']) && in_array($tb, ['presma','pengurus'])) {
        $tabel = ($tb === 'presma') ? 'pendaftar_presma' : 'pendaftar_pengurus';
        $stmt = mysqli_prepare($koneksi, "UPDATE $tabel SET status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'si', $status, $id);
        mysqli_stmt_execute($stmt);
    }
    header('Location: pendaftaran.php'); exit;
}
if (isset($_GET['hapus']) && isset($_GET['tb'])) {
    $tb = $_GET['tb'];
    if (in_array($tb, ['presma','pengurus'])) {
        $tabel = ($tb === 'presma') ? 'pendaftar_presma' : 'pendaftar_pengurus';
        mysqli_query($koneksi, "DELETE FROM $tabel WHERE id=" . (int)$_GET['hapus']);
    }
    header('Location: pendaftaran.php'); exit;
}

// Simpan setelan buka/tutup pendaftaran
if (isset($_POST['simpan_setelan'])) {
    $jenis = ($_POST['jenis'] ?? '') === 'presma' ? 'presma' : 'pengurus';
    $prefix = $jenis === 'presma' ? 'pendaftaran_presma' : 'pendaftaran_pengurus';
    $status = ($_POST['status'] ?? '') === 'buka' ? 'buka' : 'tutup';
    $buka = trim($_POST['buka'] ?? '');
    $tutup = trim($_POST['tutup'] ?? '');
    simpan_setting($prefix . '_status', $status);
    simpan_setting($prefix . '_buka', $buka);
    simpan_setting($prefix . '_tutup', $tutup);
    $msg = 'Setelan pendaftaran ' . ($jenis === 'presma' ? 'Cabupres/Wapres' : 'Pengurus BEM') . ' disimpan.';
}

$list_presma = [];
$q1 = mysqli_query($koneksi, "SELECT * FROM pendaftar_presma ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($q1)) $list_presma[] = $row;

$list_pengurus = [];
$q2 = mysqli_query($koneksi, "SELECT p.*, d.nama_departemen FROM pendaftar_pengurus p LEFT JOIN departemen d ON p.departemen_id=d.id ORDER BY p.created_at DESC");
while ($row = mysqli_fetch_assoc($q2)) $list_pengurus[] = $row;

$info_presma   = pendaftaran_info('presma');
$info_pengurus = pendaftaran_info('pengurus');

include 'includes/admin-header.php';
function badge_status($s) {
    $map = ['baru'=>'badge-gold', 'diterima'=>'badge-maroon', 'ditolak'=>'badge-danger'];
    return '<span class="badge ' . ($map[$s] ?? 'badge-gold') . '">' . esc(ucfirst($s)) . '</span>';
}
?>

<div class="page-head">
    <div>
        <h3><i class="bi bi-person-check"></i> Pendaftaran Rekrutmen</h3>
        <p>Atur buka/tutup pendaftaran beserta periode tanggalnya, dan kelola data pendaftar.</p>
    </div>
</div>

<div style="margin-bottom:24px;">
    <div class="search-wrap">
        <input type="text" id="adminPendaftaranSearch" class="form-control" placeholder="Cari nama calon...">
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:30px;">
    <!-- Setelan Presma -->
    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 6px;color:var(--maroon-900);"><i class="bi bi-person-badge"></i> Cabupres &amp; Wapres Mahasiswa</h4>
        <span class="badge <?php echo $info_presma['buka'] ? 'badge-maroon' : 'badge-danger'; ?>" style="margin-bottom:14px;display:inline-block;">
            <?php echo $info_presma['status'] === 'buka' ? 'Sedang dibuka' : 'Ditutup'; ?>
        </span>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="simpan_setelan" value="1">
            <input type="hidden" name="jenis" value="presma">
            <div class="form-group">
                <label>Status Pendaftaran</label>
                <select name="status" class="form-control">
                    <option value="buka" <?php echo $info_presma['status']=='buka'?'selected':''; ?>>Buka (terima pendaftar)</option>
                    <option value="tutup" <?php echo $info_presma['status']=='tutup'?'selected':''; ?>>Tutup</option>
                </select>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Tanggal Buka</label>
                    <input type="date" name="buka" class="form-control" value="<?php echo esc($info_presma['buka_tgl']); ?>">
                </div>
                <div class="form-group">
                    <label>Tanggal Tutup</label>
                    <input type="date" name="tutup" class="form-control" value="<?php echo esc($info_presma['tutup_tgl']); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Setelan</button>
        </form>
    </div>

    <!-- Setelan Pengurus -->
    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 6px;color:var(--maroon-900);"><i class="bi bi-people"></i> Pengurus BEM</h4>
        <span class="badge <?php echo $info_pengurus['buka'] ? 'badge-maroon' : 'badge-danger'; ?>" style="margin-bottom:14px;display:inline-block;">
            <?php echo $info_pengurus['status'] === 'buka' ? 'Sedang dibuka' : 'Ditutup'; ?>
        </span>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="simpan_setelan" value="1">
            <input type="hidden" name="jenis" value="pengurus">
            <div class="form-group">
                <label>Status Pendaftaran</label>
                <select name="status" class="form-control">
                    <option value="buka" <?php echo $info_pengurus['status']=='buka'?'selected':''; ?>>Buka (terima pendaftar)</option>
                    <option value="tutup" <?php echo $info_pengurus['status']=='tutup'?'selected':''; ?>>Tutup</option>
                </select>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Tanggal Buka</label>
                    <input type="date" name="buka" class="form-control" value="<?php echo esc($info_pengurus['buka_tgl']); ?>">
                </div>
                <div class="form-group">
                    <label>Tanggal Tutup</label>
                    <input type="date" name="tutup" class="form-control" value="<?php echo esc($info_pengurus['tutup_tgl']); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Setelan</button>
        </form>
    </div>
</div>

<div class="card" style="padding:24px;margin-bottom:24px;">
    <h4 style="margin:0 0 14px;color:var(--maroon-900);"><i class="bi bi-person-badge"></i> Daftar Calon Presma &amp; Wapres (<?php echo count($list_presma); ?>)</h4>
    <div class="table-responsive">
    <table>
        <thead><tr><th>Nama Calon Pres</th><th>Nama Calon Wapres</th><th>Kontak</th><th>Visi Misi</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php if (empty($list_presma)): ?>
            <tr><td colspan="7" style="text-align:center;color:var(--ink-soft);">Belum ada pendaftar.</td></tr>
        <?php else: foreach ($list_presma as $r): ?>
            <tr>
                <td data-label="Calon Pres"><?php echo esc($r['nama_presma']); ?><br><span style="color:var(--ink-soft);font-size:.74rem;"><?php echo esc($r['prodi'] ?? '-'); ?></span></td>
                <td data-label="Calon Wapres"><?php echo esc($r['nama_wapresma']); ?></td>
                <td data-label="Kontak"><?php echo esc($r['email']); ?><?php if (!empty($r['no_hp'])): ?><br><span style="color:var(--ink-soft);font-size:.74rem;"><?php echo esc($r['no_hp']); ?></span><?php endif; ?></td>
                <td data-label="Visi Misi" style="max-width:220px;"><?php echo esc(mb_strimwidth($r['visi_misi'] ?? '',0,70,'...')); ?></td>
                <td data-label="Status"><?php echo badge_status($r['status']); ?></td>
                <td data-label="Tanggal"><?php echo date('d/m/Y', strtotime($r['created_at'])); ?></td>
                <td data-label="Aksi" class="action-cell">
                    <a href="?tb=presma&id=<?php echo $r['id']; ?>&status=baru" class="btn btn-outline btn-sm" title="Tandai Baru">B</a>
                    <a href="?tb=presma&id=<?php echo $r['id']; ?>&status=diterima" class="btn btn-outline btn-sm" title="Terima"><i class="bi bi-check-lg"></i></a>
                    <a href="?tb=presma&id=<?php echo $r['id']; ?>&status=ditolak" class="btn btn-outline btn-sm" title="Tolak"><i class="bi bi-x-lg"></i></a>
                    <a href="?tb=presma&hapus=<?php echo $r['id']; ?>" onclick="return confirmAction('Hapus pendaftar ini?', this.href)" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    </div>
</div>

<div class="card" style="padding:24px;margin-bottom:24px;">
    <h4 style="margin:0 0 14px;color:var(--maroon-900);"><i class="bi bi-people"></i> Daftar Pendaftar Pengurus BEM (<?php echo count($list_pengurus); ?>)</h4>
    <div class="table-responsive">
    <table>
        <thead><tr><th>Nama</th><th>Departemen</th><th>Posisi</th><th>Kontak</th><th>Alasan</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php if (empty($list_pengurus)): ?>
            <tr><td colspan="8" style="text-align:center;color:var(--ink-soft);">Belum ada pendaftar.</td></tr>
        <?php else: foreach ($list_pengurus as $r): ?>
            <tr>
                <td data-label="Nama"><?php echo esc($r['nama']); ?><br><span style="color:var(--ink-soft);font-size:.74rem;"><?php echo esc($r['prodi'] ?? '-'); ?></span></td>
                <td data-label="Departemen"><?php echo esc($r['nama_departemen'] ?? '-'); ?></td>
                <td data-label="Posisi"><?php echo esc($r['pilihan_jabatan'] ?? '-'); ?></td>
                <td data-label="Kontak"><?php echo esc($r['email']); ?><?php if (!empty($r['no_hp'])): ?><br><span style="color:var(--ink-soft);font-size:.74rem;"><?php echo esc($r['no_hp']); ?></span><?php endif; ?></td>
                <td data-label="Alasan" style="max-width:200px;"><?php echo esc(mb_strimwidth($r['alasan'] ?? '',0,60,'...')); ?></td>
                <td data-label="Status"><?php echo badge_status($r['status']); ?></td>
                <td data-label="Tanggal"><?php echo date('d/m/Y', strtotime($r['created_at'])); ?></td>
                <td data-label="Aksi" class="action-cell">
                    <a href="?tb=pengurus&id=<?php echo $r['id']; ?>&status=baru" class="btn btn-outline btn-sm" title="Tandai Baru">B</a>
                    <a href="?tb=pengurus&id=<?php echo $r['id']; ?>&status=diterima" class="btn btn-outline btn-sm" title="Terima"><i class="bi bi-check-lg"></i></a>
                    <a href="?tb=pengurus&id=<?php echo $r['id']; ?>&status=ditolak" class="btn btn-outline btn-sm" title="Tolak"><i class="bi bi-x-lg"></i></a>
                    <a href="?tb=pengurus&hapus=<?php echo $r['id']; ?>" onclick="return confirmAction('Hapus pendaftar ini?', this.href)" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    </div>
</div>

<a href="pendaftaran.php" class="btn btn-outline" style="margin-top:4px;"><i class="bi bi-arrow-clockwise"></i> Segarkan Data</a>

<script>
document.getElementById('adminPendaftaranSearch')?.addEventListener('input', function() {
    const term = this.value.toLowerCase().trim();
    document.querySelectorAll('.card table tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});
</script>

<?php include 'includes/admin-footer.php'; ?>