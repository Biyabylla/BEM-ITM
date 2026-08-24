<?php
require_once __DIR__ . '/includes/auth.php';
$admin_title = 'Aspirasi Masuk';

if (isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    if (in_array($status, ['baru','dibaca','ditindaklanjuti'])) {
        $stmt = mysqli_prepare($koneksi, "UPDATE aspirasi SET status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'si', $status, $id);
        mysqli_stmt_execute($stmt);
    }
    header('Location: aspirasi.php'); exit;
}
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = mysqli_prepare($koneksi, "DELETE FROM aspirasi WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    header('Location: aspirasi.php'); exit;
}

$filter = $_GET['jenis'] ?? 'all';
$jenis_valid = ['kritik','saran','pertanyaan'];
if ($filter !== 'all' && !in_array($filter, $jenis_valid)) $filter = 'all';
if ($filter !== 'all') {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM aspirasi WHERE jenis=? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, 's', $filter);
    mysqli_stmt_execute($stmt);
    $list = mysqli_stmt_get_result($stmt);
} else {
    $list = mysqli_query($koneksi, "SELECT * FROM aspirasi ORDER BY created_at DESC");
}

include 'includes/admin-header.php';
?>

<div class="page-head">
    <div>
        <h3><i class="bi bi-chat-dots"></i> Aspirasi Masuk</h3>
        <p>Kelola aspirasi dari mahasiswa (kritik, saran, pertanyaan).</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="?jenis=all" class="btn btn-sm <?php echo $filter=='all'?'btn-primary':'btn-outline'; ?>">Semua</a>
        <a href="?jenis=kritik" class="btn btn-sm <?php echo $filter=='kritik'?'btn-primary':'btn-outline'; ?>">Kritik</a>
        <a href="?jenis=saran" class="btn btn-sm <?php echo $filter=='saran'?'btn-primary':'btn-outline'; ?>">Saran</a>
        <a href="?jenis=pertanyaan" class="btn btn-sm <?php echo $filter=='pertanyaan'?'btn-primary':'btn-outline'; ?>">Pertanyaan</a>
    </div>
</div>

<div class="card" style="padding:24px;">
    <div class="table-responsive">
    <table>
        <thead><tr><th>Nama</th><th>Kontak</th><th>Jenis</th><th>Pesan</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($list)): ?>
        <tr>
            <td><?php echo esc($row['nama']); ?><?php if($row['nim']): ?><br><span style="color:var(--ink-soft);font-size:.75rem;">NIM: <?php echo esc($row['nim']); ?></span><?php endif; ?></td>
            <td><?php echo esc($row['email']); ?></td>
            <td><span class="badge badge-maroon"><?php echo esc(ucfirst($row['jenis'])); ?></span></td>
            <td style="max-width:260px;"><?php echo esc($row['pesan']); ?></td>
            <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
            <td>
                <select onchange="window.location='?jenis=<?php echo urlencode($filter); ?>&id=<?php echo $row['id']; ?>&status='+this.value" class="form-control" style="padding:6px 10px;font-size:.75rem;">
                    <option value="baru" <?php echo $row['status']=='baru'?'selected':''; ?>>Baru</option>
                    <option value="dibaca" <?php echo $row['status']=='dibaca'?'selected':''; ?>>Dibaca</option>
                    <option value="ditindaklanjuti" <?php echo $row['status']=='ditindaklanjuti'?'selected':''; ?>>Ditindaklanjuti</option>
                </select>
            </td>
            <td><a href="?hapus=<?php echo $row['id']; ?>" onclick="return confirmAction('Hapus aspirasi ini?', this.href)" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
