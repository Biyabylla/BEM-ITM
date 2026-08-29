<?php
require_once __DIR__ . '/includes/auth.php';
$admin_title = 'Kelola Admin';
$msg = '';
$err = '';

// Tambah admin baru
if (isset($_POST['tambah'])) {
    $username = sanitize($_POST['username'], 50);
    $nama = sanitize($_POST['nama_lengkap'], 100);
    $role = $_POST['role'] ?? 'admin_publikasi';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($nama) || empty($password)) {
        $err = 'Semua field wajib diisi.';
    } elseif (strlen($password) < 6) {
        $err = 'Password minimal 6 karakter.';
    } elseif (!isset(ROLES[$role])) {
        $err = 'Role tidak valid.';
    } else {
        // Cek username unik
        $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id FROM admin_users WHERE username='" . mysqli_real_escape_string($koneksi, $username) . "'"));
        if ($cek) {
            $err = 'Username sudah digunakan.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($koneksi, "INSERT INTO admin_users (username, password, nama_lengkap, role) VALUES (?,?,?,?)");
            mysqli_stmt_bind_param($stmt, 'ssss', $username, $hash, $nama, $role);
            mysqli_stmt_execute($stmt);
            $msg = 'Admin "' . esc($nama) . '" berhasil ditambahkan.';
        }
    }
}

// Edit admin
if (isset($_POST['edit'])) {
    $id = (int)($_POST['id'] ?? 0);
    $nama = sanitize($_POST['nama_lengkap'], 100);
    $role = $_POST['role'] ?? 'admin_publikasi';

    if ($id > 0 && isset(ROLES[$role])) {
        if ($id == $_SESSION['admin_id'] && $role !== 'super_admin') {
            $err = 'Anda tidak dapat mengubah role diri sendiri dari Super Admin.';
        } else {
            $stmt = mysqli_prepare($koneksi, "UPDATE admin_users SET nama_lengkap=?, role=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'ssi', $nama, $role, $id);
            mysqli_stmt_execute($stmt);

            // Refresh session jika edit diri sendiri
            if ($id == $_SESSION['admin_id']) {
                $_SESSION['admin_nama'] = $nama;
                $_SESSION['admin_role'] = $role;
            }

            $msg = 'Data admin berhasil diperbarui.';
        }
    }
}

// Hapus admin
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id == $_SESSION['admin_id']) {
        $err = 'Anda tidak dapat menghapus akun sendiri.';
    } else {
        mysqli_query($koneksi, "DELETE FROM admin_users WHERE id=$id");
        $msg = 'Admin berhasil dihapus.';
    }
    header('Location: kelola-admin.php');
    exit;
}

// Edit mode
$edit = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $edit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM admin_users WHERE id=$edit_id"));
}

$list = mysqli_query($koneksi, "SELECT * FROM admin_users ORDER BY role ASC, nama_lengkap ASC");

include 'includes/admin-header.php';
?>

<div class="page-head">
    <div>
        <h3><i class="bi bi-people-fill"></i> Kelola Admin</h3>
        <p>Tambah, edit, atau hapus akun admin beserta rolenya.</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1.4fr;gap:24px;align-items:start;">
    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);"><?php echo $edit ? 'Edit' : 'Tambah'; ?> Admin</h4>

        <?php if ($edit): ?>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo $edit['id']; ?>">
            <div class="form-group">
                <label>Username</label>
                <input type="text" class="form-control" value="<?php echo esc($edit['username']); ?>" disabled>
                <small style="color:var(--ink-soft);">Username tidak dapat diubah.</small>
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required value="<?php echo esc($edit['nama_lengkap']); ?>">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control">
                    <?php foreach (ROLES as $rk => $rv): ?>
                    <option value="<?php echo $rk; ?>" <?php echo ($edit['role'] == $rk) ? 'selected' : ''; ?>><?php echo esc($rv['label']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="edit" class="btn btn-primary" style="width:100%;"><i class="bi bi-check-lg"></i> Simpan</button>
            <a href="kelola-admin.php" class="btn btn-outline" style="width:100%;margin-top:8px;justify-content:center;">Batal Edit</a>
        </form>

        <?php else: ?>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required placeholder="untuk login" maxlength="50">
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required placeholder="nama tampil" maxlength="100">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="minimal 6 karakter">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control">
                    <?php foreach (ROLES as $rk => $rv): ?>
                    <option value="<?php echo $rk; ?>"><?php echo esc($rv['label']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="tambah" class="btn btn-primary" style="width:100%;"><i class="bi bi-check-lg"></i> Tambah Admin</button>
        </form>
        <?php endif; ?>

        <div style="margin-top:20px;padding:18px;background:var(--cream);border:1px solid var(--line);border-radius:10px;">
            <h5 style="margin:0 0 14px;font-size:.85rem;color:var(--maroon-900);"><i class="bi bi-info-circle"></i> Keterangan Role</h5>
            <?php foreach (ROLES as $rk => $rv): ?>
            <div style="margin-bottom:12px;padding-bottom:12px;<?php echo $rk !== array_key_last(ROLES) ? 'border-bottom:1px dashed var(--line);' : ''; ?>">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <?php
                    $badge_cls = $rk === 'super_admin' ? 'badge-maroon' : 'badge-gold';
                    ?>
                    <span class="badge <?php echo $badge_cls; ?>" style="font-size:.72rem;"><?php echo esc($rv['label']); ?></span>
                </div>
                <div style="font-size:.8rem;color:var(--ink-soft);line-height:1.5;"><?php echo esc($rv['deskripsi']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card" style="padding:24px;">
        <h4 style="margin:0 0 16px;color:var(--maroon-900);">Daftar Admin</h4>
        <div class="table-responsive">
        <table>
            <thead><tr><th>Username</th><th>Nama</th><th>Role</th><th>Dibuat</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($list)): ?>
            <tr>
                <td data-label="Username"><code style="background:var(--cream);padding:3px 8px;border-radius:4px;font-size:.8rem;"><?php echo esc($row['username']); ?></code></td>
                <td data-label="Nama"><?php echo esc($row['nama_lengkap']); ?></td>
                <td data-label="Role">
                    <?php
                    $role_label = ROLES[$row['role']]['label'] ?? $row['role'];
                    $badge_cls = $row['role'] === 'super_admin' ? 'badge-maroon' : 'badge-gold';
                    ?>
                    <span class="badge <?php echo $badge_cls; ?>"><?php echo esc($role_label); ?></span>
                </td>
                <td data-label="Dibuat" style="font-size:.8rem;color:var(--ink-soft);"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                <td data-label="Aksi" class="action-cell">
                    <a href="?edit_id=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i></a>
                    <?php if ($row['id'] != $_SESSION['admin_id']): ?>
                    <a href="?hapus=<?php echo $row['id']; ?>" onclick="return confirmAction('Hapus admin ini?', this.href)" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
